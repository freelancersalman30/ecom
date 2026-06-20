@extends('backEnd.layouts.master') 
@section('title','Courier API')
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
      <img src="{{ asset('frontEnd/images/stade.svg') }}" alt="Steadfast Logo" style="height: 35px; margin-right: 10px;">
      <h4 class="page-title">Steadfast Courier</h4>
    </div>
  </div>
</div>
<!-- end page title -->

<div class="row justify-content-center">
  <div class="col-lg-12">
    <div class="card" style="border: 1px solid #ff4d4d; border-radius: 6px;">
      <div class="card-body">
        <form action="{{route('courierapi.update')}}" method="POST" class="row" data-parsley-validate="" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="id" value="{{$steadfast->id}}">
          
          <div class="col-sm-6">
            <div class="form-group mb-3">
              <label for="api_key" class="form-label">API key *</label>
              <input type="text" class="form-control @error('api_key') is-invalid @enderror"
                     name="api_key" value="{{ $steadfast->api_key }}" id="api_key" required />
              @error('api_key')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
              @enderror
            </div>
          </div>
          <!-- col-end -->

          <div class="col-sm-6">
            <div class="form-group mb-3">
              <label for="secret_key" class="form-label">Secret key *</label>
              <input type="text" class="form-control @error('secret_key') is-invalid @enderror"
                     name="secret_key" value="{{ $steadfast->secret_key }}" id="secret_key" required />
              @error('secret_key')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
              @enderror
            </div>
          </div>
          <!-- col-end -->

          <div class="col-sm-3 mb-3">
            <div class="form-group">
              <label for="status" class="d-block">Status</label>
              <label class="switch">
                <input type="checkbox" value="1" @if($steadfast->status==1)checked @endif name="status" />
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
            <input type="submit" class="btn btn-success" value="Submit" />
          </div>
        </form>
      </div>
      <!-- end card-body-->
    </div>
    <!-- end card-->
  </div>
  <!-- end col-->
</div>
<!-------------new-start------------>

<style>
.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
}
.switch input { display: none; }

.slider {
  position: absolute;
  cursor: pointer;
  background-color: #ccc;
  border-radius: 34px;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  transition: .4s;
}
.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  border-radius: 50%;
  transition: .4s;
}
input:checked + .slider {
  background-color: #28a745;
}
input:checked + .slider:before {
  transform: translateX(26px);
}

/* লাল বর্ডার কার্ড */
.red-border-card {
  border: 1px solid #ff4d4d !important;
  border-radius: 6px;
  box-shadow: 0 0 5px rgba(255, 77, 77, 0.25);
}

</style>
 

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