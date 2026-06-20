@extends('backEnd.layouts.master')
@section('title','Order Invoice')
@section('content')
<style>
    .customer-invoice {
        margin: 25px 0;
    }
    .invoice_btn{
        margin-bottom: 15px;
    }
    p{
        margin:0;
    }
    td{
        font-size: 16px;
    }
   @page { 
    margin:0px;
    }
   @media print {
    .invoice-innter{
        margin-left: -120px !important;
    }
    .invoice_btn{
        margin-bottom: 0 !important;
    }
    td{
        font-size: 18px;
    }
    p{
        margin:0;
    }
    header,footer,.no-print,.left-side-menu,.navbar-custom {
      display: none !important;
    }
  }
</style>

<section class="customer-invoice ">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <a href="/admin/order/all" class="no-print"><strong><i class="fe-arrow-left"></i> Back To Order</strong></a>
            </div>
            <div class="col-sm-6 text-end">
                <button onclick="printFunction()" class="no-print btn btn-xs btn-success waves-effect waves-light"><i class="fa fa-print"></i></button>
            </div>

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

                                <!-- ✅ নতুন Payment Gateway + Status অংশ -->
                                <div style="margin-bottom:15px;">
                                    <p><strong>Payment Gateway:</strong> {{ ucfirst($order->payment_gateway ?? 'N/A') }}</p>
                                    <p><strong>Payment Status:</strong></p>
                                    <select id="payment_status_{{ $order->id }}" class="form-control no-print" style="width:auto; display:inline-block;">
                                        <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="unpaid" {{ $order->payment_status == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                        <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Failed</option>
                                    </select>
                                    <button class="btn btn-sm btn-success no-print" onclick="updatePaymentStatus({{ $order->id }})">Update</button>
                                </div>
                                <!-- ✅ END -->

                                <div class="invoice_form">
                                    <p style="font-size:16px;line-height:1.8;color:#222"><strong>Invoice From:</strong></p>
                                    <p style="font-size:16px;line-height:1.8;color:#222">{{$generalsetting->name}}</p>
                                    <p style="font-size:16px;line-height:1.8;color:#222">{{$contact->phone}}</p>
                                    <p style="font-size:16px;line-height:1.8;color:#222">{{$contact->email}}</p>
                            {{-- ⭐ SHOW ORDER NOTE --}}
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
                                    <p style="font-size: 15px; color: #222;font-weight:bold; transform: skew(-36deg); text-align: right; padding-right: 18px">Invoice ID : <strong>#{{$order->invoice_id}}</strong></p>
                                    <p style="font-size: 15px; color: #222;font-weight:bold; transform: skew(-36deg); text-align: right; padding-right: 32px">Invoice Date: <strong>{{$order->created_at->format('d-m-y')}}</strong></p>
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
                                <td>{{$value->product_name}} 
                                    <br> 
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
                       @php
    $subtotal = $order->orderdetails->sum('sale_price');
    $shipping = $order->shipping_charge;
    $discount = $order->discount;
    $finalTotal = $order->amount;

    // Payment Table থেকে নেওয়া Paid/Advance Amount
    $advancePaid = \App\Models\Payment::where('order_id', $order->id)->sum('amount');

    // Due Amount
    $dueAmount = $finalTotal - $advancePaid;
@endphp

<table class="table" style="width: 300px; float: right; margin-bottom: 30px;">
    <tbody style="background:#f1f9f8">
        <tr>
            <td><strong>SubTotal</strong></td>
            <td><strong>৳{{ $subtotal }}</strong></td>
        </tr>
        <tr>
            <td><strong>Shipping(+)</strong></td>
            <td><strong>৳{{ $shipping }}</strong></td>
        </tr>
        <tr>
            <td><strong>Discount(-)</strong></td>
            <td><strong>৳{{ $discount }}</strong></td>
        </tr>

        <tr style="background:#4DBC60;color:#fff">
            <td><strong>Final Total</strong></td>
            <td><strong>৳{{ $finalTotal }}</strong></td>
        </tr>

        {{-- 🔥 যদি Advance Payment থাকে --}}
        @if($advancePaid > 0 && $advancePaid < $finalTotal)
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


                        <div class="terms-condition" style="overflow: hidden; width: 100%; text-align: center; padding: 20px 0; border-top: 1px solid #ddd;">
                            <h5 style="font-style: italic;"><a href="{{route('page',['slug'=>'terms-condition'])}}">Terms & Conditions</a></h5>
                            <p style="text-align: center; font-style: italic; font-size: 15px; margin-top: 10px;">* This is a computer generated invoice, does not require any signature.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ✅ JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

<script>
function printFunction() {
    window.print();
}

function updatePaymentStatus(orderId) {
    let status = document.getElementById('payment_status_' + orderId).value;

    fetch('{{ route("admin.order.updatePaymentStatus") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ order_id: orderId, payment_status: status })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            toastr.success(data.message, 'Success!');
        } else {
            toastr.error(data.message, 'Error!');
        }
    })
    .catch(err => {
        toastr.error('Something went wrong!', 'Error!');
    });
}
</script>
@endsection
