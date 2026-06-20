@extends('backEnd.layouts.master')
@section('title','Product Price Manage')
@section('content')
<div class="container-fluid">
    
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{route('products.create')}}" class="btn btn-danger rounded-pill">
                        <i class="fe-shopping-cart"></i> Add Product
                    </a>
                </div>
                <h4 class="page-title">Product Price Manage</h4>
            </div>
        </div>
    </div>       
    <!-- end page title --> 

    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{route('products.price_update')}}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover nowrap w-100 align-middle">
                                <thead class="bg-dark text-white">
                                    <tr>
                                        <th style="width:5%">SL</th>
                                        <th style="width:35%">Product Name</th>
                                        <th style="width:15%">Old Price</th>
                                        <th style="width:15%">New Price</th>
                                        <th style="width:15%">Stock</th>
                                    </tr>
                                </thead>               
                            
                                <tbody>
                                    @foreach($products as $key=>$value)
                                    <tr class="table-secondary">
                                        <td>{{ $loop->iteration }}</td>
                                        <input type="hidden" value="{{ $value->id }}" name="ids[]">
                                        <td>
                                            <strong>{{ $value->name }}</strong>
                                            @if($value->variantPrices->count() > 0)
                                                <br><small class="text-muted">
                                                    ({{ $value->variantPrices->count() }} Variants)
                                                </small>
                                            @endif
                                        </td>
                                        <td><input type="text" class="form-control" value="{{ $value->old_price }}" name="old_price[]"></td>
                                        <td><input type="text" class="form-control" value="{{ $value->new_price }}" name="new_price[]"></td>
                                        <td><input type="text" class="form-control" value="{{ $value->stock }}" name="stock[]"></td>
                                    </tr>

                                    {{-- ✅ Variant Rows --}}
                                    @if($value->variantPrices->count() > 0)
                                        @foreach($value->variantPrices as $vkey => $variant)
                                        <tr>
                                            <td></td>
                                            <td class="ps-4">
                                                <span class="badge bg-primary">
                                                    {{ $variant->color?->colorName ?? 'No Color' }}
                                                </span>
                                                <span class="badge bg-info">
                                                    {{ $variant->size?->sizeName ?? 'No Size' }}
                                                </span>
                                                <input type="hidden" name="variant_id[]" value="{{ $variant->id }}">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" 
                                                       name="variant_old_price[]" 
                                                       value="{{ $variant->price }}">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" 
                                                       name="variant_new_price[]" 
                                                       value="{{ $variant->price }}">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" 
                                                       name="variant_stock[]" 
                                                       value="{{ $variant->stock }}">
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td colspan="4"></td>
                                        <td>
                                            <button class="btn btn-success w-100">
                                                <i class="fa fa-save"></i> Update Price
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div> <!-- end card body-->
                </form>
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
</div>

@endsection
