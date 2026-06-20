@extends('backEnd.layouts.master')
@section('title','Add Coupon')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header"><h4>Create New Coupon</h4></div>
        <div class="card-body">
            <form action="{{ route('admin.coupons.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Coupon Code</label>
                        <input type="text" name="code" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Type</label>
                        <select name="type" class="form-control">
                            <option value="flat">Flat</option>
                            <option value="percent">Percent</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Value</label>
                        <input type="number" name="value" step="0.01" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Minimum Purchase</label>
                        <input type="number" name="min_purchase" step="0.01" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Valid From</label>
                        <input type="date" name="valid_from" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Valid To</label>
                        <input type="date" name="valid_to" class="form-control">
                    </div>
                </div>
                <button type="submit" class="btn btn-success mt-3">Save Coupon</button>
            </form>
        </div>
    </div>
</div>
@endsection
