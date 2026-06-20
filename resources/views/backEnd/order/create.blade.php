@extends('backEnd.layouts.master')
@section('title','Point of Sale')

@section('css')
<style>
    body{
        background:#eef1f8;
    }
    .pos-shell{
        background:#eef1f8;
        padding:10px 0 25px;
    }
    .pos-card{
        background:#ffffff;
        border-radius:14px;
        box-shadow:0 15px 30px rgba(15,23,42,0.08);
        padding:14px 14px 10px;
        border:1px solid rgba(148,163,184,0.25);
    }
    .pos-header-bar{
        background:linear-gradient(135deg,#4f46e5,#6366f1);
        color:#fff;
        border-radius:12px;
        padding:10px 14px;
        margin-bottom:10px;
        display:flex;
        justify-content:space-between;
        align-items:center;
    }
    .pos-header-bar h5{
        margin:0;
        font-size:16px;
        font-weight:600;
        letter-spacing:.3px;
    }
    .pos-badge-soft{
        padding:3px 10px;
        border-radius:999px;
        background:rgba(15,23,42,.18);
        font-size:12px;
    }

    /* LEFT – CART TABLE */
    .pos-cart-table thead{
        background:#f9fafb;
    }
    .pos-cart-table th{
        font-size:12px;
        text-transform:uppercase;
        letter-spacing:.03em;
        color:#64748b;
        border-bottom:1px solid #e2e8f0;
    }
    .pos-cart-table td{
        vertical-align:middle;
    }

    .qty-cart .quantity{
        display:flex;
        align-items:center;
        justify-content:center;
    }
    .qty-cart .quantity button{
        border:1px solid #cbd5f5;
        background:#e5edff;
        width:28px;
        height:28px;
        border-radius:6px;
        line-height:26px;
        text-align:center;
        padding:0;
        font-weight:600;
        color:#4f46e5;
    }
    .qty-cart .quantity input{
        width:40px;
        text-align:center;
        border:0;
        background:transparent;
        font-weight:600;
    }
    .product_discount{
        max-width:80px;
    }

    /* CUSTOMER + TOTAL CARD */
    .pos-section-title{
        font-size:13px;
        text-transform:uppercase;
        letter-spacing:.12em;
        color:#94a3b8;
        font-weight:600;
        margin-bottom:6px;
    }
    .pos-summary-table td{
        padding:6px 10px;
        font-size:14px;
    }
    .pos-summary-table tr:last-child td{
        border-top:1px dashed #e2e8f0;
        font-size:15px;
        font-weight:700;
    }
    .pos-grand-total{
        font-size:18px !important;
        color:#16a34a;
    }

    .btn-pos-primary{
        background:linear-gradient(135deg,#4f46e5,#6366f1);
        border:none;
        padding:9px 22px;
        border-radius:999px;
        font-weight:600;
        font-size:14px;
        box-shadow:0 10px 20px rgba(79,70,229,.35);
        color:#fff;
    }
    .btn-pos-primary:hover{
        opacity:.94;
        box-shadow:0 16px 30px rgba(79,70,229,.45);
    }

    /* RIGHT – PRODUCTS GRID */
    .pos-products-wrapper{
        max-height:520px;
        overflow-y:auto;
        padding-right:4px;
    }
    .pos-product-card{
        border-radius:12px;
        padding:8px 8px 10px;
        margin-bottom:10px;
        text-align:center;
        cursor:pointer;
        transition:.18s all;
        background:linear-gradient(145deg,#f9fafb,#e5edff);
        border:1px solid rgba(148,163,184,.35);
        position:relative;
    }
    .pos-product-card:hover{
        transform:translateY(-2px);
        box-shadow:0 12px 24px rgba(15,23,42,.15);
    }
    .pos-product-img{
        height:72px;
        object-fit:contain;
        margin-bottom:4px;
    }
    .pos-product-name{
        font-size:13px;
        font-weight:600;
        min-height:34px;
        color:#111827;
    }
    .pos-product-price{
        font-size:14px;
        font-weight:700;
        color:#16a34a;
    }
    .pos-stock-badge{
        position:absolute;
        top:6px;
        left:8px;
        background:rgba(30,64,175,.12);
        color:#1d4ed8;
        font-size:11px;
        padding:2px 6px;
        border-radius:999px;
    }

    .pos-search-bar input{
        border-radius:999px;
        border:1px solid #cbd5f5;
        font-size:13px;
        padding-left:32px;
    }
    .pos-search-bar .icon{
        position:absolute;
        top:50%;
        left:10px;
        transform:translateY(-50%);
        color:#94a3b8;
        font-size:13px;
    }
</style>
<link href="{{asset('backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('backEnd')}}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="container-fluid pos-shell">

    {{-- TOP BAR --}}
    <div class="row mb-2">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Point of Sale</h4>
                <form method="get" action="{{route('admin.order.cart_clear')}}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill delete-confirm" title="Clear Cart">
                        <i class="fas fa-trash-alt"></i> Cart Clear
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- ================= LEFT COLUMN ================= --}}
        <div class="col-lg-7">
            <div class="pos-card h-100">

                {{-- POS HEADER STRIP --}}
                <div class="pos-header-bar mb-3">
                    <div>
                        <h5>Shop Store</h5>
                        <small class="pos-badge-soft">Walk-in Customer POS</small>
                    </div>
                    <div class="text-end">
                        <div style="font-size:12px;opacity:.8;">Session</div>
                        <div style="font-weight:600;">SL-{{ date('dmy-His') }}</div>
                    </div>
                </div>

                {{-- CART TABLE --}}
                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-sm mb-0 pos-cart-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Disc.</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="cartTable">
                            @php $product_discount = 0; @endphp
                            @foreach($cartinfo as $key=>$value)
                                <tr>
                                    <td>
                                        <img height="35" src="{{asset($value->options->image)}}" alt="">
                                    </td>
                                    <td>{{$value->name}}</td>
                                    <td>
                                        <div class="qty-cart vcart-qty">
                                            <div class="quantity">
                                                <button class="minus cart_decrement" value="{{$value->qty}}" data-id="{{$value->rowId}}">-</button>
                                                <input type="text" value="{{$value->qty}}" readonly />
                                                <button class="plus cart_increment" value="{{$value->qty}}" data-id="{{$value->rowId}}">+</button>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{$value->price}}</td>
                                    <td>
                                        <input type="number"
                                               class="product_discount form-control form-control-sm"
                                               value="{{$value->options->product_discount}}"
                                               placeholder="0.00"
                                               data-id="{{$value->rowId}}">
                                    </td>
                                    <td>{{($value->price - $value->options->product_discount)*$value->qty}}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-light btn-sm cart_remove" data-id="{{$value->rowId}}">
                                            <i class="fa fa-times text-danger"></i>
                                        </button>
                                    </td>
                                </tr>
                                @php
                                    $product_discount += $value->options->product_discount*$value->qty;
                                    Session::put('product_discount',$product_discount);
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- CUSTOMER + TOTAL --}}
                <form action="{{route('admin.order.store')}}" method="POST" class="row pos_form" data-parsley-validate="" enctype="multipart/form-data">
                    @csrf

                    {{-- CUSTOMER --}}
                    <div class="col-md-6">
                        <div class="pos-section-title">Customer</div>

                        <div class="mb-2">
                            <input type="text"
                                   id="name"
                                   class="form-control form-control-sm @error('name') is-invalid @enderror"
                                   placeholder="Customer Name"
                                   name="name" required>
                            @error('name')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                        </div>

                        <div class="mb-2">
                            <input type="number"
                                   id="phone"
                                   class="form-control form-control-sm @error('phone') is-invalid @enderror"
                                   placeholder="Mobile Number"
                                   name="phone" required>
                            @error('phone')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                        </div>

                        <div class="mb-2">
                            <input type="text"
                                   id="address"
                                   class="form-control form-control-sm @error('address') is-invalid @enderror"
                                   placeholder="Address"
                                   name="address" required>
                            @error('address')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                        </div>

                        <div class="mb-2">
                            <select id="area"
                                    class="form-control form-control-sm @error('area') is-invalid @enderror"
                                    name="area" required>
                                <option value="">Delivery Area...</option>
                                <option value="1">ঢাকা সিটির ভিতরে হোম ডেলিভারি</option>
                                <option value="2">ঢাকা সিটির বাহিরে হোম ডেলিভারি</option>
                                <option value="3">কুরিয়ার অফিস থেকে ডেলিভারি</option>
                            </select>
                            @error('area')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                    </div>

                    {{-- SUMMARY --}}
                    <div class="col-md-6">
                        <div class="pos-section-title">Summary</div>

                        @php
                            $subtotal = Cart::instance('pos_shopping')->subtotal();
                            $subtotal = str_replace(',','',$subtotal);
                            $subtotal = str_replace('.00', '',$subtotal);
                            $shipping = Session::get('pos_shipping');
                            $total_discount = Session::get('pos_discount')+Session::get('product_discount');
                            $grand = ($subtotal + $shipping)- $total_discount;
                        @endphp

                        <table class="table table-borderless pos-summary-table mb-2" id="cart_details">
                            <tr>
                                <td>Sub Total</td>
                                <td class="text-end">৳{{$subtotal}}</td>
                            </tr>
                            <tr>
                                <td>Shipping Fee</td>
                                <td class="text-end">৳{{$shipping}}</td>
                            </tr>
                            <tr>
                                <td>Discount</td>
                                <td class="text-end">৳{{$total_discount}}</td>
                            </tr>
                            <tr>
                                <td>Grand Total</td>
                                <td class="text-end pos-grand-total">৳{{$grand}}</td>
                            </tr>
                        </table>

                        <div class="text-end mt-1">
                            <button type="submit" class="btn btn-pos-primary">
                                Complete Sale
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        {{-- ================= RIGHT COLUMN – PRODUCT LIST ================= --}}
        <div class="col-lg-5">
            <div class="pos-card h-100">

                {{-- SEARCH BAR --}}
                <div class="mb-2">
                    <div class="pos-section-title">Products</div>
                    <div class="pos-search-bar position-relative">
                        <span class="icon"><i class="fa fa-search"></i></span>
                        <input type="text"
                               id="product_search"
                               class="form-control form-control-sm"
                               placeholder="Search product by name...">
                    </div>
                </div>

                <div class="pos-products-wrapper">
                    <div class="row">
                        @foreach($products as $p)
                            @php $img = optional($p->image)->image ?? 'public/no-image.png'; @endphp
                            <div class="col-6 mb-2 pos-product-wrapper" data-name="{{ strtolower($p->name) }}">
                                <div class="pos-product-card pos-add-product" data-id="{{ $p->id }}">
                                    <span class="pos-stock-badge">Stock: {{ $p->stock ?? 0 }}</span>
                                    <img src="{{ asset($img) }}" class="pos-product-img" alt="">
                                    <div class="pos-product-name">{{ $p->name }}</div>
                                    <div class="pos-product-price">
                                        TK {{ $p->new_price ?? $p->old_price }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection

@section('script')
<script src="{{asset('backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/js/pages/form-advanced.init.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs//summernote/summernote-lite.min.js"></script>

<script>
    $(".summernote").summernote({
        placeholder: "Enter Your Text Here",
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $(".select2").select2();
    });

    // -------- CART CONTENT LOADERS ----------
    function cart_content() {
        $.ajax({
            type: "GET",
            url: "{{route('admin.order.cart_content')}}",
            dataType: "html",
            success: function (cartinfo) {
                $("#cartTable").html(cartinfo);
            },
        });
    }
    function cart_details() {
        $.ajax({
            type: "GET",
            url: "{{route('admin.order.cart_details')}}",
            dataType: "html",
            success: function (cartinfo) {
                $("#cart_details").html(cartinfo);
            },
        });
    }

    // -------- PRODUCT CLICK -> ADD TO CART ----------
    $(document).on("click", ".pos-add-product", function (e) {
        e.preventDefault();
        var id = $(this).data("id");
        if (id) {
            $.ajax({
                cache: false,
                type: "GET",
                data: { id: id },
                url: "{{route('admin.order.cart_add')}}",
                dataType: "json",
                success: function (cartinfo) {
                    cart_content();
                    cart_details();
                },
            });
        }
    });

    // -------- CART QTY + / - (Delegated) ----------
    $(document).on("click", ".cart_increment", function (e) {
        e.preventDefault();
        var id = $(this).data("id");
        var qty = $(this).val();
        if (id) {
            $.ajax({
                cache: false,
                data: { id: id, qty: qty },
                type: "GET",
                url: "{{route('admin.order.cart_increment')}}",
                dataType: "json",
                success: function (cartinfo) {
                    cart_content();
                    cart_details();
                },
            });
        }
    });

    $(document).on("click", ".cart_decrement", function (e) {
        e.preventDefault();
        var id = $(this).data("id");
        var qty = $(this).val();
        if (id) {
            $.ajax({
                cache: false,
                type: "GET",
                data: { id: id, qty: qty },
                url: "{{route('admin.order.cart_decrement')}}",
                dataType: "json",
                success: function (cartinfo) {
                    cart_content();
                    cart_details();
                },
            });
        }
    });

    // -------- CART REMOVE ----------
    $(document).on("click", ".cart_remove", function (e) {
        e.preventDefault();
        var id = $(this).data("id");
        if (id) {
            $.ajax({
                cache: false,
                type: "GET",
                data: { id: id },
                url: "{{route('admin.order.cart_remove')}}",
                dataType: "json",
                success: function (cartinfo) {
                    cart_content();
                    cart_details();
                },
            });
        }
    });

    // -------- PRODUCT DISCOUNT CHANGE ----------
    $(document).on("change", ".product_discount", function () {
        var id = $(this).data("id");
        var discount = $(this).val();
        $.ajax({
            cache: false,
            type: "GET",
            data: { id: id, discount: discount },
            url: "{{route('admin.order.product_discount')}}",
            dataType: "json",
            success: function (cartinfo) {
                cart_content();
                cart_details();
            },
        });
    });

    // -------- SHIPPING CHANGE ----------
    $(document).on("change", "#area", function () {
        var id = $(this).val();
        $.ajax({
            type: "GET",
            data: { id: id },
            url: "{{route('admin.order.cart_shipping')}}",
            dataType: "html",
            success: function (cartinfo) {
                cart_content();
                cart_details();
            },
        });
    });

    // -------- PRODUCT SEARCH (Right side) ----------
    $("#product_search").on("keyup", function () {
        var q = $(this).val().toLowerCase();
        $(".pos-product-wrapper").each(function(){
            var name = $(this).data("name");
            if(name.indexOf(q) !== -1){
                $(this).show();
            }else{
                $(this).hide();
            }
        });
    });

</script>
@endsection
