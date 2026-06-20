@extends('backEnd.layouts.master')
@section('title','General Setting Update')
@section('css')
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
                    <a href="{{route('settings.index')}}" class="btn btn-primary rounded-pill">Manage</a>
                </div>
                <h4 class="page-title">General Setting Update</h4>
            </div>
        </div>
    </div>       
    <!-- end page title --> 
   <div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{route('settings.update')}}" method="POST" class=row data-parsley-validate=""  enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{$edit_data->id}}">
                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $edit_data->name}}" id="name" required="">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
					

					
					<div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="facebook_page_username" class="form-label">Facebook Page Username
*</label>
                            <input type="text" class="form-control @error('facebook_page_username') is-invalid @enderror" name="facebook_page_username" value="{{ $edit_data->facebook_page_username}}" id="facebook_page_username" required="">
                            @error('facebook_page_username')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
					
					<div class="row mt-3">
    <!-- Primary Color -->
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="form-group">
            <label for="primary_color" class="form-label">Primary Color</label>
            <div class="d-flex align-items-center gap-2">
                <input type="color" 
                       name="primary_color" 
                       id="primary_color"
                       value="{{ old('primary_color', $edit_data->primary_color ?? '#0d6efd') }}"
                       class="form-control form-control-color" 
                       style="width: 60px; height: 38px; cursor: pointer;">
                <input type="text"
                       value="{{ old('primary_color', $edit_data->primary_color ?? '#0d6efd') }}"
                       class="form-control"
                       oninput="document.getElementById('primary_color').value=this.value;">
            </div>
        </div>
    </div>

    <!-- Secondary Color -->
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="form-group">
            <label for="secodery_color" class="form-label">Secondary Color</label>
            <div class="d-flex align-items-center gap-2">
                <input type="color" 
                       name="secodery_color" 
                       id="secodery_color"
                       value="{{ old('secodery_color', $edit_data->secodery_color ?? '#198754') }}"
                       class="form-control form-control-color" 
                       style="width: 60px; height: 38px; cursor: pointer;">
                <input type="text"
                       value="{{ old('secodery_color', $edit_data->secodery_color ?? '#198754') }}"
                       class="form-control"
                       oninput="document.getElementById('secodery_color').value=this.value;">
            </div>
        </div>
    </div>

    <!-- Footer Color -->
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="form-group">
            <label for="footer_color" class="form-label">Footer Color</label>
            <div class="d-flex align-items-center gap-2">
                <input type="color" 
                       name="footer_color" 
                       id="footer_color"
                       value="{{ old('footer_color', $edit_data->footer_color ?? '#222222') }}"
                       class="form-control form-control-color" 
                       style="width: 60px; height: 38px; cursor: pointer;">
                <input type="text"
                       value="{{ old('footer_color', $edit_data->footer_color ?? '#222222') }}"
                       class="form-control"
                       oninput="document.getElementById('footer_color').value=this.value;">
            </div>
        </div>
    </div>

    <!-- Copyright Color -->
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="form-group">
            <label for="copyright_color" class="form-label">Copyright Color</label>
            <div class="d-flex align-items-center gap-2">
                <input type="color" 
                       name="copyright_color" 
                       id="copyright_color"
                       value="{{ old('copyright_color', $edit_data->copyright_color ?? '#111111') }}"
                       class="form-control form-control-color" 
                       style="width: 60px; height: 38px; cursor: pointer;">
                <input type="text"
                       value="{{ old('copyright_color', $edit_data->copyright_color ?? '#111111') }}"
                       class="form-control"
                       oninput="document.getElementById('copyright_color').value=this.value;">
            </div>
        </div>
    </div>
</div>

					
					
					
                    <!-- col-end -->
                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="white_logo" class="form-label">White Logo *</label>
                            <input type="file" class="form-control @error('white_logo') is-invalid @enderror" name="white_logo" value="{{ old('white_logo') }}"  id="white_logo" >
                            <img src="{{asset($edit_data->white_logo)}}" class="edit-image" alt="">
                            @error('white_logo')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col end -->
                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="dark_logo" class="form-label">Dark Logo *</label>
                            <input type="file" class="form-control @error('dark_logo') is-invalid @enderror" name="dark_logo" value="{{ old('dark_logo') }}"  id="dark_logo" >
                            <img src="{{asset($edit_data->dark_logo)}}" class="edit-image" alt="">
                            @error('dark_logo')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col end -->
                    
                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="favicon" class="form-label">Favicon Logo *</label>
                            <input type="file" class="form-control @error('favicon') is-invalid @enderror" name="favicon" value="{{ old('favicon') }}"  id="favicon" >
                            <img src="{{asset($edit_data->favicon)}}" class="edit-image" alt="">
                            @error('favicon')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col end -->
					
					                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="og_baner" class="form-label">OG Baner Images *</label>
                            <input type="file" class="form-control @error('og_baner') is-invalid @enderror" name="og_baner" value="{{ old('og_baner') }}"  id="og_baner" >
                            <img src="{{asset($edit_data->og_baner)}}" class="edit-image" alt="">
                            @error('og_baner')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col end -->
					
                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="app_download_image" class="form-label">App Download Image</label>
                            <input type="file" class="form-control @error('app_download_image') is-invalid @enderror" name="app_download_image" value="{{ old('app_download_image') }}"  id="app_download_image" >
                            @if($edit_data->app_download_image)
                                <img src="{{asset($edit_data->app_download_image)}}" class="edit-image" alt="">
                            @endif
                            @error('app_download_image')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="app_download_link" class="form-label">App Download Link</label>
                            <input type="url" class="form-control @error('app_download_link') is-invalid @enderror" name="app_download_link" value="{{ $edit_data->app_download_link }}" id="app_download_link">
                            @error('app_download_link')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
					
					
					
					
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="hot_deal_end_date" class="form-label">Hot Deal End Date *</label>
                            <input type="date" class="form-control @error('hot_deal_end_date') is-invalid @enderror" name="hot_deal_end_date" value="{{ $edit_data->hot_deal_end_date}}" id="hot_deal_end_date" required="">
                            @error('hot_deal_end_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="flash_sale_end_date" class="form-label">Flash Sale End Date *</label>
                            <input type="date" class="form-control @error('flash_sale_end_date') is-invalid @enderror" name="flash_sale_end_date" value="{{ $edit_data->flash_sale_end_date}}" id="flash_sale_end_date" required="">
                            @error('flash_sale_end_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="copyright" class="form-label">Copyright Text</label>
                            <input type="text" class="form-control @error('copyright') is-invalid @enderror" name="copyright" value="{{ $edit_data->copyright }}" id="copyright">
                            @error('copyright')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="designed_by" class="form-label">Designed By (HTML allowed)</label>
                            <input type="text" class="form-control @error('designed_by') is-invalid @enderror" name="designed_by" value="{{ $edit_data->designed_by }}" id="designed_by">
                            @error('designed_by')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="top_headline" class="form-label">Top Headline</label>
                            <textarea class="form-control @error('top_headline') is-invalid @enderror" name="top_headline"  id="top_headline">
                                {{ $edit_data->top_headline}}
                            </textarea>
                            @error('top_headline')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="checkout_note" class="form-label">Checkout Note</label>
                            <textarea class="summernote form-control @error('checkout_note') is-invalid @enderror" name="checkout_note"  id="checkout_note">
                                {{ $edit_data->checkout_note}}
                            </textarea>
                            @error('checkout_note')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="order_policy" class="form-label">Order Policy</label>
                            <textarea class="summernote form-control @error('order_policy') is-invalid @enderror" name="order_policy"  id="order_policy">
                                {{ $edit_data->order_policy}}
                            </textarea>
                            @error('order_policy')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="show_all_products" class="d-block">All Products at Home</label>
                            <select class="form-control" name="show_all_products" id="show_all_products">
                                <option value="1" @if($edit_data->show_all_products==1)selected @endif >Show</option>
                                <option value="0" @if($edit_data->show_all_products==0)selected @endif >Hide</option>
                            </select>
                           
                            @error('show_all_products')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="show_category_wise_products" class="d-block">Categories Wise Products at Home</label>
                            <select class="form-control" name="show_category_wise_products" id="show_category_wise_products">
                                <option value="1" @if($edit_data->show_category_wise_products==1)selected @endif >Show</option>
                                <option value="0" @if($edit_data->show_category_wise_products==0)selected @endif >Hide</option>
                            </select>
                          
                            @error('show_category_wise_products')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="show_reviews" class="d-block">Customer Reviews at Home</label>
                            <select class="form-control" name="show_reviews" id="show_reviews">
                                <option value="1" @if($edit_data->show_reviews==1)selected @endif >Show</option>
                                <option value="0" @if($edit_data->show_reviews==0)selected @endif >Hide</option>
                            </select>
                          
                            @error('show_reviews')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="status" class="d-block">Status</label>
                            <label class="switch">
                              <input type="checkbox" disabled value="1" name="status" @if($edit_data->status==1)checked @endif>
                              <span class="slider round"></span>
                            </label>
                            @error('status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col end -->
                    <div>
                        <input type="submit" class="btn btn-success" value="Submit">
                    </div>

                </form>

            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->
   </div>
</div>
@endsection


@section('script')
<script src="{{asset('backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/js/pages/form-advanced.init.js"></script>
<!-- Plugins js -->
<script src="{{asset('backEnd/')}}/assets/libs//summernote/summernote-lite.min.js"></script>
<script>
    $(".summernote").summernote({
        placeholder: "Enter Your Text Here",
    });
</script>
@endsection