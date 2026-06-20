<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Util\BkashCredential;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\DigitalDownload; // ⭐ নতুন
use App\Models\OrderDetails;    // helper এ লাগবে
use Session;
use Toastr;
use Cart;
use Illuminate\Support\Str;

class BkashController extends Controller
{
    private $base_url;
    private $app_key;
    private $app_secret;
    private $username;
    private $password;

    public function __construct()
    {
        $bkash_gateway = PaymentGateway::where(['status'=> 1, 'type'=>'bkash'])->first();
        if($bkash_gateway) {
            $this->base_url  = $bkash_gateway->base_url;
            $this->app_key   = $bkash_gateway->app_key;
            $this->app_secret= $bkash_gateway->app_secret;
            $this->username  = $bkash_gateway->username;
            $this->password  = $bkash_gateway->password;
        } else {
            $this->base_url  = 'https://tokenized.pay.bka.sh/v1.2.0-beta';
            $this->app_key   = '';
            $this->app_secret= '';
            $this->username  = '';
            $this->password  = '';
        }
    }

    public function authHeaders(){
        return [
            'Content-Type:application/json',
            'Authorization:' . $this->grant(),
            'X-APP-Key:'.$this->app_key
        ];
    }
         
    public function curlWithBody($url,$header,$method,$body_data_json){
        $curl = curl_init($this->base_url.$url);
        curl_setopt($curl,CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_POSTFIELDS, $body_data_json);
        curl_setopt($curl,CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function grant()
    {
        $header = [
            'Content-Type:application/json',
            'username:'.$this->username,
            'password:'.$this->password
        ];

        $body_data = ['app_key'=> $this->app_key, 'app_secret'=>$this->app_secret];
        $body_data_json = json_encode($body_data);

        $response = $this->curlWithBody('/tokenized/checkout/token/grant',$header,'POST',$body_data_json);
        $token = json_decode($response)->id_token;

        return $token;
    }

    // 🔥 অর্ডার অনুযায়ী amount নিয়ে create
    public function create(Request $request)
    {     
        // অর্ডার আনলাম
        $order = Order::where('id',$request->order_id)->firstOrFail();

        // Payment থেকে অংক নেবো (advance থাকলে সেটা, নইলে ফুল)
        $payment = Payment::where('order_id', $order->id)->first();

        if ($payment && $payment->amount > 0) {
            $amount = $payment->amount;
        } else {
            $amount = $order->amount;
        }

        $orderId = $order->id;

        $header = $this->authHeaders();

        $baseUrl = rtrim(env('APP_URL'), '/');
        $callbackURL = $baseUrl . '/bkash/checkout-url/callback?orderId=' . $orderId;

        $body_data = [
            'mode'                  => '0011',
            'payerReference'        => ' ',
            'callbackURL'           => $callbackURL,
            'amount'                => $amount,
            'currency'              => 'BDT',
            'intent'                => 'sale',
            'merchantInvoiceNumber' => "Inv" . Str::random(10),
        ];

        $body_data_json = json_encode($body_data);

        $response = $this->curlWithBody('/tokenized/checkout/create',$header,'POST',$body_data_json);
        $res = json_decode($response);

        if (!isset($res->paymentID) || !isset($res->bkashURL)) {
            Toastr::error('bKash payment create failed', 'Error!');
            return redirect()->back();
        }

        Session::forget('paymentID');
        Session::put('paymentID', $res->paymentID);

        return redirect($res->bkashURL);
    }

    public function execute($paymentID)
    {
        $header =$this->authHeaders();

        $body_data = [
            'paymentID' => $paymentID
        ];
        $body_data_json=json_encode($body_data);

        $response = $this->curlWithBody('/tokenized/checkout/execute',$header,'POST',$body_data_json);
        
        return $response;
    }

    public function query($paymentID)
    {
        $header =$this->authHeaders();

        $body_data = [
            'paymentID' => $paymentID,
        ];
        $body_data_json=json_encode($body_data);

        $response = $this->curlWithBody('/tokenized/checkout/payment/status',$header,'POST',$body_data_json);
        return $response;
    }

    public function callback(Request $request)
    {
        $allRequest = $request->all();
        
        if(isset($allRequest['status']) && $allRequest['status'] == 'failure'){
            Toastr::error('Opps, Your bkash payment failed', 'Failed!');
            return redirect('customer/order-success/'.$allRequest['orderId']);

        }else if(isset($allRequest['status']) && $allRequest['status'] == 'cancel'){
            Toastr::error('Opps, Your bkash payment cancelld', 'Cancelled!');
            return redirect('customer/order-success/'.$allRequest['orderId']);

        }else{
            
            $response = $this->execute($allRequest['paymentID']);
            $arr = json_decode($response,true);

            if(array_key_exists("statusCode",$arr) && $arr['statusCode'] != '0000'){
                Toastr::error('Opps, Your bkash payment failed', 'Failed!');
                return redirect('customer/order-success/'.$allRequest['orderId']);

            }else if(array_key_exists("message",$arr)){
                sleep(1);
                $queryResponse = $this->query($allRequest['paymentID']);
                return redirect('customer/order-success/'.$allRequest['orderId']);
            }
            
            $queryResponse = $this->query($allRequest['paymentID']);
            
            // ✅ Order update
            $order = Order::where('id',$allRequest['orderId'])->first();
            $order->status = 'accepted';
            $order->payment_status = 'paid'; // চাইলে যোগ করা ভালো
            $order->save();
            
            // ✅ Payment update
            $payment = Payment::where('order_id',$allRequest['orderId'])->first();
            $payment->trx_id = $allRequest['paymentID'];
            $payment->payment_status = 'paid';
            $payment->save();

            // ⭐ সফল পেমেন্টের পরে ডিজিটাল ডাউনলোড তৈরি
            $this->createDigitalDownloads($order);
            
            Toastr::success('Thanks, Your bkash payment successfully done', 'Success!');
            return redirect('customer/order-success/'.$allRequest['orderId']);
        }
    }
 
    public function getRefund(Request $request)
    {
        return view('CheckoutURL.refund');
    }

    public function refund(Request $request)
    {
        $header =$this->authHeaders();

        $body_data = [
            'paymentID' => $request->paymentID,
            'amount' =>  $request->amount,
            'trxID' => $request->trxID,
            'sku' => 'sku',
            'reason' => 'Quality issue'
        ];
     
        $body_data_json=json_encode($body_data);

        $response = $this->curlWithBody('/tokenized/checkout/payment/refund',$header,'POST',$body_data_json);

        return view('CheckoutURL.refund')->with([
            'response' => $response,
        ]);
    }
    
    public function getRefundStatus(Request $request)
    {
        return view('CheckoutURL.refund-status');
    }

    public function refundStatus(Request $request)
    {     
        Session::forget('bkash_token');  
        $token = $this->grant();
        Session::put('bkash_token', $token);

        $header =$this->authHeaders();

        $body_data = [
            'paymentID' => $request->paymentID,
            'trxID' => $request->trxID,
        ];
        $body_data_json = json_encode($body_data);

        $response = $this->curlWithBody('/tokenized/checkout/payment/refund',$header,'POST',$body_data_json);
                
        return view('CheckoutURL.refund-status')->with([
            'response' => $response,
        ]);
    }

    // =====================================
    // ⭐ DIGITAL DOWNLOAD CREATOR (HELPER)
    // =====================================
    private function createDigitalDownloads(Order $order)
    {
        // অর্ডারের ডিটেইল + প্রোডাক্ট নিয়ে আসি
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
