<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\District;
use App\Models\OrderStatus;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Shipping;
use App\Models\ShippingCharge;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Courierapi;
use App\Models\SmsGateway;
use App\Models\GeneralSetting;
use App\Models\Color;
use App\Models\Size;
use App\Models\FundTransaction;
use App\Helpers\FundHelper;
use App\Models\Expense;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

use Session;
use Cart;
use Toastr;
use Mail;

class OrderController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | COMMON STOCK HANDLER
    |--------------------------------------------------------------------------
    |
    | activeStatuses = 1,2,3,5,6,8  => স্টক মাইনাস
    | newStatus = 11 এবং oldStatus active হলে => স্টক প্লাস
    |
    */
    protected function handleStockChange(Order $order, int $oldStatus, int $newStatus)
    {
        $activeStatuses = [1, 2, 3, 5, 6, 8];

        // 1) প্রথমবার active status এ ঢুকলে স্টক কমবে
        if (in_array($newStatus, $activeStatuses) && !in_array($oldStatus, $activeStatuses)) {
            $details = OrderDetails::where('order_id', $order->id)->get();

            foreach ($details as $row) {
                $product = Product::find($row->product_id);
                if ($product) {
                    $product->stock = max(0, $product->stock - $row->qty);
                    $product->save();
                }
            }
        }

        // 2) cancel (11) হলে, যদি আগেরটা active group এ থাকে -> স্টক রিস্টোর
        if ($newStatus == 11 && in_array($oldStatus, $activeStatuses)) {
            $details = OrderDetails::where('order_id', $order->id)->get();

            foreach ($details as $row) {
                $product = Product::find($row->product_id);
                if ($product) {
                    $product->stock = $product->stock + $row->qty;
                    $product->save();
                }
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | FRAUD CHECK PART
    |--------------------------------------------------------------------------
    */

    public function fraudCheck(Request $request)
    {
        $mobile = $request->input('mobile');
        if (!$mobile) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Mobile number is required'
            ]);
        }

        $generalSetting = GeneralSetting::where('status', 1)->first();
        $apiKey    = $generalSetting->fraud_api_key ?? null;
        $secretKey = $generalSetting->fraud_secret_key ?? null;

        if (!$apiKey || !$secretKey) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Fraud API credentials not set in settings'
            ]);
        }

        try {
            $response = Http::withHeaders([
                'api-key'           => $apiKey,
                'secret-key'        => $secretKey,
                'x-customer-domain' => $request->getHost(),
            ])->asForm()
                ->post('https://zachaikori.com/api/v1/fraud-check', [
                    'mobile' => $mobile,
                ]);

            $res = $response->json();

            if (isset($res['status']) && $res['status'] === 'success') {
                $data = $res['data'];

                $order = Order::whereHas('shipping', function ($q) use ($mobile) {
                    $q->where('phone', $mobile);
                })->first();

                if ($order) {
                    $order->fraud_success     = $data['fraud']['success'] ?? 0;
                    $order->fraud_cancel      = $data['fraud']['cancel'] ?? 0;
                    $order->fraud_rate        = $data['fraud']['rate'] ?? 0;

                    $order->pathao_success    = $data['pathao']['success'] ?? 0;
                    $order->pathao_cancel     = $data['pathao']['cancel'] ?? 0;
                    $order->pathao_rate       = $data['pathao']['rate'] ?? 0;

                    $order->redx_success      = $data['redx']['success'] ?? 0;
                    $order->redx_cancel       = $data['redx']['cancel'] ?? 0;
                    $order->redx_rate         = $data['redx']['rate'] ?? 0;

                    $order->steadfast_success = $data['steadfast']['success'] ?? 0;
                    $order->steadfast_cancel  = $data['steadfast']['cancel'] ?? 0;
                    $order->steadfast_rate    = $data['steadfast']['rate'] ?? 0;

                    $order->save();
                }

                return response()->json([
                    'status' => 'success',
                    'data'   => $data
                ]);
            } else {
                return response()->json([
                    'status'  => 'failed',
                    'message' => $res['message'] ?? 'Fraud check failed'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function manualFraudCheckPage()
    {
        return view('backEnd.fraud.manual_check');
    }

    public function manualFraudCheck(Request $request)
    {
        $mobile = $request->input('mobile');

        if (!$mobile) {
            return back()->with('error', 'দয়া করে একটি মোবাইল নাম্বার লিখুন');
        }

        $generalSetting = GeneralSetting::where('status', 1)->first();
        $apiKey    = $generalSetting->fraud_api_key ?? null;
        $secretKey = $generalSetting->fraud_secret_key ?? null;

        if (!$apiKey || !$secretKey) {
            return back()->with('error', 'Fraud API credentials সেট করা হয়নি');
        }

        try {
            $response = Http::withHeaders([
                'api-key'           => $apiKey,
                'secret-key'        => $secretKey,
                'x-customer-domain' => $request->getHost(),
            ])->asForm()->post('https://zachaikori.com/api/v1/fraud-check', [
                'mobile' => $mobile,
            ]);

            $res = $response->json();

            if (isset($res['status']) && $res['status'] === 'success') {
                $data = $res['data'];
                return view('backEnd.fraud.manual_check', compact('mobile', 'data'));
            } else {
                return back()->with('error', $res['message'] ?? 'Fraud check ব্যর্থ হয়েছে');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'API Error: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ORDER LIST
    |--------------------------------------------------------------------------
    */

    public function index($slug, Request $request)
    {
        if ($slug == 'all') {
            $order_status = (object) [
                'name'         => 'All',
                'orders_count' => Order::count(),
            ];

            $show_data = Order::latest()->with('shipping', 'status');

            if ($request->keyword) {
                $show_data = $show_data->where(function ($query) use ($request) {
                    $query->orWhere('invoice_id', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhereHas('shipping', function ($subQuery) use ($request) {
                            $subQuery->where('phone', $request->keyword);
                        });
                });
            }
            $show_data = $show_data->paginate(10);
        } else {
            $order_status = OrderStatus::where('slug', $slug)->withCount('orders')->first();
            $show_data = Order::where(['order_status' => $order_status->id])
                ->latest()
                ->with('shipping', 'status')
                ->paginate(10);
        }

        $users       = User::get();
        $steadfast   = Courierapi::where(['status' => 1, 'type' => 'steadfast'])->first();
        $pathao_info = Courierapi::where(['status' => 1, 'type' => 'pathao'])
            ->select('id', 'type', 'url', 'token', 'status')
            ->first();

        if ($pathao_info) {
            $response     = Http::get($pathao_info->url . '/api/v1/countries/1/city-list');
            $pathaocities = $response->json();

            $response2 = Http::withHeaders([
                'Authorization' => 'Bearer ' . $pathao_info->token,
                'Content-Type'  => 'application/json',
            ])->get($pathao_info->url . '/api/v1/stores');
            $pathaostore = $response2->json();
        } else {
            $pathaocities = [];
            $pathaostore  = [];
        }

        return view('backEnd.order.index', compact('show_data', 'order_status', 'users', 'steadfast', 'pathaostore', 'pathaocities'));
    }

    public function pathaocity(Request $request)
    {
        $pathao_info = Courierapi::where(['status' => 1, 'type' => 'pathao'])
            ->select('id', 'type', 'url', 'token', 'status')->first();

        if ($pathao_info) {
            $response    = Http::get($pathao_info->url . '/api/v1/cities/' . $request->city_id . '/zone-list');
            $pathaozones = $response->json();
            return response()->json($pathaozones);
        } else {
            return response()->json([]);
        }
    }

    public function pathaozone(Request $request)
    {
        $pathao_info = Courierapi::where(['status' => 1, 'type' => 'pathao'])
            ->select('id', 'type', 'url', 'token', 'status')->first();

        if ($pathao_info) {
            $response   = Http::get($pathao_info->url . '/api/v1/zones/' . $request->zone_id . '/area-list');
            $pathaoareas = $response->json();
            return response()->json($pathaoareas);
        } else {
            return response()->json([]);
        }
    }

    public function order_pathao(Request $request)
    {
        $orders_id = $request->order_ids;

        foreach ($orders_id as $order_id) {
            $order = Order::with('shipping')->find($order_id);

            $pathao_info = Courierapi::where(['status' => 1, 'type' => 'pathao'])
                ->select('id', 'type', 'url', 'token', 'status')->first();

            if ($pathao_info) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $pathao_info->token,
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                ])->post($pathao_info->url . '/api/v1/orders', [
                    'store_id'           => $request->pathaostore,
                    'merchant_order_id'  => $order->invoice_id,
                    'sender_name'        => 'Test',
                    'sender_phone'       => $order->shipping ? $order->shipping->phone : '',
                    'recipient_name'     => $order->shipping ? $order->shipping->name : '',
                    'recipient_phone'    => $order->shipping ? $order->shipping->phone : '',
                    'recipient_address'  => $order->shipping ? $order->shipping->address : '',
                    'recipient_city'     => $request->pathaocity,
                    'recipient_zone'     => $request->pathaozone,
                    'recipient_area'     => $request->pathaoarea,
                    'delivery_type'      => 48,
                    'item_type'          => 2,
                    'special_instruction'=> 'Special note- product must be check after delivery',
                    'item_quantity'      => 1,
                    'item_weight'        => 0.5,
                    'amount_to_collect'  => round($order->amount),
                    'item_description'   => 'Special note- product must be check after delivery',
                ]);
            }

            if ($response->status() == '200') {
                Toastr::success($response['data']['consignment_id'], 'Courier Tracking ID');
                return response()->json([
                    'status'  => 'success',
                    'message' => $response['data']['consignment_id'],
                    'Courier Tracking ID'
                ]);
            } else {
                Toastr::error($response['message'], 'Courier Order Faild');
                return response()->json([
                    'status'  => 'failed',
                    'message' => $response['message'],
                    'Courier Order Faild'
                ]);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | INVOICE / PROCESS
    |--------------------------------------------------------------------------
    */

    public function invoice($invoice_id)
    {
        $order = Order::where(['invoice_id' => $invoice_id])
            ->with('orderdetails', 'payment', 'shipping', 'customer')
            ->firstOrFail();

        return view('backEnd.order.invoice', compact('order'));
    }

    public function process($invoice_id)
    {
        $data = Order::where(['invoice_id' => $invoice_id])
            ->select('id', 'invoice_id', 'order_status')
            ->with('orderdetails')
            ->first();

        $shippingcharge = ShippingCharge::where('status', 1)->get();

        return view('backEnd.order.process', compact('data', 'shippingcharge'));
    }

    public function order_process(Request $request)
    {
        $link = OrderStatus::find($request->status)->slug;

        $order     = Order::find($request->id);
        $oldStatus = (int) $order->order_status;
        $newStatus = (int) $request->status;

        $order->order_status = $newStatus;
        $order->admin_note   = $request->admin_note;

        if ($newStatus == 6 && $oldStatus != 6) {
            FundTransaction::create([
                'direction'  => 'in',
                'source'     => 'sale',
                'source_id'  => $order->id,
                'amount'     => $order->amount,
                'note'       => 'Order complete (#' . $order->invoice_id . ') via process page',
                'created_by' => auth()->id(),
            ]);
        }

        $order->save();

        // 🔥 এখানে থেকে স্টক হ্যান্ডেল করছি
        $this->handleStockChange($order, $oldStatus, $newStatus);

        $shipping_update = Shipping::where('order_id', $order->id)->first();
        $shippingfee     = ShippingCharge::find($request->area);

        if ($shippingfee->name != $request->area) {
            $total                = $order->amount + ($shippingfee->amount - $order->shipping_charge);
            $order->shipping_charge = $shippingfee->amount;
            $order->amount          = $total;
            $order->save();
        }

        $shipping_update->name    = $request->name;
        $shipping_update->phone   = $request->phone;
        $shipping_update->address = $request->address;
        $shipping_update->area    = $shippingfee->name;
        $shipping_update->save();

        if ($newStatus == 5 && $oldStatus != 5) {
            $courier_info = Courierapi::where(['status' => 1, 'type' => 'steadfast'])->first();
            if ($courier_info) {
                $consignmentData = [
                    'invoice'          => $order->invoice_id,
                    'recipient_name'   => $order->shipping ? $order->shipping->name : 'InboxHat',
                    'recipient_phone'  => $order->shipping ? $order->shipping->phone : '01750578495',
                    'recipient_address'=> $order->shipping ? $order->shipping->address : '01750578495',
                    'cod_amount'       => $order->amount
                ];
                $client   = new Client();
                $response = $client->post($courier_info->url, [
                    'json'    => $consignmentData,
                    'headers' => [
                        'Api-Key'    => $courier_info->api_key,
                        'Secret-Key' => $courier_info->secret_key,
                        'Accept'     => 'application/json',
                    ],
                ]);

                $responseData = json_decode($response->getBody(), true);
            } else {
                return "ok";
            }

            Toastr::success('Success', 'Order status change successfully');
            return redirect('admin/order/' . $link);
        }

        Toastr::success('Success', 'Order status change successfully');
        return redirect('admin/order/' . $link);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE / BULK DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(Request $request)
    {
        Order::where('id', $request->id)->delete();
        OrderDetails::where('order_id', $request->id)->delete();
        Shipping::where('order_id', $request->id)->delete();
        Payment::where('order_id', $request->id)->delete();

        Toastr::success('Success', 'Order delete success successfully');
        return redirect()->back();
    }

    public function bulk_destroy(Request $request)
    {
        $orders_id = $request->order_ids;
        foreach ($orders_id as $order_id) {
            Order::where('id', $order_id)->delete();
            OrderDetails::where('order_id', $order_id)->delete();
            Shipping::where('order_id', $order_id)->delete();
            Payment::where('order_id', $order_id)->delete();
        }
        return response()->json(['status' => 'success', 'message' => 'Order delete successfully']);
    }

    /*
    |--------------------------------------------------------------------------
    | ASSIGN / BULK COURIER / PRINT
    |--------------------------------------------------------------------------
    */

    public function order_assign(Request $request)
    {
        Order::whereIn('id', $request->input('order_ids'))
            ->update(['user_id' => $request->user_id]);

        return response()->json(['status' => 'success', 'message' => 'Order user id assign']);
    }

    // ✅ Bulk status change + stock handle
    public function order_status(Request $request)
    {
        $sms_gateway  = SmsGateway::where('status', 1)->first();
        $site_setting = GeneralSetting::where('status', 1)->first();

        $targetStatus = (int) $request->order_status;

        $orders = Order::whereIn('id', $request->input('order_ids'))->get();

        foreach ($orders as $order) {

            $oldStatus = (int) $order->order_status;

            $order->order_status = $targetStatus;
            $order->update();

            $orderStatus = OrderStatus::find($targetStatus);

            if ($targetStatus == 6 && $oldStatus != 6) {
                FundTransaction::create([
                    'direction'  => 'in',
                    'source'     => 'sale',
                    'source_id'  => $order->id,
                    'amount'     => $order->amount,
                    'note'       => 'Order complete (#' . $order->invoice_id . ')',
                    'created_by' => auth()->id(),
                ]);
            }

            // 🔥 এখানে থেকে কমন ফাংশনে স্টক হ্যান্ডেল
            $this->handleStockChange($order, $oldStatus, $targetStatus);

            if ($sms_gateway) {
                $customer_info = Customer::find($order->customer_id);
                if ($customer_info) {
                    $url  = $sms_gateway->url;
                    $data = [
                        "api_key"  => $sms_gateway->api_key,
                        "number"   => $customer_info->phone,
                        "type"     => 'text',
                        "senderid" => $sms_gateway->serderid,
                        "message"  => "Dear {$customer_info->name},\r\n"
                            . "Your order (Order ID: {$order->invoice_id}) status has been updated to: "
                            . "{$orderStatus->name}.\r\n"
                            . "Thank you for using {$site_setting->name}!",
                    ];

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_exec($ch);
                    curl_close($ch);
                }
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Order status change successfully'
        ]);
    }

    public function order_print(Request $request)
    {
        $orders = Order::whereIn('id', $request->input('order_ids'))
            ->with('orderdetails', 'payment', 'shipping', 'customer')
            ->get();

        $view = view('backEnd.order.print', ['orders' => $orders])->render();
        return response()->json(['status' => 'success', 'view' => $view]);
    }

    public function bulk_courier($slug, Request $request)
    {
        $courier_info = Courierapi::where(['status' => 1, 'type' => $slug])->first();

        if (!$courier_info) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Courier information not found.'
            ]);
        }

        $orders_ids = $request->order_ids ?? [];
        if (empty($orders_ids)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No orders selected.'
            ]);
        }

        $successOrders = [];
        $failedOrders  = [];

        foreach ($orders_ids as $order_id) {
            $order = Order::with('shipping')->find($order_id);
            if (!$order) continue;

            try {
                $data = [
                    'invoice'          => $order->invoice_id,
                    'recipient_name'   => $order->shipping->name ?? 'Unknown',
                    'recipient_phone'  => $order->shipping->phone ?? '00000000000',
                    'recipient_address'=> $order->shipping->address ?? 'No address',
                    'cod_amount'       => $order->amount,
                ];

                $client   = new \GuzzleHttp\Client();
                $response = $client->post($courier_info->url, [
                    'json'    => $data,
                    'headers' => [
                        'Api-Key'    => $courier_info->api_key,
                        'Secret-Key' => $courier_info->secret_key,
                        'Accept'     => 'application/json',
                    ],
                ]);

                $res = json_decode($response->getBody(), true);

                \Log::info('Courier Response:', $res);

                $consignment_id =
                    $res['consignment']['consignment_id']
                    ?? $res['data']['consignment_id']
                    ?? $res['consignment_id']
                    ?? null;

                if ($consignment_id) {
                    $order->consignment_id = $consignment_id;
                    $order->order_status   = 5;
                    $order->save();

                    $successOrders[] = [
                        'order_id'       => $order_id,
                        'consignment_id' => $consignment_id,
                        'message'        => $res['message'] ?? 'Order placed successfully',
                    ];
                } else {
                    $failedOrders[] = [
                        'order_id' => $order_id,
                        'message'  => 'No consignment_id found in response',
                        'response' => $res,
                    ];
                }
            } catch (\Exception $e) {
                $failedOrders[] = [
                    'order_id' => $order_id,
                    'message'  => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Courier processed successfully',
            'success' => $successOrders,
            'failed'  => $failedOrders
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STOCK REPORT / ORDER REPORT
    |--------------------------------------------------------------------------
    */

    public function stock_report(Request $request)
    {
        $products = Product::select('id', 'name', 'new_price', 'stock')
            ->where('status', 1);

        if ($request->keyword) {
            $products = $products->where('name', 'LIKE', '%' . $request->keyword . "%");
        }
        if ($request->category_id) {
            $products = $products->where('category_id', $request->category_id);
        }
        if ($request->start_date && $request->end_date) {
            $products = $products->whereBetween('updated_at', [$request->start_date, $request->end_date]);
        }

        $total_purchase = $products->sum(\DB::raw('purchase_price * stock'));
        $total_stock    = $products->sum('stock');
        $total_price    = $products->sum(\DB::raw('new_price * stock'));

        $products   = $products->paginate(10);
        $categories = Category::where('status', 1)->get();

        return view('backEnd.reports.stock', compact(
            'products',
            'categories',
            'total_purchase',
            'total_stock',
            'total_price'
        ));
    }

    public function order_report(Request $request)
    {
        $users = User::where('status', 1)->get();

        $orders = OrderDetails::with('shipping', 'order')
            ->whereHas('order', function ($query) {
                $query->where('order_status', 6);
            });

        if ($request->keyword) {
            $orders = $orders->where('name', 'LIKE', '%' . $request->keyword . "%");
        }
        if ($request->user_id) {
            $orders = $orders->whereHas('order', function ($query) use ($request) {
                $query->where('user_id', $request->user_id);
            });
        }
        if ($request->start_date && $request->end_date) {
            $orders = $orders->whereBetween('updated_at', [$request->start_date, $request->end_date]);
        }

        $total_purchase = $orders->sum(\DB::raw('purchase_price * qty'));
        $total_item     = $orders->sum('qty');
        $total_sales    = $orders->sum(\DB::raw('sale_price * qty'));
        $orders         = $orders->paginate(10);

        return view('backEnd.reports.order', compact(
            'orders',
            'users',
            'total_purchase',
            'total_item',
            'total_sales'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | POS ORDER CREATE / UPDATE
    |--------------------------------------------------------------------------
    */

    public function order_create()
    {
        Cart::instance('pos_shopping')->destroy();

        $products = Product::select('id', 'name', 'new_price', 'product_code')
            ->where(['status' => 1])->get();

        $cartinfo       = Cart::instance('pos_shopping')->content();
        $shippingcharge = ShippingCharge::where('status', 1)->get();

        return view('backEnd.order.create', compact(
            'products',
            'cartinfo',
            'shippingcharge'
        ));
    }

    public function order_store(Request $request)
    {
        $this->validate($request, [
            'name'    => 'required',
            'phone'   => 'required',
            'address' => 'required',
            'area'    => 'required',
        ]);

        if (Cart::instance('pos_shopping')->count() <= 0) {
            Toastr::error('Your shopping empty', 'Failed!');
            return redirect()->back();
        }

        $subtotal   = Cart::instance('pos_shopping')->subtotal();
        $subtotal   = str_replace(',', '', $subtotal);
        $subtotal   = str_replace('.00', '', $subtotal);
        $discount   = Session::get('pos_discount') + Session::get('product_discount');
        $shippingfee = ShippingCharge::find($request->area);

        $exits_customer = Customer::where('phone', $request->phone)
            ->select('phone', 'id')->first();

        if ($exits_customer) {
            $customer_id = $exits_customer->id;
        } else {
            $password        = rand(111111, 999999);
            $store           = new Customer();
            $store->name     = $request->name;
            $store->slug     = $request->name;
            $store->phone    = $request->phone;
            $store->password = bcrypt($password);
            $store->verify   = 1;
            $store->status   = 'active';
            $store->save();
            $customer_id = $store->id;
        }

        $order                  = new Order();
        $order->invoice_id      = rand(11111, 99999);
        $order->amount          = ($subtotal + $shippingfee->amount) - $discount;
        $order->discount        = $discount ? $discount : 0;
        $order->shipping_charge = $shippingfee->amount;
        $order->customer_id     = $customer_id;
        $order->order_status    = 1;
        $order->note            = $request->note;
        $order->save();

        $shipping              = new Shipping();
        $shipping->order_id    = $order->id;
        $shipping->customer_id = $customer_id;
        $shipping->name        = $request->name;
        $shipping->phone       = $request->phone;
        $shipping->address     = $request->address;
        $shipping->area        = $shippingfee->name;
        $shipping->save();

        $payment                 = new Payment();
        $payment->order_id       = $order->id;
        $payment->customer_id    = $customer_id;
        $payment->payment_method = 'Cash On Delivery';
        $payment->amount         = $order->amount;
        $payment->payment_status = 'pending';
        $payment->save();

        foreach (Cart::instance('pos_shopping')->content() as $cart) {
            $order_details                   = new OrderDetails();
            $order_details->order_id         = $order->id;
            $order_details->product_id       = $cart->id;
            $order_details->product_name     = $cart->name;
            $order_details->purchase_price   = $cart->options->purchase_price;
            $order_details->product_discount = $cart->options->product_discount;
            $order_details->sale_price       = $cart->price;
            $order_details->qty              = $cart->qty;
            $order_details->save();
        }

        // 🔥 নতুন অর্ডার প্লেস করলে স্টক কমানো (oldStatus = 0, newStatus = 1)
        $this->handleStockChange($order, 0, (int) $order->order_status);

        Cart::instance('pos_shopping')->destroy();
        Session::forget('pos_shipping');
        Session::forget('pos_discount');
        Session::forget('product_discount');

        Toastr::success('Thanks, Your order place successfully', 'Success!');
        return redirect('admin/order/pending');
    }

    public function cart_add(Request $request)
    {
        $product = Product::select('id', 'name', 'stock', 'new_price', 'old_price', 'purchase_price', 'slug')
            ->where(['id' => $request->id])->first();

        $qty      = 1;
        $cartinfo = Cart::instance('pos_shopping')->add([
            'id'      => $product->id,
            'name'    => $product->name,
            'qty'     => $qty,
            'price'   => $product->new_price,
            'options' => [
                'slug'            => $product->slug,
                'image'           => $product->image->image,
                'old_price'       => $product->old_price,
                'purchase_price'  => $product->purchase_price,
                'product_discount'=> 0,
            ],
        ]);
        return response()->json(compact('cartinfo'));
    }

    public function updateNote(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'note_type'=> 'required|in:order,admin',
            'note'     => 'nullable|string',
        ]);

        $order = Order::findOrFail($request->order_id);

        if ($request->note_type === 'order') {
            if (Schema::hasColumn('orders', 'order_note')) {
                $order->order_note = $request->note;
            } else {
                $order->note = $request->note;
            }
        } else {
            $order->admin_note = $request->note;
        }

        $order->save();

        return response()->json([
            'status' => 'success',
            'note'   => $request->note,
        ]);
    }

    public function cart_content()
    {
        $cartinfo = Cart::instance('pos_shopping')->content();
        return view('backEnd.order.cart_content', compact('cartinfo'));
    }

    public function cart_details()
    {
        $cartinfo = Cart::instance('pos_shopping')->content();
        $discount = 0;
        foreach ($cartinfo as $cart) {
            $discount += $cart->options->product_discount * $cart->qty;
        }
        Session::put('product_discount', $discount);
        return view('backEnd.order.cart_details', compact('cartinfo'));
    }

    public function cart_increment(Request $request)
    {
        $qty  = $request->qty + 1;
        $cart = Cart::instance('pos_shopping')->content()->where('rowId', $request->id)->first();

        $cartinfo = Cart::instance('pos_shopping')->update($request->id, [
            'qty'     => $qty,
            'options' => [
                'slug'            => $cart->options->slug,
                'image'           => $cart->options->image,
                'old_price'       => $cart->options->old_price,
                'purchase_price'  => $cart->options->purchase_price,
                'product_discount'=> $cart->options->product_discount,
                'product_size'    => $cart->options->product_size,
                'product_color'   => $cart->options->product_color,
            ],
        ]);
        return response()->json($cartinfo);
    }

    public function cart_decrement(Request $request)
    {
        $qty  = $request->qty - 1;
        $cart = Cart::instance('pos_shopping')->content()->where('rowId', $request->id)->first();

        $cartinfo = Cart::instance('pos_shopping')->update($request->id, [
            'qty'     => $qty,
            'options' => [
                'slug'            => $cart->options->slug,
                'image'           => $cart->options->image,
                'old_price'       => $cart->options->old_price,
                'purchase_price'  => $cart->options->purchase_price,
                'product_discount'=> $cart->options->product_discount,
                'product_size'    => $cart->options->product_size,
                'product_color'   => $cart->options->product_color,
            ],
        ]);

        return response()->json($cartinfo);
    }

    public function cart_remove(Request $request)
    {
        Cart::instance('pos_shopping')->remove($request->id);
        $cartinfo = Cart::instance('pos_shopping')->content();
        return response()->json($cartinfo);
    }

    public function product_discount(Request $request)
    {
        $cart = Cart::instance('pos_shopping')->content()->where('rowId', $request->id)->first();

        $cartinfo = Cart::instance('pos_shopping')->update($request->id, [
            'options' => [
                'slug'            => $cart->options->slug,
                'image'           => $cart->options->image,
                'old_price'       => $cart->options->old_price,
                'purchase_price'  => $cart->options->purchase_price,
                'product_discount'=> $request->discount,
                'product_size'    => $cart->options->product_size,
                'product_color'   => $cart->options->product_color,
            ],
        ]);
        return response()->json($cartinfo);
    }

    public function cart_update(Request $request)
    {
        $rowId    = $request->id;
        $cartItem = Cart::instance('pos_shopping')->content()->where('rowId', $request->id)->first();

        if ($cartItem) {
            Cart::instance('pos_shopping')->update($rowId, [
                'options' => [
                    'product_size'    => $request->product_size ?: $cartItem->options->product_size,
                    'product_color'   => $request->product_color ?: $cartItem->options->product_color,
                    'slug'            => $cartItem->options->slug,
                    'image'           => $cartItem->options->image,
                    'old_price'       => $cartItem->options->old_price,
                    'purchase_price'  => $cartItem->options->purchase_price,
                    'product_discount'=> $cartItem->options->product_discount,
                ],
            ]);
        }

        return response()->json($cartItem);
    }

    public function cart_shipping(Request $request)
    {
        $shipping = ShippingCharge::where(['status' => 1, 'id' => $request->id])
            ->first()->amount;

        Session::put('pos_shipping', $shipping);
        return response()->json($shipping);
    }

    public function cart_clear(Request $request)
    {
        Cart::instance('pos_shopping')->destroy();
        Session::forget('pos_shipping');
        Session::forget('pos_discount');
        Session::forget('product_discount');
        return redirect()->back();
    }

    /*
    |--------------------------------------------------------------------------
    | ORDER EDIT / UPDATE (POS)
    |--------------------------------------------------------------------------
    */

    public function order_edit($invoice_id)
    {
        $products = Product::select('id', 'name', 'new_price', 'product_code')
            ->where(['status' => 1])->get();

        $shippingcharge = ShippingCharge::where('status', 1)->get();
        $order          = Order::where('invoice_id', $invoice_id)->firstOrFail();

        Cart::instance('pos_shopping')->destroy();

        $shippinginfo = Shipping::where('order_id', $order->id)->first();
        Session::put('product_discount', $order->discount);
        Session::put('pos_shipping', $order->shipping_charge);

        $orderdetails = OrderDetails::where('order_id', $order->id)
            ->with(['image', 'color', 'size'])
            ->get();

        foreach ($orderdetails as $ordetails) {
            Cart::instance('pos_shopping')->add([
                'id'      => $ordetails->product_id,
                'name'    => $ordetails->product_name,
                'qty'     => $ordetails->qty,
                'price'   => $ordetails->sale_price,
                'options' => [
                    'image'             => $ordetails->image->image ?? 'public/no-image.png',
                    'purchase_price'    => $ordetails->purchase_price,
                    'product_discount'  => $ordetails->product_discount,
                    'details_id'        => $ordetails->id,
                    'product_color'     => $ordetails->product_color,
                    'product_size'      => $ordetails->product_size,
                    'product_color_name'=> $ordetails->color->name ?? $ordetails->product_color ?? 'N/A',
                    'product_size_name' => $ordetails->size->name  ?? $ordetails->product_size  ?? 'N/A',
                ],
            ]);
        }

        $cartinfo = Cart::instance('pos_shopping')->content();

        return view('backEnd.order.edit', compact(
            'products',
            'cartinfo',
            'shippingcharge',
            'shippinginfo',
            'order'
        ));
    }

    public function order_update(Request $request)
    {
        $this->validate($request, [
            'name'    => 'required',
            'phone'   => 'required',
            'address' => 'required',
            'area'    => 'required',
        ]);

        if (Cart::instance('pos_shopping')->count() <= 0) {
            Toastr::error('Your shopping cart is empty', 'Failed!');
            return redirect()->back();
        }

        $subtotal    = str_replace([',', '.00'], '', Cart::instance('pos_shopping')->subtotal());
        $discount    = Session::get('pos_discount') + Session::get('product_discount');
        $shippingfee = ShippingCharge::find($request->area);

        $customer = Customer::firstOrCreate(
            ['phone' => $request->phone],
            [
                'name'     => $request->name,
                'slug'     => $request->name,
                'password' => bcrypt(rand(111111, 999999)),
                'verify'   => 1,
                'status'   => 'active'
            ]
        );

        $order                  = Order::findOrFail($request->order_id);
        $order->amount          = ($subtotal + $shippingfee->amount) - $discount;
        $order->discount        = $discount ?? 0;
        $order->shipping_charge = $shippingfee->amount;
        $order->customer_id     = $customer->id;
        $order->order_status    = 1; // এখানে চাইলে স্টক হ্যান্ডেল করতে চাইলে handleStockChange আরও কেয়ারফুললি ব্যবহার করতে হবে
        $order->note            = $request->note;
        $order->save();

        $shipping           = Shipping::where('order_id', $order->id)->firstOrFail();
        $shipping->name     = $request->name;
        $shipping->phone    = $request->phone;
        $shipping->address  = $request->address;
        $shipping->area     = $shippingfee->name;
        $shipping->save();

        $payment                 = Payment::where('order_id', $order->id)->firstOrNew(['order_id' => $order->id]);
        $payment->customer_id    = $customer->id;
        $payment->payment_method = 'Cash On Delivery';
        $payment->amount         = $order->amount;
        $payment->payment_status = 'pending';
        $payment->save();

        $existingDetails = OrderDetails::where('order_id', $order->id)->pluck('id')->toArray();
        $updatedIds      = [];

        foreach (Cart::instance('pos_shopping')->content() as $cart) {
            if (!empty($cart->options->details_id) && in_array($cart->options->details_id, $existingDetails)) {
                $detail = OrderDetails::find($cart->options->details_id);
            } else {
                $detail              = new OrderDetails();
                $detail->order_id    = $order->id;
                $detail->product_id  = $cart->id;
                $detail->product_name= $cart->name;
            }

            $detail->purchase_price   = $cart->options->purchase_price ?? 0;
            $detail->product_discount = $cart->options->product_discount ?? 0;
            $detail->product_color    = $cart->options->product_color ?? null;
            $detail->product_size     = $cart->options->product_size ?? null;
            $detail->sale_price       = $cart->price;
            $detail->qty              = $cart->qty;
            $detail->save();

            $updatedIds[] = $detail->id;
        }

        OrderDetails::where('order_id', $order->id)
            ->whereNotIn('id', $updatedIds)
            ->delete();

        Cart::instance('pos_shopping')->destroy();
        Session::forget(['pos_shipping', 'pos_discount', 'product_discount']);

        Toastr::success('Order updated successfully!', 'Success!');
        return redirect()->route('admin.orders', 'pending');
    }

    /*
    |--------------------------------------------------------------------------
    | PAYMENT STATUS UPDATE
    |--------------------------------------------------------------------------
    */

    public function updatePaymentStatus(Request $request)
    {
        $order = Order::find($request->order_id);

        if (!$order) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Order not found!',
            ]);
        }

        $order->payment_status = $request->payment_status;
        $order->save();

        $payment = Payment::where('order_id', $order->id)->first();
        if ($payment) {
            $payment->payment_status = $request->payment_status;
            $payment->save();
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Payment status updated successfully!',
        ]);
    }
}
