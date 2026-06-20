<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use shurjopayv2\ShurjopayLaravelPackage8\Http\Controllers\ShurjopayController;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\DigitalDownload; // ⭐ নতুন
use Session;
use Toastr;
use Cart;
use Illuminate\Support\Str;

class ShurjopayControllers extends Controller
{
    public function payment_success(Request $request)
    {
        $order_id = $request->order_id;

        $shurjopay_service = new ShurjopayController();
        $json = $shurjopay_service->verify($order_id);
        $data = json_decode($json);

        // ✅ পেমেন্ট ভেরিফাই ফেইল হলে
        if (!is_array($data) || !isset($data[0]) || $data[0]->sp_code != 1000) {
            Toastr::error('Failed', 'Your order place failed');
            return redirect()->route('customer.checkout');
        }

        /**
         * CustomerController::order_save এ তুমি value1 এ order_id পাঠিয়েছ:
         * 'value1' => $order->id
         *
         * অনেক ইমপ্লিমেন্টেশনে শুরজোপে verify() রেসপন্সে order_id/value1 এ আমাদের অর্ডার ফেরত দেয়।
         * তোমার পুরানো কোডে $data[0]->id ব্যবহার করছিল, কিন্তু বেশিরভাগ ক্ষেত্রে
         * $data[0]->value1 ই order_id হিসেবে থাকে। যদি আগেরটা কাজ করত, চাইলে সেটা রাখার সুযোগও আছে।
         */

        // যদি লাইব্রেরি value1 এ order_id দেয়:
        $orderIdFromGateway = $data[0]->value1 ?? $data[0]->id;

        $order = Order::where('id', $orderIdFromGateway)->firstOrFail();
        $order->order_status = 2;      // accepted/processing ইত্যাদি
        $order->payment_status = 'paid';
        $order->save();
        
        $payment = Payment::where(['order_id' => $order->id])->first();
        if ($payment) {
            $payment->payment_method = $data[0]->method ?? $payment->payment_method;
            $payment->trx_id         = $data[0]->bank_trx_id ?? $payment->trx_id;
            $payment->sender_number  = $data[0]->phone_no ?? $payment->sender_number;
            $payment->payment_status = $data[0]->bank_status ?? 'paid';
            $payment->save();
        }

        // ⭐ সফল পেমেন্টের পর ডিজিটাল ডাউনলোড তৈরি
        $this->createDigitalDownloads($order);

        Cart::instance('shopping')->destroy(); 

        Toastr::success('Thanks, Your order place successfully', 'Success!');
        return redirect('customer/order-success/'.$order->id);
    }

    public function payment_cancel(Request $request)
    {
        Toastr::error('Your payment cancelled', 'Cancelled!');
        return redirect()->route('home');
    }

    // =====================================
    // ⭐ DIGITAL DOWNLOAD CREATOR (HELPER)
    // =====================================
    private function createDigitalDownloads(Order $order)
    {
        // অর্ডারের ডিটেইল + প্রোডাক্ট রিলেশন লোড
        $order->loadMissing('orderdetails.product');

        foreach ($order->orderdetails as $item) {
            $product = $item->product;

            // প্রোডাক্ট ডিজিটাল কিনা চেক
            if ($product && $product->is_digital == 1 && $product->digital_file) {

                // একই order + product + customer এর জন্য পুনরায় যেন না তৈরি হয়
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
