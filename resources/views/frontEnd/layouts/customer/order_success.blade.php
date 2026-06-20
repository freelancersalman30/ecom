@extends('frontEnd.layouts.master')
@section('title','Order Success')

@php
    // এই অর্ডারের পেমেন্ট ইনফো নিই
    $payment = \App\Models\Payment::where('order_id', $order->id)->orderBy('id','desc')->first();

    // যদি payment টেবিলে amount থাকে (মানে advance/full যাই হোক)
    $advance_amount = ($payment && $payment->amount > 0) ? $payment->amount : 0;

    // অর্ডারের গ্র্যান্ড টোটাল (ইনভয়েসে যে দেখাও)
    $grand_total = $order->amount;

    // বাকি টাকা (ডেলিভারির সময় দিতে হবে)
    $due_amount = max(0, $grand_total - $advance_amount);

    // পেমেন্ট মেথড COD কি না চেক করার জন্য
    $payment_method = $payment->payment_method ?? null;
    $is_cod = $payment_method 
        ? in_array(strtolower($payment_method), ['cash on delivery', 'cod'])
        : false;

    // ⭐ এই অর্ডারের জন্য ডিজিটাল ডাউনলোডগুলো নিই
    $downloads = \App\Models\DigitalDownload::where('order_id', $order->id)->get();
@endphp

@section('content')
<section class="customer-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-8">
                <div class="success-img">
                    <img src="{{asset('frontEnd/images/order-success.png')}}" alt="">
                </div>
                <div class="success-title">
                    <h2>আপনার অর্ডারটি আমাদের কাছে সফলভাবে পৌঁছেছে, কিছুক্ষনের মধ্যে আমাদের একজন প্রতিনিধি আপনার নাম্বারে কল করবেন </h2>
                </div>

                <h5 class="my-3">Your Order Details</h5>
				
                <div class="success-table">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>
                                    <p>Invoice ID</p>
                                    <p><strong>{{$order->invoice_id}}</strong></p>
                                </td>
                                <td>
                                    <p>Date</p>
                                    <p><strong>{{$order->created_at->format('d-m-y')}}</strong></p>
                                </td>
                                <td>
                                    <p>Phone</p>
                                    <p><strong>{{$order->shipping?$order->shipping->phone:''}}</strong></p>
                                </td>
                                <td>
                                    <p>Total</p>
                                    <p><strong>৳ {{$order->amount}}</strong></p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <p>Payment Method</p>
                                    <p><strong>{{$payment->payment_method ?? ''}}</strong></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    @if(!empty($order->order_note) || !empty($order->note))
                        <p><strong>Order Note:</strong> {{ $order->order_note ?? $order->note }}</p>
                    @endif

                </div>

                {{-- ⭐⭐ DIGITAL DOWNLOAD SECTION ⭐⭐ --}}
                @if($downloads->count() > 0)
                    <div class="success-table mt-4">
                        <h5 class="mb-3">📥 Your Digital Downloads</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Download Link</th>
                                    <th>Expires At</th>
                                    <th>Remaining Downloads</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($downloads as $dl)
                                    <tr>
                                        <td>{{ $dl->product->name ?? 'Digital Product' }}</td>
                                        <td>
                                            <a href="{{ route('digital.download', $dl->token) }}" 
                                               class="btn btn-success btn-sm" target="_blank">
                                                Download
                                            </a>
                                        </td>
                                        <td>
                                            @if($dl->expires_at)
                                                {{ \Carbon\Carbon::parse($dl->expires_at)->format('d-m-Y') }}
                                            @else
                                                <span class="text-muted">Never Expires</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(is_null($dl->remaining_downloads))
                                                Unlimited
                                            @else
                                                {{ $dl->remaining_downloads }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                {{-- ⭐⭐ END DIGITAL DOWNLOAD SECTION ⭐⭐ --}}

                <!-- success table -->
                <h5 class="my-4">Pay with cash upon delivery</h5>
                <div class="success-table">
                    <h6 class="mb-3">Order Delivery</h6>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderdetails as $key=>$value)
                            <tr>
                                <td>
                                    <p>{{$value->product_name}} x {{$value->qty}}</p>
                                </td>
                                <td><p><strong>৳ {{$value->sale_price}}</strong></p></td>
                            </tr>
                            @endforeach
                            <tr>
                                <th  class="text-end px-4">Net Total</th>
                                <td><strong id="net_total">৳{{$order->amount + $order->discount- $order->shipping_charge}}</strong></td>
                            </tr>
                            @if($order->discount)
                            <tr>
                                <th  class="text-end px-4">Discount</th>
                                <td><strong id="net_total">৳{{$order->discount}}</strong></td>
                            </tr>
                            @endif
                            <tr>
                                <th  class="text-end px-4">Shipping Cost</th>
                                <td>
                                    <strong id="cart_shipping_cost">৳{{$order->shipping_charge}}</strong>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-end px-4">Grand Total</th>
                                <td>
                                    <strong id="grand_total">৳ {{$order->amount}}</strong>
                                </td>
                            </tr>

                            {{-- ✅ Advance Paid / Due on Delivery শুধু তখনই দেখাবে যখন advance আছে এবং COD না --}}
                            @if($advance_amount > 0 && !$is_cod)
                                <tr>
                                    <th class="text-end px-4">Advance Paid</th>
                                    <td>
                                        <strong>৳ {{ number_format($advance_amount, 2) }}</strong>
                                    </td>
                                </tr>

                                <tr>
                                    <th class="text-end px-4">Due on Delivery</th>
                                    <td>
                                        <strong>৳ {{ number_format($due_amount, 2) }}</strong>
                                    </td>
                                </tr>
                            @endif

                        </tbody>
                    </table>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>
                                    <h5 class="my-4">Billing Address</h5>
                                    <p>{{$order->shipping?$order->shipping->name:''}}</p>
                                    <p>{{$order->shipping?$order->shipping->phone:''}}</p>
                                    <p>{{$order->shipping?$order->shipping->address:''}}</p>
                                    <p>{{$order->shipping?$order->shipping->area:''}}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- success table -->
                <a href="{{route('home')}}" class=" my-5 btn btn-primary">Go To Home</a>
                <button onclick="showTotalAndDownload()" class="no-print btn btn-xs btn-info waves-effect waves-light">
                    <i class="fa fa-print"></i> Print Invoice
                </button>
                <a href="{{route('customer.invoice_download', $order->id)}}" class="no-print btn btn-xs btn-success waves-effect waves-light">
                    <i class="fa fa-download"></i> Download PDF
                </a>
            </div>
        </div>
    </div>
</section>

<section id="customer-invoice" class="customer-invoice" style="display:none;">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 mt-3">
                <div class="invoice-innter" style="width:760px;margin: 0 auto;background: #fff;overflow: hidden;padding: 30px;padding-top: 0;">
                    <table style="width:100%">
                        <tr>
                            <td style="width: 40%; float: left; padding-top: 15px;">
                                <img src="{{asset($generalsetting->white_logo)}}" width="190px" style="margin-top:25px !important" alt="">
                                <p style="font-size: 14px; color: #222; margin: 20px 0;">
                                    <strong>Payment Method:</strong>
                                    <span style="text-transform: uppercase;">{{$order->payment?$order->payment->payment_method:''}}</span>
                                </p>
                                <div class="invoice_form">
                                    <p style="font-size:16px;line-height:1.8;color:#222"><strong>Invoice From:</strong></p>
                                    <p style="font-size:16px;line-height:1.8;color:#222">{{$generalsetting->name}}</p>
                                    <p style="font-size:16px;line-height:1.8;color:#222">{{$contact->phone}}</p>
                                    <p style="font-size:16px;line-height:1.8;color:#222">{{$contact->email}}</p>
                                    <p style="font-size:16px;line-height:1.8;color:#222">{{$contact->address}}</p>

                                    @if(!empty($order->order_note) || !empty($order->note))
                                        <p style="font-size:16px;line-height:1.8;color:#222">
                                            <strong>Order Note:</strong> {{ $order->order_note ?? $order->note }}
                                        </p>
                                    @endif
                                </div>
                            </td>
                            <td  style="width:60%;float: left;">
                                <div class="invoice-bar" style=" background: #4DBC60; transform: skew(38deg); width: 100%; margin-left: 65px; padding: 20px 60px; ">
                                    <p style="font-size: 30px; color: #fff; transform: skew(-38deg); text-transform: uppercase; text-align: right; font-weight: bold;">Invoice</p>
                                </div>
                                <div class="invoice-bar" style="background: #fff; transform: skew(36deg); width: 72%; margin-left: 182px; padding: 12px 32px; margin-top: 6px;">
                                    <p style="font-size: 15px; color: #222;font-weight:bold; transform: skew(-36deg); text-align: right; padding-right: 18px">
                                        Invoice ID : <strong>#{{$order->invoice_id}}</strong>
                                    </p>
                                    <p style="font-size: 15px; color: #222;font-weight:bold; transform: skew(-36deg); text-align: right; padding-right: 32px">
                                        Invoice Date: <strong>{{$order->created_at->format('d-m-y')}}</strong>
                                    </p>
                                </div>
                                <div class="invoice_to" style="padding-top: 20px;">
                                    <p style="font-size:16px;line-height:1.8;color:#222;text-align: right;"><strong>Invoice To:</strong></p>
                                    <p style="font-size:16px;line-height:1.8;color:#222;text-align: right;">{{$order->shipping?$order->shipping->name:''}}</p>
                                    <p style="font-size:16px;line-height:1.8;color:#222;text-align: right;">{{$order->shipping?$order->shipping->phone:''}}</p>
                                    <p style="font-size:16px;line-height:1.8;color:#222;text-align: right;">{{$order->shipping?$order->shipping->address:''}}</p>
                                    <p style="font-size:16px;line-height:1.8;color:#222;text-align: right;">{{$order->shipping?$order->shipping->area:''}}</p>
                                </div>
                            </td>
                        </tr>
                    </table>

                    <table class="table" style="margin-top: 30px;margin-bottom: 0;">
                        <thead style="background: #4DBC60; color: #fff;">
                            <tr>
                                <th>SL</th>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderdetails as $key=>$value)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>
                                    {{$value->product_name}} <br>
                                    @if($value->size)
                                        <small>Size: {{$value->size->name}}</small>
                                    @endif
                                    @if($value->color)
                                        <small>Color: {{$value->color->name}}</small>
                                    @endif
                                </td>
                                <td>৳{{$value->sale_price}}</td>
                                <td>{{$value->qty}}</td>
                                <td>৳{{$value->sale_price*$value->qty}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="invoice-bottom">
                        <table class="table" style="width: 300px; float: right; margin-bottom: 30px;">
                            <tbody style="background:#f1f9f8">
                                <tr>
                                    <td><strong>SubTotal</strong></td>
                                    <td><strong>৳{{$order->amount + $order->discount- $order->shipping_charge}}</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Shipping(+)</strong></td>
                                    <td><strong>৳{{$order->shipping_charge}}</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Discount(-)</strong></td>
                                    <td><strong>৳{{$order->discount}}</strong></td>
                                </tr>
                                <tr style="background:#4DBC60;color:#fff">
                                    <td><strong>Final Total</strong></td>
                                    <td><strong>৳{{$order->amount}}</strong></td>
                                </tr>

                                {{-- ✅ Advance Paid / Due on Delivery শুধু তখনই দেখাবে যখন advance আছে এবং COD না --}}
                                @if($advance_amount > 0 && !$is_cod)
                                    <tr>
                                        <td><strong>Advance Paid</strong></td>
                                        <td><strong>৳ {{ number_format($advance_amount, 2) }}</strong></td>
                                    </tr>

                                    <tr>
                                        <td><strong>Due on Delivery</strong></td>
                                        <td><strong>৳ {{ number_format($due_amount, 2) }}</strong></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>

                        <div class="terms-condition" style="overflow: hidden; width: 100%; text-align: center; padding: 20px 0; border-top: 1px solid #ddd;">
                            <h5 style="font-style: italic;">
                                <a href="{{route('page',['slug'=>'terms-condition'])}}">Terms & Conditions</a>
                            </h5>
                            <p style="text-align: center; font-style: italic; font-size: 15px; margin-top: 10px;">
                                * This is a computer generated invoice, does not require any signature.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('script')

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

<script>
function showTotalAndDownload() {
    // Show the total section
    document.getElementById('customer-invoice').style.display = 'block';
    
    // Download PDF after showing the total section
    var element = document.querySelector('.invoice-innter'); // Select the invoice section to convert to PDF
    html2pdf().from(element).set({
        margin: 1,
        filename: 'invoice.pdf',
        html2canvas: { scale: 2 },
        jsPDF: { orientation: 'portrait', unit: 'mm', format: 'a4' }
    }).save();
    
    // Optionally, hide the total section again after saving the PDF
    setTimeout(() => {
        document.getElementById('customer-invoice').style.display = 'none';
    }, 100);
}
</script>

{{-- 🔹 GA4 Purchase + Facebook Pixel Purchase Event --}}
<script type="text/javascript">
    window.dataLayer = window.dataLayer || [];

    (function () {
        // PHP থেকে ডাটা
        const grandTotal    = parseFloat("{{ $grand_total }}") || 0;
        const advancePaid   = parseFloat("{{ $advance_amount }}") || 0;
        const dueOnDelivery = parseFloat("{{ $due_amount }}") || 0;
        const currency      = "BDT";
        const coupon        = @json($order->coupon_code ?? null);

        // GA4 items
        const ga4Items = [
            @foreach($order->orderdetails as $key=>$item)
            {
                item_id: "{{ $item->product_id }}",
                item_name: {!! json_encode($item->product_name) !!},
                price: {{ (float)$item->sale_price }},
                quantity: {{ (int)$item->qty }},
                index: {{ $key }}
            }@if(!$loop->last),@endif
            @endforeach
        ];

        // যদি প্রোডাক্ট থাকে তখনই ইভেন্ট পাঠাবো
        if (ga4Items.length) {
            // 🔸 GA4: purchase
            window.dataLayer.push({ ecommerce: null });
            window.dataLayer.push({
                event: "purchase",
                ecommerce: {
                    transaction_id: "{{ $order->invoice_id }}",
                    value: grandTotal,
                    currency: currency,
                    shipping: {{ $order->shipping_charge ?? 0 }},
                    discount: {{ $order->discount ?? 0 }},
                    coupon: coupon,
                    items: ga4Items
                }
            });
        }

        // Facebook Pixel এর জন্য contents
        const fbContents = [
            @foreach($order->orderdetails as $key=>$item)
            {
                id: "{{ $item->product_id }}",
                quantity: {{ (int)$item->qty }},
                item_price: {{ (float)$item->sale_price }}
            }@if(!$loop->last),@endif
            @endforeach
        ];

        if (typeof fbq === "function" && fbContents.length) {
            fbq('track', 'Purchase', {
                value: grandTotal,
                currency: currency,
                contents: fbContents,
                content_ids: fbContents.map(function(c){ return c.id; }),
                num_items: fbContents.reduce(function(sum, c){ return sum + c.quantity; }, 0),
                order_id: "{{ $order->invoice_id }}",
                advance_amount: advancePaid,
                due_amount: dueOnDelivery,
                coupon: coupon || undefined
            });
        }
    })();
</script>
@endpush
