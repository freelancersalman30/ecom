@extends('frontEnd.layouts.master') 
@section('title',$keyword) 
@push('css')
<link rel="stylesheet" href="{{asset('frontEnd/css/jquery-ui.css')}}" />
@endpush 
@section('content')
<section class="product-section">
    <div class="container">
        <div class="sorting-section">
            <div class="row">
                <div class="col-sm-6">
                    <div class="category-breadcrumb d-flex align-items-center">
                        <a href="{{ route('home') }}">Home</a>
                        <span>/</span>
                        <strong>{{ $keyword }}</strong>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="showing-data">
                                <span>Showing {{ $products->firstItem() }}-{{ $products->lastItem() }} of {{ $products->total() }} Results</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mobile-filter-toggle">
                                <i class="fa fa-list-ul"></i><span>filter</span>
                            </div>
                            <div class="page-sort">
                                <form action="" class="sort-form">
                                    <select name="sort" class="form-control form-select sort">
                                        <option value="1" @if(request()->get('sort')==1)selected @endif>Product: Latest</option>
                                        <option value="2" @if(request()->get('sort')==2)selected @endif>Product: Oldest</option>
                                        <option value="3" @if(request()->get('sort')==3)selected @endif>Price: High To Low</option>
                                        <option value="4" @if(request()->get('sort')==4)selected @endif>Price: Low To High</option>
                                        <option value="5" @if(request()->get('sort')==5)selected @endif>Name: A-Z</option>
                                        <option value="6" @if(request()->get('sort')==6)selected @endif>Name: Z-A</option>
                                    </select>
                                    <input type="hidden" name="min_price" value="{{request()->get('min_price')}}" />
                                    <input type="hidden" name="max_price" value="{{request()->get('max_price')}}" />
                                </form>
                            </div>
                            
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-sm-12">
                <div class="category-product main_product_inner">
                    @foreach($products as $key=>$value)
                    <div class="product_item wist_item wow zoomIn" data-wow-duration="1.5s"
                            data-wow-delay="0.{{ $key }}s">
                            <div class="product_item_inner">
                                @if($value->old_price)
                                <div class="sale-badge">
                                    <div class="sale-badge-inner">
                                        <div class="sale-badge-box">
                                            <span class="sale-badge-text">
                                                <p>@php $discount=(((($value->old_price)-($value->new_price))*100) / ($value->old_price)) @endphp {{ number_format($discount, 0) }}%</p>
                                                ছাড়
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="pro_img">
                                    <a href="{{ route('product', $value->slug) }}">
                                        <img src="{{ asset($value->image ? $value->image->image : '') }}"
                                            alt="{{ $value->name }}" />
                                    </a>
                                </div>
                                <div class="pro_des">
                                    <div class="pro_name">
                                        <a
                                            href="{{ route('product', $value->slug) }}">{{ Str::limit($value->name, 35) }}</a>
                                    </div>
                                </div>
                            </div>
                            @php
                                $averageRating = $value->reviews->avg('ratting'); 
                                $filledStars = floor($averageRating);
                                $hasHalfStar = $averageRating - $filledStars >= 0.5;
                                $emptyStars = 5 - $filledStars - ($hasHalfStar ? 1 : 0);
                            @endphp

                            @if ($averageRating >= 0 && $averageRating <= 5)
                                {{-- Filled stars --}}
                                @for ($i = 0; $i < $filledStars; $i++)
                                    <i class="fas fa-star"></i>
                                @endfor

                                {{-- Half star --}}
                                @if ($hasHalfStar)
                                    <i class="fas fa-star-half-alt"></i>
                                @endif

                                {{-- Empty stars --}}
                                @for ($i = 0; $i < $emptyStars; $i++)
                                    <i class="far fa-star"></i>
                                @endfor
                            @else
                                <span>Invalid rating range</span>
                            @endif

                             <div class="pro_price">
                                <p>
                                    <del>৳ {{ $value->old_price }}</del>
                                    ৳ {{ $value->new_price }} @if ($value->old_price)
                                    @endif
                                </p>
                            </div>
                            @if (!$value->prosizes->isEmpty() || !$value->procolors->isEmpty())
                            <div class="pro_btn">
                                <div class="cart_btn order_button">
                                    <a href="{{ route('product', $value->slug) }}"
                                        class="addcartbutton">
                                        <span>অর্ডার করুন</span>
                                    </a>
                                </div>
                               
                            </div>
                            @else
                            <div class="pro_btn">
                                <div class="cart_btn order_button">
                                    <a class="addcartbutton" data-id="{{ $value->id }}" data-checkout="yes">
                                        <span>অর্ডার করুন</span>
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="custom_paginate">
                    {{$products->links('pagination::bootstrap-4')}}
                   
                </div>
            </div>
        </div>
    </div>
</section>


@endsection
@push('script')
<script>
    $(".sort").change(function(){
       $('#loading').show();
       $(".sort-form").submit();
    })
</script>

{{-- 🔹 GA4 DataLayer + Facebook Pixel for Search Results --}}
<script type="text/javascript">
    window.dataLayer = window.dataLayer || [];

    (function () {
        var searchTerm = @json($keyword);
        var listId = "search_results";
        var listName = "Search - " + searchTerm;

        var listItems = [
            @foreach($products as $index => $value)
            {
                item_id: "{{ $value->id }}",
                item_name: @json($value->name),
                price: {{ (float) $value->new_price }},
                item_brand: @json(optional($value->brand)->name),
                item_category: @json(optional($value->category)->name),
                item_list_id: listId,
                item_list_name: listName,
                index: {{ $loop->iteration }},
                slug: @json($value->slug),
                currency: "BDT"
            }@if(!$loop->last),@endif
            @endforeach
        ];

        // GA4: search event
        window.dataLayer.push({
            event: "search",
            search_term: searchTerm
        });

        // GA4: view_item_list for search results
        if (listItems.length) {
            window.dataLayer.push({ ecommerce: null });
            window.dataLayer.push({
                event: "view_item_list",
                search_term: searchTerm,
                ecommerce: {
                    item_list_id: listId,
                    item_list_name: listName,
                    items: listItems.map(function (item) {
                        return {
                            item_id: item.item_id,
                            item_name: item.item_name,
                            index: item.index,
                            price: item.price,
                            item_brand: item.item_brand,
                            item_category: item.item_category,
                            item_list_id: item.item_list_id,
                            item_list_name: item.item_list_name,
                            currency: item.currency
                        };
                    })
                }
            });
        }

        // Facebook Pixel: Search + SearchResultsView (custom)
        if (typeof fbq === "function") {
            fbq("track", "Search", {
                search_string: searchTerm,
                content_category: "search_results",
                currency: "BDT"
            });

            if (listItems.length) {
                fbq("trackCustom", "SearchResultsView", {
                    search_term: searchTerm,
                    content_ids: listItems.map(function (i) { return i.item_id; }),
                    currency: "BDT"
                });
            }
        }

        function findItemByHref(href) {
            if (!href) return null;
            try {
                var parts = href.split("/");
                var last = parts[parts.length - 1].split("?")[0];
                return listItems.find(function (i) { return i.slug === last; }) || null;
            } catch (e) {
                return null;
            }
        }

        // Product click from search result -> select_item + FB event
        $(document).on("click", ".category-product .product_item a", function () {
            var href = $(this).attr("href") || "";
            var item = findItemByHref(href);
            if (!item) return;

            window.dataLayer.push({ ecommerce: null });
            window.dataLayer.push({
                event: "select_item",
                ecommerce: {
                    item_list_id: listId,
                    item_list_name: listName,
                    items: [{
                        item_id: item.item_id,
                        item_name: item.item_name,
                        index: item.index,
                        price: item.price,
                        item_brand: item.item_brand,
                        item_category: item.item_category,
                        item_list_id: item.item_list_id,
                        item_list_name: item.item_list_name,
                        currency: item.currency
                    }]
                }
            });

            if (typeof fbq === "function") {
                fbq("trackCustom", "SearchResultProductClick", {
                    search_term: searchTerm,
                    content_ids: [item.item_id],
                    content_name: item.item_name,
                    content_category: item.item_category,
                    value: item.price,
                    currency: "BDT"
                });
            }
        });

    })();
</script>
@endpush
