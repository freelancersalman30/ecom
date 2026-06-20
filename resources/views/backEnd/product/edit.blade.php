@extends('backEnd.layouts.master')
@section('title','Product Edit')

@section('css')
<style>
  .increment_btn,
  .remove_btn,
  .btn-warning {
    margin-top: -17px;
    margin-bottom: 10px;
  }
  .edit-image{
    width:70px;
    height:70px;
    object-fit:cover;
    margin-right:5px;
  }
</style>
<link href="{{asset('backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('backEnd')}}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="container-fluid">
  <!-- start page title -->
  <div class="row">
    <div class="col-12">
      <div class="page-title-box">
        <div class="page-title-right">
          <a href="{{route('products.index')}}" class="btn btn-primary rounded-pill">Manage</a>
        </div>
        <h4 class="page-title">Product Edit</h4>
      </div>
    </div>
  </div>
  <!-- end page title -->

  <div class="row justify-content-center">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <form action="{{route('products.update')}}" method="POST" class="row" data-parsley-validate="" enctype="multipart/form-data" name="editForm">
            @csrf
            <input type="hidden" value="{{$edit_data->id}}" name="id" />

            {{-- ================= BASIC INFO ================= --}}
            <div class="col-sm-6">
              <div class="form-group mb-3">
                <label for="name" class="form-label">Product Name *</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror"
                       name="name" value="{{$edit_data->name }}" id="name" required />
                @error('name')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group mb-3">
                <label for="category_id" class="form-label">Categories *</label>
                <select class="form-control form-select select2 @error('category_id') is-invalid @enderror"
                        name="category_id" required>
                  <optgroup>
                    <option value="">Select..</option>
                    @foreach($categories as $category)
                      <option value="{{$category->id}}" @if($edit_data->category_id==$category->id) selected @endif>
                        {{$category->name}}
                      </option>
                      @foreach ($category->childrenCategories as $childCategory)
                        <option value="{{$childCategory->id}}" @if($edit_data->category_id==$childCategory->id) selected @endif>
                          - {{$childCategory->name}}
                        </option>
                      @endforeach
                    @endforeach
                  </optgroup>
                </select>
                @error('category_id')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group mb-3">
                <label for="subcategory_id" class="form-label">SubCategories (Optional)</label>
                <select class="form-control form-select select2-multiple @error('subcategory_id') is-invalid @enderror"
                        id="subcategory_id" name="subcategory_id">
                  <optgroup>
                    <option value="">Select..</option>
                    @foreach($subcategory as $value)
                      <option value="{{$value->id}}" @if($edit_data->subcategory_id==$value->id) selected @endif>
                        {{$value->subcategoryName}}
                      </option>
                    @endforeach
                  </optgroup>
                </select>
                @error('subcategory_id')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group mb-3">
                <label for="childcategory_id" class="form-label">Child Categories (Optional)</label>
                <select class="form-control form-select select2-multiple @error('childcategory_id') is-invalid @enderror"
                        id="childcategory_id" name="childcategory_id">
                  <optgroup>
                    <option value="">Select..</option>
                    @foreach($childcategory as $value)
                      <option value="{{$value->id}}" @if($edit_data->childcategory_id==$value->id) selected @endif>
                        {{$value->childcategoryName}}
                      </option>
                    @endforeach
                  </optgroup>
                </select>
                @error('childcategory_id')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
              </div>
            </div>

            <div class="col-sm-4">
              <div class="form-group mb-3">
                <label for="brand_id" class="form-label">Brands</label>
                <select class="form-control select2 @error('brand_id') is-invalid @enderror"
                        name="brand_id">
                  <option value="">Select..</option>
                  @foreach($brands as $value)
                    <option value="{{$value->id}}" @if($edit_data->brand_id==$value->id) selected @endif>
                      {{$value->name}}
                    </option>
                  @endforeach
                </select>
                @error('brand_id')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
              </div>
            </div>

            <div class="col-sm-4">
              <div class="form-group mb-3">
                <label for="purchase_price" class="form-label">Purchase Price *</label>
                <input type="text" class="form-control @error('purchase_price') is-invalid @enderror"
                       name="purchase_price" value="{{ $edit_data->purchase_price}}" id="purchase_price" required />
                @error('purchase_price')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
              </div>
            </div>

            <div class="col-sm-4">
              <div class="form-group mb-3">
                <label for="old_price" class="form-label">Old Price *</label>
                <input type="text" class="form-control @error('old_price') is-invalid @enderror"
                       name="old_price" value="{{ $edit_data->old_price }}" id="old_price" />
                @error('old_price')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
              </div>
            </div>

            <div class="col-sm-4">
              <div class="form-group mb-3">
                <label for="new_price" class="form-label">New Price *</label>
                <input type="text" class="form-control @error('new_price') is-invalid @enderror"
                       name="new_price" value="{{ $edit_data->new_price }}" id="new_price" required />
                @error('new_price')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
              </div>
            </div>

            <div class="col-sm-4">
              <div class="form-group mb-3">
                <label for="stock" class="form-label">Stock *</label>
                <input type="text" class="form-control @error('stock') is-invalid @enderror"
                       name="stock" value="{{ $edit_data->stock }}" id="stock" />
                @error('stock')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
              </div>
            </div>

            {{-- ================= IMAGE ================= --}}
            <div class="col-sm-4 mb-3">
              <label for="image">Image *</label>
              <div class="input-group control-group increment">
                <input type="file" name="image[]" class="form-control @error('image') is-invalid @enderror" />
                <div class="input-group-btn">
                  <button class="btn btn-success btn-increment" type="button"><i class="fa fa-plus"></i></button>
                </div>
                @error('image')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
              </div>

              <div class="clone hide" style="display: none;">
                <div class="control-group input-group">
                  <input type="file" name="image[]" class="form-control" />
                  <div class="input-group-btn">
                    <button class="btn btn-danger" type="button"><i class="fa fa-trash"></i></button>
                  </div>
                </div>
              </div>

              <div class="product_img mt-2">
                @foreach($edit_data->images as $image)
                  <img src="{{asset($image->image)}}" class="edit-image border" alt="">
                  <a href="{{route('products.image.destroy',['id'=>$image->id])}}"
                     class="btn btn-xs btn-danger waves-effect waves-light">
                    <i class="mdi mdi-close"></i>
                  </a>
                @endforeach
              </div>
            </div>

            {{-- =============== PRODUCT UNIT & VIDEO =============== --}}
            <div class="col-sm-6">
              <div class="form-group mb-3">
                <label for="pro_unit" class="form-label">Product Unit (Optional)</label>
                <input type="text" class="form-control @error('pro_unit') is-invalid @enderror"
                       name="pro_unit" value="{{ $edit_data->pro_unit }}" id="pro_unit" />
                @error('pro_unit')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group mb-3">
                <label for="pro_video" class="form-label">Product Video (Optional)</label>
                <input type="text" class="form-control @error('pro_video') is-invalid @enderror"
                       name="pro_video" value="{{ $edit_data->pro_video }}" id="pro_video" />
                @error('pro_video')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
              </div>
            </div>

            {{-- ================================
            ⭐ PRODUCT TYPE (EDIT)
            ================================ --}}
            @php
              $currentType = old('product_type', $edit_data->is_digital ? 'digital' : 'physical');
              $isDigital   = $currentType === 'digital';
            @endphp

            <div class="col-sm-6">
              <div class="form-group mb-3">
                <label for="product_type" class="form-label">Product Type</label>
                <select class="form-control" id="product_type" name="product_type">
                  <option value="physical" {{ $currentType === 'physical' ? 'selected' : '' }}>Physical Product</option>
                  <option value="digital"  {{ $currentType === 'digital'  ? 'selected' : '' }}>Digital Product</option>
                </select>
              </div>
            </div>

            {{-- ⭐ ADVANCE PAYMENT (PHYSICAL ONLY) --}}
            <div class="col-sm-6" id="advance_area">
              <div class="form-group mb-3">
                <label for="advance_amount" class="form-label">Advance Payment Amount</label>
                <input type="text"
                       class="form-control @error('advance_amount') is-invalid @enderror"
                       name="advance_amount"
                       id="advance_amount"
                       value="{{ old('advance_amount', $edit_data->advance_amount) }}" />
                @error('advance_amount')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
              </div>
            </div>

            {{-- ⭐ DIGITAL PRODUCT FIELDS --}}
            <div class="row w-100" id="digital_area" style="{{ $isDigital ? '' : 'display:none;' }}">

              {{-- LEFT --}}
              <div class="col-sm-6">
                @if($edit_data->digital_file)
                  <div class="form-group mb-2">
                    <label class="form-label d-block">Current Digital File</label>
                    <code>{{ $edit_data->digital_file }}</code>
                  </div>
                @endif

                <div class="form-group mb-3">
                  <label for="digital_file" class="form-label">Change Digital File (ZIP/PDF/SOFTWARE)</label>
                  <input type="file" class="form-control" name="digital_file" id="digital_file">
                  <small class="text-muted">If you upload new file, it will replace old one.</small>
                </div>

                <div class="form-group mb-3">
                  <label for="download_limit" class="form-label">Download Limit</label>
                  <input type="number" class="form-control"
                         name="download_limit" id="download_limit"
                         value="{{ old('download_limit', $edit_data->download_limit ?? 5) }}"
                         min="1">
                </div>
              </div>

              {{-- RIGHT --}}
              <div class="col-sm-6">
                <div class="form-group mb-3">
                  <label for="download_expire_days" class="form-label">Download Expire (Days)</label>
                  <input type="number" class="form-control"
                         name="download_expire_days" id="download_expire_days"
                         value="{{ old('download_expire_days', $edit_data->download_expire_days ?? 7) }}"
                         min="1">
                </div>
              </div>

            </div>

            {{-- ================= VARIANT PRICE ================= --}}
            <hr>
            <div class="col-sm-12 mb-3">
              <h5 class="bg-dark text-white p-2 rounded">💰 Variant Price (Color + Size অনুযায়ী)</h5>

              <div id="variant-wrapper">
                @forelse($edit_data->variantPrices as $key => $variant)
                  <div class="row variant-item align-items-end mb-3">
                    <div class="col-md-3">
                      <label class="form-label">Color</label>
                      <select name="variant_price[{{ $key }}][color_id]" class="form-control select2">
                        <option value="">Select Color</option>
                        @foreach($totalcolors as $color)
                          <option value="{{ $color->id }}" {{ $variant->color_id == $color->id ? 'selected' : '' }}>
                            {{ $color->colorName ?? $color->name }}
                          </option>
                        @endforeach
                      </select>
                    </div>

                    <div class="col-md-3">
                      <label class="form-label">Size</label>
                      <select name="variant_price[{{ $key }}][size_id]" class="form-control select2">
                        <option value="">Select Size</option>
                        @foreach($totalsizes as $size)
                          <option value="{{ $size->id }}" {{ $variant->size_id == $size->id ? 'selected' : '' }}>
                            {{ $size->sizeName ?? $size->name }}
                          </option>
                        @endforeach
                      </select>
                    </div>

                    <div class="col-md-2">
                      <label class="form-label">Price</label>
                      <input type="number" step="0.01" name="variant_price[{{ $key }}][price]"
                             value="{{ $variant->price }}" class="form-control" placeholder="Enter Price">
                    </div>

                    <div class="col-md-2">
                      <label class="form-label">Stock</label>
                      <input type="number" name="variant_price[{{ $key }}][stock]"
                             value="{{ $variant->stock }}" class="form-control" placeholder="Enter Stock">
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                      @if($loop->first)
                        <button type="button" class="btn btn-success add-variant" style="margin-top:5px;">
                          <i class="fa fa-plus"></i>
                        </button>
                      @else
                        <button type="button" class="btn btn-danger remove-variant" style="margin-top:5px;">
                          <i class="fa fa-trash"></i>
                        </button>
                      @endif
                    </div>
                  </div>
                @empty
                  <div class="row variant-item align-items-end mb-3">
                    <div class="col-md-3">
                      <label class="form-label">Color</label>
                      <select name="variant_price[0][color_id]" class="form-control select2">
                        <option value="">Select Color</option>
                        @foreach($totalcolors as $color)
                          <option value="{{ $color->id }}">{{ $color->colorName ?? $color->name }}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="col-md-3">
                      <label class="form-label">Size</label>
                      <select name="variant_price[0][size_id]" class="form-control select2">
                        <option value="">Select Size</option>
                        @foreach($totalsizes as $size)
                          <option value="{{ $size->id }}">{{ $size->sizeName ?? $size->name }}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="col-md-2">
                      <label class="form-label">Price</label>
                      <input type="number" step="0.01" name="variant_price[0][price]"
                             class="form-control" placeholder="Enter Price">
                    </div>

                    <div class="col-md-2">
                      <label class="form-label">Stock</label>
                      <input type="number" name="variant_price[0][stock]"
                             class="form-control" placeholder="Enter Stock">
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                      <button type="button" class="btn btn-success add-variant" style="margin-top:5px;">
                        <i class="fa fa-plus"></i>
                      </button>
                    </div>
                  </div>
                @endforelse
              </div>
            </div>

            {{-- ================= DESCRIPTION / NOTE ================= --}}
            <div class="col-sm-12 mb-3">
              <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" rows="6"
                          class="summernote form-control @error('description') is-invalid @enderror">
                  {{$edit_data->description}}
                </textarea>
                @error('description')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
              </div>
            </div>

            <div class="col-sm-12 mb-3">
              <div class="form-group">
                <label for="note" class="form-label">Note</label>
                <textarea name="note" rows="6"
                          class="form-control @error('note') is-invalid @enderror">{{$edit_data->note}}</textarea>
                @error('note')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
              </div>
            </div>

            {{-- ================= SEO ================= --}}
            <hr>
            <div class="col-sm-12 mb-3">
              <div class="form-group">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <h5 class="mb-0 text-white bg-dark p-2 rounded" style="width:100%;">🔍 SEO Configuration</h5>
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="meta_title" class="form-label">Meta Title</label>
                    <input type="text" name="meta_title" id="meta_title" class="form-control"
                           value="{{ $edit_data->meta_title ?? $edit_data->name }}"
                           placeholder="Enter meta title">
                  </div>

                  <div class="col-md-6 mb-3">
                    <label for="meta_keywords" class="form-label">Meta Keywords</label>
                    <input type="text" name="meta_keywords" id="meta_keywords" class="form-control"
                           value="{{ $edit_data->meta_keywords ?? '' }}"
                           placeholder="meta1, meta2, meta3">
                  </div>

                  <div class="col-md-12 mb-3">
                    <label for="meta_description" class="form-label">Meta Description</label>
                    <textarea name="meta_description" id="meta_description" class="form-control" rows="3"
                              placeholder="Enter short SEO description...">
                      {{ $edit_data->meta_description ?? \Illuminate\Support\Str::limit(strip_tags($edit_data->description), 160) }}
                    </textarea>
                  </div>

                  <div class="col-md-6 mb-3">
                    <label for="meta_image" class="form-label">Meta Image (og:image)</label>
                    <input type="file" name="meta_image" id="meta_image" class="form-control">

                    @if(!empty($edit_data->meta_image))
                      <div class="mt-2">
                        <img src="{{ asset($edit_data->meta_image) }}" alt="Meta Image"
                             class="border rounded" width="120">
                      </div>
                    @endif

                    <small class="text-muted d-block mt-1">Recommended size: 1200x630px</small>
                  </div>
                </div>
              </div>
            </div>

            {{-- ================= STATUS FLAGS ================= --}}
            <div class="col-sm-3 mb-3">
              <div class="form-group mb-3">
                <label for="sold" class="form-label">Sold</label>
                <input type="text" class="form-control @error('sold') is-invalid @enderror"
                       name="sold" value="{{ $edit_data->sold }}" id="sold" />
                @error('sold')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
              </div>
            </div>

            <div class="col-sm-3 mb-3">
              <div class="form-group">
                <label for="status" class="d-block">Status</label>
                <label class="switch">
                  <input type="checkbox" value="1" name="status" @if($edit_data->status==1) checked @endif>
                  <span class="slider round"></span>
                </label>
                @error('status')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
              </div>
            </div>

            <div class="col-sm-3 mb-3">
              <div class="form-group">
                <label for="topsale" class="d-block">Hot Deals</label>
                <label class="switch">
                  <input type="checkbox" value="1" name="topsale" @if($edit_data->topsale==1) checked @endif>
                  <span class="slider round"></span>
                </label>
                @error('topsale')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
              </div>
            </div>

            <div class="col-sm-3 mb-3">
              <div class="form-group">
                <label for="flashsale" class="d-block">Flash Sales</label>
                <label class="switch">
                  <input type="checkbox" value="1" name="flashsale" @if($edit_data->flashsale==1) checked @endif>
                  <span class="slider round"></span>
                </label>
                @error('flashsale')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
              </div>
            </div>

            <div>
              <input type="submit" class="btn btn-success" value="Submit" />
            </div>

          </form>
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

<script>
  $(document).ready(function () {
    $(".btn-increment").click(function () {
      var html = $(".clone").html();
      $(".increment").after(html);
    });
    $("body").on("click", ".btn-danger", function () {
      $(this).parents(".control-group").remove();
    });

    $(".select2").select2();
  });
</script>

<script>
  // Category to subcategory & childcategory
  $("#category_id").on("change", function () {
    var ajaxId = $(this).val();
    if (ajaxId) {
      $.ajax({
        type: "GET",
        url: "{{url('ajax-product-subcategory')}}?category_id=" + ajaxId,
        success: function (res) {
          if (res) {
            $("#subcategory_id").empty();
            $("#subcategory_id").append('<option value="0">Choose...</option>');
            $.each(res, function (key, value) {
              $("#subcategory_id").append('<option value="' + key + '">' + value + "</option>");
            });
          } else {
            $("#subcategory_id").empty();
          }
        },
      });
    } else {
      $("#subcategory_id").empty();
    }
  });

  $("#subcategory_id").on("change", function () {
    var ajaxId = $(this).val();
    if (ajaxId) {
      $.ajax({
        type: "GET",
        url: "{{url('ajax-product-childcategory')}}?subcategory_id=" + ajaxId,
        success: function (res) {
          if (res) {
            $("#childcategory_id").empty();
            $("#childcategory_id").append('<option value="0">Choose...</option>');
            $.each(res, function (key, value) {
              $("#childcategory_id").append('<option value="' + key + '">' + value + "</option>");
            });
          } else {
            $("#childcategory_id").empty();
          }
        },
      });
    } else {
      $("#childcategory_id").empty();
    }
  });

  // Set selected values on load
  document.forms["editForm"].elements["category_id"].value = "{{$edit_data->category_id}}";
  document.forms["editForm"].elements["subcategory_id"].value = "{{$edit_data->subcategory_id}}";
  document.forms["editForm"].elements["childcategory_id"].value = "{{$edit_data->childcategory_id}}";
</script>

{{-- Variant add/remove --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  let variantIndex = {{ $edit_data->variantPrices->count() ?? 1 }};
  $('.select2').select2();

  document.body.addEventListener('click', function (e) {
    const target = e.target.closest('.add-variant, .remove-variant');
    if (!target) return;

    if (target.classList.contains('add-variant')) {
      const wrapper = document.getElementById('variant-wrapper');
      const firstRow = wrapper.querySelector('.variant-item');
      if (!firstRow) return;

      const newRow = $(firstRow.cloneNode(true));
      newRow.find('.select2-container').remove();

      newRow.find('input, select').each(function () {
        const oldName = $(this).attr('name');
        if (oldName) {
          $(this).attr('name', oldName.replace(/\[\d+\]/, '[' + variantIndex + ']'));
        }
        $(this).val('');
      });

      newRow.find('.add-variant')
        .removeClass('btn-success add-variant')
        .addClass('btn-danger remove-variant')
        .html('<i class="fa fa-trash"></i>');

      newRow.appendTo(wrapper);

      setTimeout(() => {
        newRow.find('select.select2').select2({
          width: '100%',
          dropdownParent: $('#variant-wrapper')
        });
      }, 100);

      variantIndex++;
    }

    if (target.classList.contains('remove-variant')) {
      target.closest('.variant-item').remove();
    }
  });
});
</script>

{{-- Product type toggle --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    function toggleFields() {
        let type = document.getElementById('product_type').value;
        if (type === 'digital') {
            document.getElementById('digital_area').style.display = 'flex';
            document.getElementById('advance_area').style.display = 'none';
        } else {
            document.getElementById('digital_area').style.display = 'none';
            document.getElementById('advance_area').style.display = 'block';
        }
    }

    document.getElementById('product_type').addEventListener('change', toggleFields);
    toggleFields(); // initial
});
</script>
@endsection
