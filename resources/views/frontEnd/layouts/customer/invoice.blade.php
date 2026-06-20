@extends('frontEnd.layouts.master')
@section('title','Customer Invoice')
@section('content')

<style>
    .customer-invoice { margin: 25px 0; }
    .invoice_btn{ margin-bottom: 15px; }
    td{ font-size: 16px; }

   @page { size: a4;  margin: 0mm; background:#F9F9F9 }
   @media print {
        td{ font-size: 18px; }
        header,footer,.no-print { display: none !important; }
   }
</style>

<section class="customer-invoice">
    <div class="container">
        <div class="row">

            <div class="col-sm-6">
                <a href="{{route('customer.orders')}}">
                    <strong><i class="fa-solid fa-arrow-left"></i> Back To Order</strong>
                </a>
            </div>

            <div class="col-sm-6 text-end">
                <button onclick="printFunction()" class="no-print invoice_btn">
                    <i class="fa fa-print"></i>
                </button>
            </div>

            <div class="col-sm-12">

                <!-- INVOICE BOX -->
                <div class="invoice-innter" style="width: 900px;margin: 0 auto;background: #f9f9f9;overflow: hidden;padding: 30px;padding-top: 0;">

                    {{-- ===================== INVOICE HEADER ===================== --}}
                    <table style="width:100%">
                        <tr>
                            <td style="width: 40%; float: left; padding-top: 15px;">

                                <img src="{{asset($generalsetting->white_logo)}}" style="margin-top:25px !important;width:150px">

                                <p style="font-size: 14px; color: #222; margin: 20px 0;">
                                    <strong>Payment Method:</strong> 
                                    <span style="text-transform: uppercase;">
                                        {{$order->payment?$order->payment->payment_method:''}}
                                    </span>
                                </p>

                                <div class="invoice_form">
                                    <p><strong>Invoice From:</strong></p>
                                    <p>{{$generalsetting->name}}</p>
                                    <p>{{$contact->phone}}</p>
                                    <p>{{$contact->email}}</p>
                                    <p>{{$contact->address}}</p>
									{{-- ⭐ SHOW ORDER NOTE --}}
@if(!empty($order->order_note) || !empty($order->note))
    <p style="font-size:16px; line-height:1.8; color:#222;">
        <strong>Order Note:</strong> {{ $order->order_note ?? $order->note }}
    </p>
@endif

                                </div>
                            </td>

                            <td style="width:60%;float: left;">
                                <div class="invoice-bar" style="background:#00aef0; transform: skew(38deg); padding: 20px 60px; margin-left: 65px;">
                                    <p style="font-size: 30px; color: #fff; transform: skew(-38deg); text-align: right; font-weight: bold;">Invoice</p>
                                </div>

                                <div class="invoice-bar" style="background:#fff; transform: skew(36deg); width: 80%; margin-left: 182px; padding: 12px 32px; margin-top: 6px;text-align:right">
                                   <p style="transform: skew(-36deg);display:inline-block">Invoice Date: <strong>{{$order->created_at->format('d-m-y')}}</strong></p>
                                   <p style="transform: skew(-36deg);display:inline-block">Invoice No: <strong>{{$order->invoice_id}}</strong></p>
                                </div>

                                <div class="invoice_to" style="padding-top: 20px;">
                                    <p><strong>Invoice To:</strong></p>
                                    <p>{{$order->shipping?$order->shipping->name:''}}</p>
                                    <p>{{$order->shipping?$order->shipping->phone:''}}</p>
                                    <p>{{$order->shipping?$order->shipping->address:''}}</p>
                                    <p>{{$order->shipping?$order->shipping->area:''}}</p>
                                </div>
                            </td>
                        </tr>
                    </table>

                    {{-- ===================== PRODUCTS TABLE ===================== --}}
                    <table class="table" style="margin-top: 30px;">
                        <thead style="background: #00aef0; color: #fff;">
                            <tr>
                                <th>SL</th>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($order->orderdetails as $value)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$value->product_name}}</td>
                                <td>৳{{$value->sale_price}}</td>
                                <td>{{$value->qty}}</td>
                                <td>৳{{$value->sale_price * $value->qty}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- ===================== TOTAL CALCULATION ===================== --}}
                    @php
                        $subtotal = $order->orderdetails->sum('sale_price');
                        $shipping = $order->shipping_charge;
                        $discount = $order->discount;
                        $totalAmount = $order->amount;

                        // total paid (advance)
                        $advancePaid = \App\Models\Payment::where('order_id', $order->id)->sum('amount');
                        $dueAmount = $totalAmount - $advancePaid;
                    @endphp

                    <div class="invoice-bottom">
                        <table class="table" style="width: 300px; float: right; margin-bottom: 30px;">
                            <tbody style="background:#00aef0; color:#fff;">

                                <tr>
                                    <td><strong>SubTotal</strong></td>
                                    <td><strong>৳{{$subtotal}}</strong></td>
                                </tr>

                                <tr>
                                    <td><strong>Shipping(+)</strong></td>
                                    <td><strong>৳{{$shipping}}</strong></td>
                                </tr>

                                <tr>
                                    <td><strong>Discount(-)</strong></td>
                                    <td><strong>৳{{$discount}}</strong></td>
                                </tr>

                                <tr>
                                    <td><strong>Final Total</strong></td>
                                    <td><strong>৳{{$totalAmount}}</strong></td>
                                </tr>

                                {{-- ========== SHOW ADVANCE & DUE ONLY IF ADVANCE PAID ========== --}}
                                @if($advancePaid > 0 && $advancePaid < $totalAmount)
                                <tr>
                                    <td><strong>Advance Paid</strong></td>
                                    <td><strong>৳{{ number_format($advancePaid, 2) }}</strong></td>
                                </tr>

                                <tr>
                                    <td><strong>Due Amount</strong></td>
                                    <td><strong>৳{{ number_format($dueAmount, 2) }}</strong></td>
                                </tr>
                                @endif

                            </tbody>
                        </table>

                        <div class="terms-condition" style="overflow: hidden; width: 100%; text-align: center; padding: 20px 0;">
                            <h5 style="font-style: italic;">
                                <a href="{{route('page',['slug'=>'terms-condition'])}}">Terms & Conditions</a>
                            </h5>
                            <p style="text-align: center; font-style: italic; font-size: 15px;">* This is a computer generated invoice.</p>
                        </div>
                    </div>

                </div> <!-- invoice inner -->
            </div>
        </div>
    </div>
</section>

<script>
    function printFunction() {
        window.print();
    }
</script>

@endsection
