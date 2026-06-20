@extends('backEnd.layouts.master') 
@section('title','Payment Gateway')
@section('css')
<style>
  .increment_btn,
  .remove_btn {
    margin-top: -17px;
    margin-bottom: 10px;
  }
</style>
<link href="{{asset('backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('backEnd')}}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet" type="text/css" />
@endsection @section('content')
<div class="container-fluid">
<!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box d-flex align-items-center">
      <h4 class="page-title d-flex align-items-center gap-2">
        <img src="{{ asset('frontEnd/images/bkash.svg') }}"
             alt="Bkash Logo"
             style="height:28px; width:auto; margin-right:8px;">
        Bkash
      </h4>
    </div>
  </div>
</div>
<!-- end page title -->

<div class="row justify-content-center">
  <div class="col-lg-12">
    <div class="card border border-danger shadow-sm">
      <div class="card-body">
        <form action="{{ route('paymentgeteway.update') }}" method="POST" class="row" data-parsley-validate enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="id" value="{{ $bkash->id }}">

          <div class="col-sm-4">
            <div class="form-group mb-3">
              <label for="username" class="form-label">User Name *</label>
              <input type="text" class="form-control @error('username') is-invalid @enderror"
                     name="username" id="username" value="{{ $bkash->username }}" required />
              @error('username')
              <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
            </div>
          </div>

          <div class="col-sm-4">
            <div class="form-group mb-3">
              <label for="app_key" class="form-label">App Key *</label>
              <input type="text" class="form-control @error('app_key') is-invalid @enderror"
                     name="app_key" id="app_key" value="{{ $bkash->app_key }}" required />
              @error('app_key')
              <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
            </div>
          </div>

          <div class="col-sm-4">
            <div class="form-group mb-3">
              <label for="app_secret" class="form-label">App Secret *</label>
              <input type="text" class="form-control @error('app_secret') is-invalid @enderror"
                     name="app_secret" id="app_secret" value="{{ $bkash->app_secret }}" required />
              @error('app_secret')
              <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
            </div>
          </div>

          <div class="col-sm-4">
            <div class="form-group mb-3">
              <label for="base_url" class="form-label">Base Url *</label>
              <input type="text" class="form-control @error('base_url') is-invalid @enderror"
                     name="base_url" id="base_url" value="{{ $bkash->base_url }}" required />
              @error('base_url')
              <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
            </div>
          </div>

          <div class="col-sm-4">
            <div class="form-group mb-3">
              <label for="password" class="form-label">Password *</label>
              <input type="text" class="form-control @error('password') is-invalid @enderror"
                     name="password" id="password" value="{{ $bkash->password }}" required />
              @error('password')
              <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
            </div>
          </div>

          <div class="col-sm-3 mb-3">
            <div class="form-group">
              <label for="status" class="d-block">Status</label>
              <label class="switch">
                <input type="checkbox" value="1" @if($bkash->status==1) checked @endif name="status" />
                <span class="slider round"></span>
              </label>
            </div>
          </div>

          <div class="col-12">
            <button type="submit" class="btn btn-success mt-2">
              <i class="fas fa-save"></i> Update Bkash Settings
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

  
  
    <!------------- UddoktaPay Integration Start ------------>
  <div class="row mt-4">
    <div class="col-12">
      <div class="page-title-box">
        <h4 class="page-title">
          <img src="{{ asset('frontEnd/images/uddokta.png') }}" alt="UddoktaPay Logo" style="height:28px;margin-right:6px;">
          UddoktaPay
        </h4>
      </div>
    </div>
  </div>

  <div class="row justify-content-center">
    <div class="col-lg-12">
      <div class="card border border-success">
        <div class="card-body">
          <form action="{{ route('paymentgeteway.update') }}" method="POST" class="row" data-parsley-validate enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{ $uddoktapay->id ?? '' }}">
            
            <div class="col-sm-6">
              <div class="form-group mb-3">
                <label for="app_key" class="form-label">UddoktaPay API Key *</label>
                <input type="text" class="form-control @error('app_key') is-invalid @enderror"
                       name="app_key" id="app_key"
                       value="{{ $uddoktapay->app_key ?? '' }}"
                       placeholder="Enter your UDDOKTAPAY_API_KEY" required />
                @error('app_key')
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                  </span>
                @enderror
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group mb-3">
                <label for="base_url" class="form-label">UddoktaPay API URL *</label>
                <input type="text" class="form-control @error('base_url') is-invalid @enderror"
                       name="base_url" id="base_url"
                       value="{{ $uddoktapay->base_url ?? 'https://sandbox.uddoktapay.com/api/checkout-v2' }}"
                       placeholder="Enter your UDDOKTAPAY_API_URL" required />
                @error('base_url')
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                  </span>
                @enderror
              </div>
            </div>

            <div class="col-sm-3 mb-3">
              <div class="form-group">
                <label for="status" class="d-block">Status</label>
                <label class="switch">
                  <input type="checkbox" value="1" name="status"
                    @if(isset($uddoktapay) && $uddoktapay->status == 1) checked @endif />
                  <span class="slider round"></span>
                </label>
                <span class="ms-2">{{ (isset($uddoktapay) && $uddoktapay->status == 1) ? 'Enabled' : 'Disabled' }}</span>
              </div>
            </div>

            <div class="col-12">
              <button type="submit" class="btn btn-success mt-2">Save UddoktaPay Settings</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!------------- UddoktaPay Integration End ------------->

 <!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box d-flex align-items-center">
      <h4 class="page-title d-flex align-items-center gap-2">
        <img src="{{ asset('frontEnd/images/shurjoPay.png') }}"
             alt="Shurjopay Logo"
             style="height:28px; width:auto; margin-right:8px;">
        Shurjopay
      </h4>
    </div>
  </div>
</div>
<!-- end page title -->

<div class="row justify-content-center">
  <div class="col-lg-12">
    <div class="card border border-primary shadow-sm">
      <div class="card-body">
        <form action="{{ route('paymentgeteway.update') }}" method="POST" class="row" data-parsley-validate enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="id" value="{{ $shurjopay->id }}">

          <div class="col-sm-4">
            <div class="form-group mb-3">
              <label for="username" class="form-label">User Name *</label>
              <input type="text" class="form-control @error('username') is-invalid @enderror"
                     name="username" id="username"
                     value="{{ $shurjopay->username }}" required />
              @error('username')
              <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
            </div>
          </div>

          <div class="col-sm-4">
            <div class="form-group mb-3">
              <label for="prefix" class="form-label">Prefix *</label>
              <input type="text" class="form-control @error('prefix') is-invalid @enderror"
                     name="prefix" id="prefix"
                     value="{{ $shurjopay->prefix }}" required />
              @error('prefix')
              <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
            </div>
          </div>

          <div class="col-sm-4">
            <div class="form-group mb-3">
              <label for="success_url" class="form-label">Success URL *</label>
              <input type="text" class="form-control @error('success_url') is-invalid @enderror"
                     name="success_url" id="success_url"
                     value="{{ $shurjopay->success_url }}" required />
              @error('success_url')
              <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
            </div>
          </div>

          <div class="col-sm-4">
            <div class="form-group mb-3">
              <label for="return_url" class="form-label">Return URL *</label>
              <input type="text" class="form-control @error('return_url') is-invalid @enderror"
                     name="return_url" id="return_url"
                     value="{{ $shurjopay->return_url }}" required />
              @error('return_url')
              <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
            </div>
          </div>

          <div class="col-sm-4">
            <div class="form-group mb-3">
              <label for="base_url" class="form-label">Base URL *</label>
              <input type="text" class="form-control @error('base_url') is-invalid @enderror"
                     name="base_url" id="base_url"
                     value="{{ $shurjopay->base_url }}" required />
              @error('base_url')
              <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
            </div>
          </div>

          <div class="col-sm-4">
            <div class="form-group mb-3">
              <label for="password" class="form-label">Password *</label>
              <input type="text" class="form-control @error('password') is-invalid @enderror"
                     name="password" id="password"
                     value="{{ $shurjopay->password }}" required />
              @error('password')
              <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
            </div>
          </div>

          <div class="col-sm-3 mb-3">
            <div class="form-group">
              <label for="status" class="d-block">Status</label>
              <label class="switch">
                <input type="checkbox" value="1" @if($shurjopay->status==1) checked @endif name="status" />
                <span class="slider round"></span>
              </label>
            </div>
          </div>

          <div class="col-12">
            <button type="submit" class="btn btn-primary mt-2">
              <i class="fas fa-save"></i> Update Shurjopay Settings
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

  
</div>
@endsection @section('script')
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
<script type="text/javascript">
  $(document).ready(function () {
    $(".btn-increment").click(function () {
      var html = $(".clone").html();
      $(".increment").after(html);
    });
    $("body").on("click", ".btn-danger", function () {
      $(this).parents(".control-group").remove();
    });
  });
</script>
<script type="text/javascript">
  $(document).ready(function () {
    $(".increment_btn").click(function () {
      var html = $(".clone_price").html();
      $(".increment_price").after(html);
    });
    $("body").on("click", ".remove_btn", function () {
      $(this).parents(".increment_control").remove();
    });

    $(".select2").select2();
  });
</script>
@endsection