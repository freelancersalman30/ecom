@extends('backEnd.layouts.master')
@section('title','Edit Coupon')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Edit Coupon</h4>
            <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary btn-sm">← Back</a>
        </div>
        <div class="card-body">

            {{-- ✅ ঠিক করা ফর্ম --}}
            <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
                @csrf
                {{-- @method('POST') --}}
                {{-- যদি রাউট PUT হয়, তাহলে নিচের লাইন ব্যবহার করো --}}
                {{-- @method('PUT') --}}

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Coupon Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control"
                            value="{{ old('code', $coupon->code) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-control" required>
                            <option value="percent" {{ $coupon->type == 'percent' ? 'selected' : '' }}>Percentage (%)</option>
                            <option value="flat" {{ $coupon->type == 'flat' ? 'selected' : '' }}>Flat (৳)</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Value <span class="text-danger">*</span></label>
                        <input type="number" name="value" class="form-control" step="0.01"
                            value="{{ old('value', $coupon->value) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Minimum Purchase (optional)</label>
                        <input type="number" name="min_purchase" class="form-control" step="0.01"
                            value="{{ old('min_purchase', $coupon->min_purchase) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Valid From</label>
                        <input type="date" name="valid_from" class="form-control"
                            value="{{ old('valid_from', $coupon->valid_from) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Valid To</label>
                        <input type="date" name="valid_to" class="form-control"
                            value="{{ old('valid_to', $coupon->valid_to) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="1" {{ $coupon->status == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ $coupon->status == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="col-md-12 mt-3">
                        <button type="submit" class="btn btn-primary w-100">💾 Update Coupon</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
