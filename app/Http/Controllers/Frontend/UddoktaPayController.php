<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Shipping;
use App\Models\Payment;
use App\Models\DigitalDownload; // ⭐ ডিজিটাল ডাউনলোড মডেল
use UddoktaPay\LaravelSDK\UddoktaPay;
use UddoktaPay\LaravelSDK\Requests\CheckoutRequest;
use UddoktaPay\LaravelSDK\Exceptions\UddoktaPayException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UddoktaPayController extends Controller
{
    /**
     * Redirect user to UddoktaPay checkout page
     */
    public function checkout(Request $request, $order_id = null)
    {
        // 🔹 order_id দুইভাবে নেব — route param বা request থেকে
        $orderId = $order_id ?? $request->order_id;

        if(!$orderId){
            return redirect()->back()->with('error', 'Order ID missing for UddoktaPay.');
        }

        $order = Order::findOrFail($orderId);
        $shipping = Shipping::where('order_id', $order->id)->first();

        $fullName = $shipping->name ?? 'Customer';
        $email    = 'customer@example.com';

        // 🔥 Payment টেবিল থেকে amount (advance থাকলে advance, নইলে full)
        $payment = Payment::where('order_id', $order->id)->first();

        if ($payment && $payment->amount > 0) {
            $amount = (float) $payment->amount;    // অগ্রিম
        } else {
            $amount = (float) $order->amount;      // fallback: পুরো অর্ডার
        }

        Log::info("UddoktaPay checkout for order {$order->id} with amount={$amount}");

        // উদ্যোক্তা পে ইনিশিয়ালাইজ
        $uddoktapay = UddoktaPay::make(env('UDDOKTAPAY_API_KEY'), env('UDDOKTAPAY_API_URL'));

        try {
            $checkoutRequest = CheckoutRequest::make()
                ->setFullName($fullName)
                ->setEmail($email)
                ->setAmount($amount)
                ->addMetadata('order_id', $order->id)
                ->setRedirectUrl(route('uddoktapay.verify'))
                ->setCancelUrl(route('uddoktapay.cancel'))
                ->setWebhookUrl(route('uddoktapay.ipn'));

            $response = $uddoktapay->checkout($checkoutRequest);

            if ($response->failed()) {
                Log::error('UddoktaPay Checkout Failed: ' . $response->message());
                return redirect()->back()->with('error', 'Payment initiation failed.');
            }

            // ✅ gateway info আপডেট
            $order->payment_gateway = 'uddoktapay';
            $order->payment_status  = 'pending';
            $order->save();

            return redirect($response->paymentURL());

        } catch (UddoktaPayException $e) {
            Log::error('UddoktaPay Checkout Error: ' . $e->getMessage());
            return redirect()->back()->with('error', "Payment Error: " . $e->getMessage());
        }
    }

    /**
     * Payment verification after returning from UddoktaPay
     */
    public function verify(Request $request)
    {
        $uddoktapay = UddoktaPay::make(env('UDDOKTAPAY_API_KEY'), env('UDDOKTAPAY_API_URL'));

        try {
            $response = $uddoktapay->verify($request);
            $order_id = $response->metadata('order_id');
            $order    = Order::where('id', $order_id)->first();

            if (!$order) {
                return redirect()->route('customer.account')->with('error', 'Order not found.');
            }

            // ✅ পেমেন্ট সফল
            if ($response->success()) {
                $order->payment_status  = 'paid';
                $order->payment_gateway = 'uddoktapay';
                $order->save();

                // ⭐ সফল পেমেন্টের পরে ডিজিটাল ডাউনলোড তৈরি
                $this->createDigitalDownloads($order);

                return redirect()->route('customer.order_success', $order->id)
                                 ->with('success', 'Payment Successful! Your digital downloads are ready.');
            } else {
                // ❌ ব্যর্থ পেমেন্ট
                $order->payment_status  = 'failed';
                $order->payment_gateway = 'uddoktapay';
                $order->save();

                return redirect()->route('customer.order_success', $order->id)
                                 ->with('error', 'Payment Failed!');
            }

        } catch (UddoktaPayException $e) {
            Log::error('UddoktaPay Verify Error: ' . $e->getMessage());
            return redirect()->route('customer.account')->with('error', "Verification Error: " . $e->getMessage());
        }
    }

    /**
     * If customer cancels payment
     */
    public function cancel()
    {
        return redirect()->route('customer.account')
                         ->with('error', 'Payment cancelled by user.');
    }

    /**
     * IPN (Instant Payment Notification)
     * Called automatically from UddoktaPay server after payment.
     */
    public function ipn(Request $request)
    {
        $uddoktapay = UddoktaPay::make(env('UDDOKTAPAY_API_KEY'), env('UDDOKTAPAY_API_URL'));
        $response   = $uddoktapay->ipn($request);

        if ($response->success()) {
            $order_id = $response->metadata('order_id');
            $order    = Order::where('id', $order_id)->first();

            if ($order && $order->payment_status != 'paid') {
                $order->payment_status  = 'paid';
                $order->payment_gateway = 'uddoktapay';
                $order->save();

                // ⭐ IPN দিয়েও যদি প্রথমবার paid হয় → তখনও ডিজিটাল ডাউনলোড তৈরি
                $this->createDigitalDownloads($order);

                Log::info("Order #{$order_id} marked as paid via IPN and digital downloads created.");
            }
        } else {
            Log::warning('UddoktaPay IPN Failed or Invalid');
        }
    }

    // =====================================
    // ⭐ DIGITAL DOWNLOAD CREATOR (HELPER)
    // =====================================
    private function createDigitalDownloads(Order $order)
    {
        // orderdetails + product রিলেশন লোড করি
        $order->loadMissing('orderdetails.product');

        foreach ($order->orderdetails as $item) {
            $product = $item->product;

            if ($product && $product->is_digital == 1 && $product->digital_file) {

                DigitalDownload::firstOrCreate(
                    [
                        'order_id'    => $order->id,
                        'product_id'  => $product->id,
                        'customer_id' => $order->customer_id,
                    ],
                    [
                        'token'               => Str::uuid(),
                        'file_path'           => $product->digital_file,
                        'remaining_downloads' => $product->download_limit ?? 5,
                        'expires_at'          => $product->download_expire_days
                                                    ? now()->addDays($product->download_expire_days)
                                                    : null,
                    ]
                );
            }
        }
    }
}
