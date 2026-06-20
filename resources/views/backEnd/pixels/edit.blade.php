@extends('backEnd.layouts.master')
@section('title','Pixels Edit')
@section('css')
<link href="{{asset('backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<div class="container-fluid">
    
  <!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="page-title d-flex align-items-center">
                <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEigthY5BKvlC1VMkGl6BBjlgeUOT8oRfmRGxTKW7CBmtGNUMS-bRUgkVQvOF5TxvniIzqz2fXM_9WpQ-egRi-BQJ5YFXjD-tb_2O2p9YSdnTE-K4ED3oaN5qYKlm18fPSZ8caex3K33-1bZNX00g8C6tDabotLwiIMaImRqLyjsu_pWs_xo9TkUPaaF41EI/s320/meta-logo%20(1).webp" alt="Facebook Pixel" width="36" class="me-2">
                Facebook Pixel Edit
            </h4>
            <div class="page-title-right">
                <a href="{{ route('pixels.index') }}" class="btn btn-primary rounded-pill">
                    <i class="fa fa-list me-1"></i> Manage
                </a>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row justify-content-center mt-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-bottom d-flex align-items-center">
                <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEigthY5BKvlC1VMkGl6BBjlgeUOT8oRfmRGxTKW7CBmtGNUMS-bRUgkVQvOF5TxvniIzqz2fXM_9WpQ-egRi-BQJ5YFXjD-tb_2O2p9YSdnTE-K4ED3oaN5qYKlm18fPSZ8caex3K33-1bZNX00g8C6tDabotLwiIMaImRqLyjsu_pWs_xo9TkUPaaF41EI/s320/meta-logo%20(1).webp" alt="Pixel Logo" width="30" class="me-2">
                <h5 class="mb-0 text-dark fw-semibold">Facebook Pixel Configuration</h5>
            </div>

            <div class="card-body">
                <form action="{{ route('pixels.update') }}" method="POST" class="row g-3" data-parsley-validate enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" value="{{ $edit_data->id }}" name="id">

                    <!-- Pixels ID -->
                    <div class="col-md-12">
                        <label for="code" class="form-label fw-semibold">Pixel ID *</label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" 
                               name="code" id="code" value="{{ $edit_data->code }}" required>
                        @error('code')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-md-12 mt-2">
                        <label for="status" class="form-label fw-semibold d-block mb-1">Status</label>
                        <label class="switch">
                            <input type="checkbox" value="1" name="status" @if($edit_data->status == 1) checked @endif>
                            <span class="slider round"></span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-md-12 mt-3 text-end">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="fa fa-save me-1"></i> Update Pixel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Custom Switch Style -->
<style>
.page-title-box {
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 10px;
}
.card {
    border-radius: 10px;
}
.switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 26px;
}
.switch input { display: none; }
.slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}
.slider:before {
    position: absolute;
    content: "";
    height: 20px; width: 20px;
    left: 3px; bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}
input:checked + .slider {
    background-color: #28a745;
}
input:checked + .slider:before {
    transform: translateX(24px);
}
</style>

</div>
@endsection


@section('script')
<script src="{{asset('backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/js/pages/form-advanced.init.js"></script>
@endsection