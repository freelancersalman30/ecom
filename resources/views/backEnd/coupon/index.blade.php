@extends('backEnd.layouts.master')
@section('title','Manage Coupons')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h4>All Coupons</h4>
            <a href="{{ route('admin.coupons.create') }}" class="btn btn-success btn-sm">+ Add Coupon</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Min Purchase</th>
                        <th>Valid From</th>
                        <th>Valid To</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($coupons as $key => $coupon)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $coupon->code }}</td>
                        <td>{{ ucfirst($coupon->type) }}</td>
                        <td>{{ $coupon->value }}</td>
                        <td>{{ $coupon->min_purchase ?? '-' }}</td>
                        <td>{{ $coupon->valid_from ?? '-' }}</td>
                        <td>{{ $coupon->valid_to ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $coupon->status ? 'bg-success' : 'bg-danger' }}">
                                {{ $coupon->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                <button class="btn btn-danger btn-sm" onclick="return confirm('Delete coupon?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
