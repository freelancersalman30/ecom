@extends('frontEnd.layouts.master')
@section('title', 'Customer Checkout')

@push('css')
<link rel="stylesheet" href="{{ asset('frontEnd/css/select2.min.css') }}" />
@endpush

@section('content')
<section class="chheckout-section">
    @php
        $subtotalString = Cart::instance('shopping')->subtotal();
        $subtotal = (float) preg_replace('/[^\d.]/', '', $subtotalString);

        $shipping = (float) preg_replace('/[^\d.]/', '', Session::get('shipping', 0));
        $discount = (float) preg_replace('/[^\d.]/', '', Session::get('discount', 0));
        $grand_total = $subtotal + $shipping - $discount;

        // ✅ cart items prepare for JS
        $cartItemsForJs = [];
        foreach (Cart::instance('shopping')->content() as $item) {
            $cartItemsForJs[] = [
                'id'    => $item->id,
                'name'  => $item->name,
                'qty'   => $item->qty,
                'price' => (float) $item->price,
                'image' => asset($item->options->image ?? ''),
                'link'  => isset($item->options->slug) ? url('/product/' . $item->options->slug) : '#',
            ];
        }

        // ✅ কার্ট থেকে মোট Advance Amount বের করছি
        $advance_amount = \App\Http\Controllers\Frontend\ShoppingController::getCartAdvanceAmount();
        $hasAdvance     = $advance_amount > 0 ? true : false;

        // অগ্রিম থাকলে গ্রাহক এখন যত টাকা দিবে
        $payable_now = $hasAdvance ? $advance_amount : $grand_total;

        // অগ্রিম থাকলে কত টাকা ডেলিভারির সময় দিতে হবে
        $due_amount = $hasAdvance ? ($grand_total - $advance_amount) : 0;

        // ✅ কার্টে ডিজিটাল প্রোডাক্ট আছে কিনা চেক
        $hasDigital = false;
        foreach (Cart::instance('shopping')->content() as $item) {
            $p = \App\Models\Product::find($item->id);
            if ($p && $p->is_digital == 1) {
                $hasDigital = true;
                break;
            }
        }
    @endphp

    <div class="container">
        <div class="row">
            {{-- Left side checkout --}}
            <div class="col-sm-5 cus-order-2">
                <div class="checkout-shipping">
                    <form id="checkout-form" action="{{ route('customer.ordersave') }}" method="POST" data-parsley-validate="">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h6>আপনার অর্ডারটি কনফার্ম করতে তথ্যগুলো পূরণ করে "অর্ডার করুন" বাটন এ ক্লিক করুন </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- name --}}
                                    <div class="col-sm-12">
                                        <div class="form-group mb-3">
                                            <label for="name">আপনার নাম লিখুন *</label>
                                            <input type="text" id="name" class="form-control" name="name"
                                                value="{{ Auth::guard('customer')->user()->name ?? old('name') }}" required>
                                        </div>
                                    </div>

                                    {{-- phone --}}
                                    <div class="col-sm-12">
                                        <div class="form-group mb-3">
                                            <label for="phone">মোবাইল নাম্বার দিন *</label>
                                            <input type="text" id="phone" class="form-control" minlength="11" maxlength="11"
                                                pattern="0[0-9]+" name="phone"
                                                value="{{ Auth::guard('customer')->user()->phone ?? old('phone') }}" required>
                                        </div>
                                    </div>

                                    {{-- address --}}
                                    <div class="col-sm-12">
                                        <div class="form-group mb-3">
                                            <label for="address">ঠিকানা লিখুন *</label>
                                            <input type="text" id="address" class="form-control" name="address"
                                                value="{{ Auth::guard('customer')->user()->address ?? old('address') }}" required>
                                        </div>
                                    </div>

                                    {{-- order note --}}
                                    <div class="col-sm-12 mb-3">
                                        <div style="border:1px solid #ddd; padding:15px; border-radius:8px; background:#f9f9f9;">
                                            <label for="order_note" style="font-weight:600; margin-bottom:8px; display:block;">
                                                Order Note (ঐচ্ছিক)
                                            </label>

                                            <textarea 
                                                name="order_note" 
                                                id="order_note" 
                                                class="form-control" 
                                                rows="3"
                                                placeholder="যেমন: সন্ধ্যার পর ডেলিভারি করবেন, আগে ফোন করবেন..."
                                                style="resize:none;">{{ $order_note ?? '' }}</textarea>
                                        </div>
                                    </div>

                                    {{-- area --}}
                                    <div class="col-sm-12">
                                        <div class="form-group mb-3">
                                            <label for="area">ডেলিভারি এরিয়া নিবার্চন করুন *</label>
                                            <select id="area" class="form-control" name="area" required>
                                                @foreach ($shippingcharge as $value)
                                                    <option 
                                                        value="{{ $value->id }}"
                                                        data-charge="{{ $value->amount }}"
                                                        {{ Session::get('shipping_id') == $value->id ? 'selected' : '' }}
                                                    >
                                                        {{ $value->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- payment --}}
                                    <div class="col-sm-12">
                                        <div class="radio_payment">
                                            <label id="payment_method">পেমেন্ট মেথড (optional)</label>
                                        </div>

                                        <!-- ================== Payment Methods ================== -->
                                        <div class="payment-methods mt-3">
                                            <div class="row g-3">

                                                {{-- 🔔 ডিজিটাল প্রোডাক্ট বা advance থাকলে COD লুকাবে --}}
                                                @if(!$hasDigital && !$hasAdvance)
                                                    {{-- 🟢 Cash On Delivery --}}
                                                    <div class="col-md-6 col-12">
                                                        <div class="form-check p_cash">
                                                            <input class="form-check-input" type="radio" name="payment_method"
                                                                id="inlineRadio1" value="cod" checked required />
                                                            <label class="form-check-label d-flex align-items-center gap-2" for="inlineRadio1">
                                                                <i class="fa fa-truck text-success" style="font-size:20px;"></i>
                                                                <span>(ডেলিভারির সময় পেমেন্ট)</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if($hasAdvance)
                                                    <div class="alert alert-info py-2 px-3 mb-3">
                                                        এই অর্ডারে মোট <b>৳ {{ number_format($advance_amount,2) }}</b> অগ্রিম দিতে হবে।
                                                        আপনি নিচের যে কোন Online Payment পদ্ধতিতে এখনই এই টাকা পরিশোধ করবেন।
                                                        বাকি <b>৳ <span id="dueAmountText">{{ number_format($due_amount,2) }}</span></b> ডেলিভারির সময় পরিশোধ করবেন।
                                                    </div>
                                                @endif

                                                @if($bkash_gateway)
                                                    {{-- 🟣 Bkash --}}
                                                    <div class="col-md-6 col-12">
                                                        <div class="form-check p_bkash">
                                                            <input class="form-check-input" type="radio" name="payment_method"
                                                                id="inlineRadio2" value="bkash" />
                                                            <label class="form-check-label d-flex align-items-center gap-2" for="inlineRadio2">
                                                                <img src="{{ asset('frontEnd/images/bkash.svg') }}"
                                                                    alt="Bkash Logo"
                                                                    style="height:24px; width:auto; margin-right:6px;">
                                                                <span>বিকাশ পেমেন্ট</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if($shurjopay_gateway)
                                                    {{-- 🟠 ShurjoPay --}}
                                                    <div class="col-md-6 col-12">
                                                        <div class="form-check p_shurjo">
                                                            <input class="form-check-input" type="radio" name="payment_method"
                                                                id="inlineRadio3" value="shurjopay" required />
                                                            <label class="form-check-label d-flex align-items-center gap-2" for="inlineRadio3">
                                                                <img src="{{ asset('frontEnd/images/shurjoPay.png') }}"
                                                                    alt="ShurjoPay"
                                                                    style="height:24px; width:auto; margin-right:6px;">
                                                                <span>ShurjoPay</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if($uddoktapay_gateway)
                                                    {{-- 🔵 UddoktaPay --}}
                                                    <div class="col-md-6 col-12">
                                                        <div class="form-check p_uddokta mb-2">
                                                            <input class="form-check-input" type="radio" name="payment_method"
                                                                id="inlineRadio4" value="uddoktapay" required />
                                                            <label class="form-check-label d-flex align-items-center gap-2" for="inlineRadio4">
                                                                <img src="{{ asset('frontEnd/images/uddokta.png') }}"
                                                                    alt="UddoktaPay"
                                                                    style="height:22px; margin-right:6px;">
                                                                
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endif

                                            </div>
                                        </div>

                                        <!-- ================== Custom CSS ================== -->
                                        <style>
                                            .form-check {
                                                background: #f9f9f9;
                                                border: 1px solid #ddd;
                                                border-radius: 8px;
                                                padding: 10px 14px;
                                                margin-bottom: 10px;
                                                transition: all 0.3s ease;
                                            }
                                            .form-check:hover {
                                                background: #eefaf0;
                                                border-color: #4DBC60;
                                            }
                                            .form-check-input {
                                                transform: scale(1.1);
                                                margin-right: 10px;
                                            }
                                            .form-check-input:checked {
                                                background-color: #4DBC60;
                                                border-color: #4DBC60;
                                            }
                                            .form-check-label {
                                                font-weight: 500;
                                                display: flex;
                                                align-items: center;
                                                gap: 8px;
                                            }
                                            .form-check-label img {
                                                object-fit: contain;
                                            }
                                            @media (max-width: 767px) {
                                                .form-check-label span {
                                                    font-size: 14px;
                                                }
                                            }
                                        </style>
                                    </div>

                                    {{-- Submit --}}
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <button class="order_place" type="submit">অর্ডার করুন</button>
                                        </div>
                                    </div>
                                </div> {{-- .row --}}
                            </div> {{-- .card-body --}}
                        </div> {{-- .card --}}
                    </form>
                </div>
            </div>

            {{-- Right side cart --}}
            <div class="col-sm-7 cust-order-1">
                <div class="cart_details table-responsive-sm">
                    <div class="card">
                        <div class="card-header">
                            <h5>অর্ডারের তথ্য</h5>
                        </div>

                        {{-- 🧾 Coupon Apply Section --}}
                        <div class="coupon-section border rounded mb-3 p-3">
                            <h6 class="fw-bold mb-2">🎟️ কুপন কোড ব্যবহার করুন</h6>

                            @if(!Session::has('coupon_code'))
                                <form action="{{ route('coupon.apply') }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    <input type="text" name="coupon_code" class="form-control" placeholder="Coupon code দিন..." required>
                                    <button class="btn btn-success px-4 fw-semibold">Apply</button>
                                </form>
                            @else
                                <div class="alert alert-success d-flex justify-content-between align-items-center mb-0 shadow-sm">
                                    <div>
                                        <span class="fw-semibold">🎉 Coupon <b>{{ Session::get('coupon_code') }}</b> Applied!</span><br>
                                        Discount: <b>৳{{ Session::get('discount') }}</b>
                                    </div>
                                    <a href="{{ route('coupon.remove') }}" class="text-danger small fw-bold text-decoration-none">Remove ✖</a>
                                </div>
                            @endif
                        </div>

                        <style>
                            .coupon-section {
                                background: #f9fafb;
                                border: 1px solid #e2e6ea;
                                border-radius: 8px;
                                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
                            }
                            .coupon-section h6 {
                                font-size: 15px;
                                color: #333;
                            }
                            .coupon-section form input {
                                border: 1px solid #d1d5db;
                                border-radius: 6px;
                                font-size: 14px;
                            }
                            .coupon-section form input:focus {
                                border-color: #4DBC60;
                                box-shadow: 0 0 0 0.15rem rgba(77,188,96,0.25);
                            }
                            .coupon-section .btn-success {
                                background: #4DBC60;
                                border-color: #4DBC60;
                                transition: all 0.3s ease;
                            }
                            .coupon-section .btn-success:hover {
                                background: #3ca752;
                            }
                            .alert-success {
                                background-color: #e9f8ec;
                                border: 1px solid #cce8d3;
                                color: #256029;
                                border-radius: 6px;
                                padding: 10px 14px;
                                font-size: 14px;
                            }
                            .alert-success a {
                                color: #c0392b !important;
                            }
                            .alert-success a:hover {
                                text-decoration: underline;
                            }
                        </style>

                        <div class="card-body cartlist">
                            <input type="hidden" id="js_subtotal" value="{{ $subtotal }}">
                            <table class="cart_table table table-bordered table-striped text-center mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 20%;">ডিলিট</th>
                                        <th style="width: 40%;">প্রোডাক্ট</th>
                                        <th style="width: 20%;">পরিমাণ</th>
                                        <th style="width: 20%;">মূল্য</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (Cart::instance('shopping')->content() as $value)
                                        <tr>
                                            <td>
                                                <a class="cart_remove" data-id="{{ $value->rowId }}">
                                                    <i class="fas fa-trash text-danger"></i>
                                                </a>
                                            </td>
                                            <td class="text-left">
                                                <a href="{{ route('product', $value->options->slug) }}">
                                                    <img src="{{ asset($value->options->image) }}" />
                                                    {{ Str::limit($value->name, 20) }}
                                                </a>
                                                @if ($value->options->product_size)
                                                    <p>Size: {{ $value->options->product_size }}</p>
                                                @endif
                                                @if ($value->options->product_color)
                                                    <p>Color: {{ $value->options->product_color }}</p>
                                                @endif
                                            </td>
                                            <td>{{ $value->qty }}</td>
                                            <td><span class="alinur">৳</span> <strong>{{ $value->price }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end px-4">মোট</th>
                                        <td class="px-4" id="subtotalAmount">৳ {{ number_format($subtotal,2) }}</td>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-end px-4">ডেলিভারি চার্জ</th>
                                        <td class="px-4" id="shippingAmount">৳ {{ number_format($shipping,2) }}</td>
                                    </tr>
                                    @if($discount > 0)
                                        <tr>
                                            <th colspan="3" class="text-end px-4">কুপন ছাড়</th>
                                            <td id="discountAmount">-৳ {{ number_format($discount,2) }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th colspan="3" class="text-end px-4">সর্বমোট</th>
                                        <td><b id="grandTotalAmount">৳ {{ number_format($grand_total,2) }}</b></td>
                                    </tr>

                                    @if($hasAdvance)
                                        <tr>
                                            <th colspan="3" class="text-end px-4">এখন অগ্রিম পরিশোধ করবেন</th>
                                            <td class="px-4 text-success">
                                                <b id="advanceAmountCell">৳ {{ number_format($advance_amount,2) }}</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th colspan="3" class="text-end px-4">ডেলিভারির সময় দিতে হবে</th>
                                            <td class="px-4 text-danger">
                                                <b id="dueAmountCell">৳ {{ number_format($due_amount,2) }}</b>
                                            </td>
                                        </tr>
                                    @endif
                                </tfoot>
                            </table>
                        </div> {{-- .card-body --}}
                    </div> {{-- .card --}}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('script')
<script src="{{ asset('frontEnd/js/select2.min.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        $(".select2").select2();

        const form = document.querySelector('#checkout-form');
        if(!form) return;

        // 🔹 Helper: Format number to 2 decimal places
        function formatAmount(amount) {
            return parseFloat(amount).toFixed(2);
        }

        // 🔹 Helper: Clean amount string to float
        function cleanAmount(val) {
            if (typeof val === 'number') return val;
            if (!val) return 0;
            // Remove everything except digits and dot
            let cleaned = val.toString().replace(/[^\d.]/g, '');
            return parseFloat(cleaned) || 0;
        }

        // 🔹 Delivery area change (jQuery used for Select2 compatibility)
        $('#area').on('change', function () {
            const selectedOption = $(this).find(':selected');
            const newShipping    = cleanAmount(selectedOption.data('charge'));

            // ✅ সাবটোটাল সব সময় তাজা (DOM থেকে নেওয়া হচ্ছে)
            const subtotal       = cleanAmount($('#js_subtotal').val());
            const discount       = cleanAmount("{{ $discount }}");
            const advanceAmount  = cleanAmount("{{ $advance_amount }}");
            const hasAdvance     = @json($hasAdvance);

            const grandTotal     = subtotal + newShipping - discount;
            const dueAmount      = hasAdvance ? (grandTotal - advanceAmount) : 0;

            // DOM আপডেট
            $('#shippingAmount').text('৳ ' + formatAmount(newShipping));
            $('#subtotalAmount').text('৳ ' + formatAmount(subtotal));
            $('#grandTotalAmount').text('৳ ' + formatAmount(grandTotal));

            if (hasAdvance) {
                $('#advanceAmountCell').text('৳ ' + formatAmount(advanceAmount));
                $('#dueAmountCell').text('৳ ' + formatAmount(dueAmount));
                $('#dueAmountText').text(formatAmount(dueAmount));
            }

            // Sync total amount for tracking
            currentGrandTotal = grandTotal;

            // Update session on server (fire and forget)
            $.get('{{ route("shipping.charge") }}', {id: $(this).val()});
        });

        // Initial setup for tracking
        const cartItems       = @json($cartItemsForJs);
        const initialSubtotal = parseFloat("{{ (float)$subtotal }}") || 0;
        const initialShipping = parseFloat("{{ (float)$shipping }}") || 0;
        const initialDiscount = parseFloat("{{ (float)$discount }}") || 0;
        let currentGrandTotal = initialSubtotal + initialShipping - initialDiscount;

        let timer;
        let isSubmitting = false; // ✅ ফর্ম সাবমিট হলে true হবে

        function saveIncompleteOrder() {
            if (isSubmitting) return; // ✅ সাবমিটের সময় আর ইনকমপ্লিট সেভ হবে না

            clearTimeout(timer);
            timer = setTimeout(() => {
                const name    = form.querySelector('input[name="name"]').value.trim();
                const phone   = form.querySelector('input[name="phone"]').value.trim();
                const address = form.querySelector('input[name="address"]').value.trim();

                if(!name && !phone && !address) return;

                const payload = {
                    name, phone, address,
                    items: cartItems,
                    product_image: cartItems[0]?.image || '',
                    product_link:  cartItems[0]?.link || '',
                    total_amount: currentGrandTotal.toFixed(2)
                };

                fetch('{{ route("incomplete.order.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => console.log('✅ Incomplete Order Saved:', data))
                .catch(err => console.error('❌ Error:', err));
            }, 2000);
        }

        form.addEventListener('input', saveIncompleteOrder);
        form.addEventListener('change', saveIncompleteOrder);

        // ✅ ফর্ম সাবমিট হলে ইনকমপ্লিট সেভ বন্ধ
        form.addEventListener('submit', function () {
            isSubmitting = true;
            clearTimeout(timer);
        });
    });
</script>

{{-- 🔹 GA4 + Facebook Pixel Tracking for Checkout --}}
<script type="text/javascript">
    window.dataLayer = window.dataLayer || [];

    (function () {
        const items        = @json($cartItemsForJs);
        const hasAdvance   = @json($hasAdvance);
        const advanceAmount= parseFloat("{{ $advance_amount }}") || 0;
        const grandTotal   = parseFloat("{{ $grand_total }}") || 0;
        const payableNow   = hasAdvance ? advanceAmount : grandTotal;
        const coupon       = @json(Session::get('coupon_code', null));

        const ga4Items = items.map(function (item, index) {
            return {
                item_id: String(item.id),
                item_name: item.name,
                quantity: Number(item.qty),
                price: Number(item.price),
                index: index
            };
        });

        // GA4: begin_checkout
        if (ga4Items.length) {
            window.dataLayer.push({ ecommerce: null });
            window.dataLayer.push({
                event: "begin_checkout",
                ecommerce: {
                    currency: "BDT",
                    value: payableNow,
                    coupon: coupon,
                    items: ga4Items
                }
            });
        }

        // Facebook Pixel: InitiateCheckout
        if (typeof fbq === "function" && items.length) {
            fbq("track", "InitiateCheckout", {
                value: payableNow,
                currency: "BDT",
                num_items: items.length,
                content_ids: items.map(function(i){ return i.id; }),
                contents: items.map(function(i){
                    return {id: i.id, quantity: i.qty, item_price: i.price};
                }),
                coupon: coupon || undefined
            });
        }

        // On form submit: GA4 add_payment_info + Pixel AddPaymentInfo
        document.addEventListener("DOMContentLoaded", function () {
            var form = document.getElementById("checkout-form");
            if (!form) return;

            form.addEventListener("submit", function () {
                var paymentInput  = form.querySelector('input[name="payment_method"]:checked');
                var paymentMethod = paymentInput ? paymentInput.value : null;

                // GA4 add_payment_info
                window.dataLayer.push({ ecommerce: null });
                window.dataLayer.push({
                    event: "add_payment_info",
                    payment_type: paymentMethod,
                    ecommerce: {
                        currency: "BDT",
                        value: payableNow,
                        coupon: coupon,
                        items: ga4Items
                    }
                });

                // Facebook Pixel: AddPaymentInfo
                if (typeof fbq === "function" && items.length) {
                    fbq("track", "AddPaymentInfo", {
                        value: payableNow,
                        currency: "BDT",
                        payment_method: paymentMethod,
                        num_items: items.length,
                        content_ids: items.map(function(i){ return i.id; })
                    });
                }
            });
        });
    })();
</script>
@endpush
