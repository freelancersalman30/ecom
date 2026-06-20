@extends('frontEnd.layouts.master')
@section('title','Customer Account')
@section('content')
<section class="customer-section">
    <div class="container">
        <div class="row">
            <div class="col-sm-3">
                <div class="customer-sidebar">
                    @include('frontEnd.layouts.customer.sidebar')
                </div>
            </div>
            <div class="col-sm-9">
                <div class="customer-content">
                    <h5 class="account-title">Account Dashboard</h5>

                    @php
                        $customer = \App\Models\Customer::with('cust_area')->find(Auth::guard('customer')->user()->id);
                        $total_orders = \App\Models\Order::where('customer_id', $customer->id)->count();
                        $total_spent = \App\Models\Order::where('customer_id', $customer->id)->where('order_status', 6)->sum('amount');
                        $pending_orders = \App\Models\Order::where('customer_id', $customer->id)->where('order_status', 1)->count();
                    @endphp

                    <div class="dash-stats">
                        <div class="stat-card">
                            <div class="stat-icon blue">
                                <i data-feather="shopping-cart"></i>
                            </div>
                            <div class="stat-info">
                                <p>Total Orders</p>
                                <h3>{{$total_orders}}</h3>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon green">
                                <i data-feather="dollar-sign"></i>
                            </div>
                            <div class="stat-info">
                                <p>Total Spent</p>
                                <h3>৳{{number_format($total_spent, 0)}}</h3>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon orange">
                                <i data-feather="clock"></i>
                            </div>
                            <div class="stat-info">
                                <p>Pending Orders</p>
                                <h3>{{$pending_orders}}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="profile-info-card">
                        <h6 class="mb-4" style="font-weight: 700; color: #32325d;">Personal Information</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small text-uppercase font-weight-bold">Full Name</label>
                                <p class="mb-0 font-weight-bold" style="color: #525f7f; font-size: 16px;">{{$customer->name}}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small text-uppercase font-weight-bold">Phone Number</label>
                                <p class="mb-0 font-weight-bold" style="color: #525f7f; font-size: 16px;">{{$customer->phone}}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small text-uppercase font-weight-bold">Email Address</label>
                                <p class="mb-0 font-weight-bold" style="color: #525f7f; font-size: 16px;">{{$customer->email}}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small text-uppercase font-weight-bold">District</label>
                                <p class="mb-0 font-weight-bold" style="color: #525f7f; font-size: 16px;">{{$customer->district}}</p>
                            </div>
                            <div class="col-md-12">
                                <label class="text-muted small text-uppercase font-weight-bold">Full Address</label>
                                <p class="mb-0 font-weight-bold" style="color: #525f7f; font-size: 16px;">{{$customer->address}}, {{$customer->cust_area?$customer->cust_area->area_name:''}}</p>
                            </div>
                        </div>
                    </div>
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