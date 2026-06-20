<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customer Invoice - {{$order->invoice_id}}</title>
    <style>
        body { font-family: sans-serif; background: #fff; margin: 0; padding: 0; }
        .invoice-innter { width: 100%; margin: 0 auto; background: #fff; padding: 20px; }
        td { font-size: 14px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        .table th, .table td { padding: 8px; border-bottom: 1px solid #ddd; text-align: left; }
        .table th { background: #00aef0; color: #fff; }
        .invoice-bottom table { width: 300px; float: right; margin-bottom: 30px; border-collapse: collapse; }
        .invoice-bottom table td { padding: 8px; border-bottom: 1px solid #eee; }
        .invoice-bottom table tr { background: #00aef0; color: #fff; }
        .terms-condition { clear: both; width: 100%; text-align: center; padding: 20px 0; margin-top: 200px; }
    </style>
</head>
<body>
    <div class="invoice-innter">
        <table style="width:100%">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    @if($generalsetting->white_logo)
                        <!-- Note: Using public_path() or base64 for images is better for dompdf -->
                        <img src="{{ public_path(str_replace('public/', '', $generalsetting->white_logo)) }}" style="width:150px">
                    @else
                        <h2>{{$generalsetting->name}}</h2>
                    @endif

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
                        @if(!empty($order->order_note) || !empty($order->note))
                        <p style="font-size:14px; line-height:1.8; color:#222;">
                            <strong>Order Note:</strong> {{ $order->order_note ?? $order->note }}
                        </p>
                        @endif
                    </div>
                </td>

                <td style="width: 50%; vertical-align: top; text-align: right;">
                    <div style="background:#00aef0; padding: 10px; margin-bottom: 10px; color: #fff;">
                        <h2 style="margin: 0;">INVOICE</h2>
                    </div>

                    <div style="background:#f9f9f9; padding: 10px; margin-bottom: 20px;">
                        <p style="margin: 5px 0;">Invoice Date: <strong>{{$order->created_at->format('d-m-y')}}</strong></p>
                        <p style="margin: 5px 0;">Invoice No: <strong>{{$order->invoice_id}}</strong></p>
                    </div>

                    <div class="invoice_to" style="text-align: right;">
                        <p><strong>Invoice To:</strong></p>
                        <p>{{$order->shipping?$order->shipping->name:''}}</p>
                        <p>{{$order->shipping?$order->shipping->phone:''}}</p>
                        <p>{{$order->shipping?$order->shipping->address:''}}</p>
                        <p>{{$order->shipping?$order->shipping->area:''}}</p>
                    </div>
                </td>
            </tr>
        </table>

        <table class="table">
            <thead>
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

        @php
            $subtotal = $order->orderdetails->sum('sale_price');
            $shipping = $order->shipping_charge;
            $discount = $order->discount;
            $totalAmount = $order->amount;

            $advancePaid = \App\Models\Payment::where('order_id', $order->id)->sum('amount');
            $dueAmount = $totalAmount - $advancePaid;
        @endphp

        <div class="invoice-bottom" style="margin-top: 20px;">
            <table class="table" style="width: 300px; float: right;">
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
        </div>

        <div class="terms-condition">
            <h5 style="font-style: italic;">Terms & Conditions</h5>
            <p style="text-align: center; font-style: italic; font-size: 13px;">* This is a computer generated invoice.</p>
        </div>
    </div>
</body>
</html>
