@extends('frontEnd.layouts.master')
@section('title','Customer Account')
@section('content')

<section class="comn_sec">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="cmn_menu">
                    <ul>
                        @foreach($cmnmenu as $key=>$value)
                        <li>
                            <a href="{{route('page',$value->slug)}}">{{$value->name}}</a>
                        </li>
                        @endforeach
                        <li>
                            <a href="{{route('contact')}}">যোগাযোগ করুন</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="contact-section">
    <div class="container">

        <div class="row">
            <div class="col-sm-6">
                <div class="cont_item">
                 <a href="tel:{{$contact->hotline}}">
                  <i data-feather="phone"></i>
                  {{$contact->hotline}}
                 </a>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="cont_item">
                 <a href="">
                  <i data-feather="mail"></i>
                  {{$contact->email}}
                 </a>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-sm-12">
                
            </div>
             <div class="col-sm-10">
                <div class="contact-form">
                    <h5 class="account-title">অথবা </h5>
<form action="{{ route('contact.store') }}" method="POST" class="row" enctype="multipart/form-data">
    @csrf
    <div class="col-sm-6">
        <label for="name">সম্পূর্ণ নাম *</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="col-sm-6">
        <label for="phone">মোবাইল নাম্বার *</label>
        <input type="text" name="phone" class="form-control" required>
    </div>

    <div class="col-sm-12">
        <label for="email">ইমেইল *</label>
        <input type="email" name="email" class="form-control" required>
    </div>

    <div class="col-sm-12">
        <label for="subject">বিষয় *</label>
        <input type="text" name="subject" class="form-control" required>
    </div>

    <div class="col-sm-12">
        <label for="message">মেসেজ লিখুন *</label>
        <textarea name="message" class="form-control" required></textarea>
    </div>

    <div class="col-sm-12 mt-3">
        <button type="submit" class="btn btn-primary w-100">মেসেজ পাঠান</button>
    </div>
</form>

                </div>
            </div>
        </div>

    </div>
</section>
@endsection
@push('script')
<script src="{{asset('frontEnd/')}}/js/parsley.min.js"></script>
<script src="{{asset('frontEnd/')}}/js/form-validation.init.js"></script>
@endpush