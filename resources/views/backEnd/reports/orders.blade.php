@extends('backEnd.layouts.master')
@section('title','Order Report')

@section('content')
<div class="container-fluid">

    <div class="d-flex align-items-center mb-3">
        <h4 class="mb-0">Order Report</h4>
        <span class="ms-3 text-muted">{{ $label }}</span>
    </div>

    {{-- Filter Form --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.orders') }}" class="row g-3">

                <div class="col-md-3">
                    <label class="form-label">Report Type</label>
                    <select name="type" class="form-control" id="report-type">
                        <option value="today" {{ $type=='today' ? 'selected' : '' }}>Today</option>
                        <option value="month" {{ $type=='month' ? 'selected' : '' }}>This / Any Month</option>
                        <option value="year"  {{ $type=='year'  ? 'selected' : '' }}>This / Any Year</option>
                        <option value="range" {{ $type=='range' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>

                <div class="col-md-2 type-month type-year">
                    <label class="form-label">Year</label>
                    <input type="number" name="year" class="form-control"
                           value="{{ request('year', now()->year) }}">
                </div>

                <div class="col-md-2 type-month">
                    <label class="form-label">Month</label>
                    <input type="number" name="month" class="form-control"
                           min="1" max="12"
                           value="{{ request('month', now()->month) }}">
                </div>

                <div class="col-md-2 type-range">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from_date" class="form-control"
                           value="{{ request('from_date') }}">
                </div>

                <div class="col-md-2 type-range">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to_date" class="form-control"
                           value="{{ request('to_date') }}">
                </div>

                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button class="btn btn-primary me-2" type="submit">
                        Filter
                    </button>

                    <button class="btn btn-outline-success" type="submit" name="export" value="csv">
                        ⬇ Export CSV
                    </button>
                </div>
            </form>
        </div>
    </div>

   <style>
    /* সব রঙিন কার্ডের ভিতরের টেক্সট সাদা */
    .card.bg-info *,
    .card.bg-success *,
    .card.bg-warning *,
    .card.bg-secondary * {
        color: white !important;
    }
</style>

{{-- Summary Cards --}}
<div class="row mb-3">
    <div class="col-md-3 mb-2">
        <div class="card bg-info">
            <div class="card-body">
                <h6 class="mb-1">Total Orders</h6>
                <h3 class="mb-0">{{ $totalOrders }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-2">
        <div class="card bg-success">
            <div class="card-body">
                <h6 class="mb-1">Total Amount</h6>
                <h3 class="mb-0">{{ number_format($totalAmount, 2) }} ৳</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-2">
        <div class="card bg-warning">
            <div class="card-body">
                <h6 class="mb-1">Total Discount</h6>
                <h3 class="mb-0">{{ number_format($totalDiscount, 2) }} ৳</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-2">
        <div class="card bg-secondary">
            <div class="card-body">
                <h6 class="mb-1">Total Shipping</h6>
                <h3 class="mb-0">{{ number_format($totalShipping, 2) }} ৳</h3>
            </div>
        </div>
    </div>
</div>

    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-header">
            <strong>Order List</strong>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Invoice</th>
                    <th>Customer</th>
                    <th class="text-end">Total (৳)</th>
                    <th class="text-end">Discount</th>
                    <th class="text-end">Shipping</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    @php
                        // row wise calculations – controller এর helper অনুযায়ী
                        $rowTotal = 0;
                        if (isset($order->amount) && is_numeric($order->amount)) {
                            $rowTotal = $order->amount;
                        } elseif (isset($order->total) && is_numeric($order->total)) {
                            $rowTotal = $order->total;
                        } elseif (isset($order->total_amount) && is_numeric($order->total_amount)) {
                            $rowTotal = $order->total_amount;
                        } elseif (isset($order->grand_total) && is_numeric($order->grand_total)) {
                            $rowTotal = $order->grand_total;
                        } elseif (isset($order->subtotal) && is_numeric($order->subtotal)) {
                            $rowTotal = $order->subtotal;
                        }

                        $rowDiscount = 0;
                        if (isset($order->discount) && is_numeric($order->discount)) {
                            $rowDiscount = $order->discount;
                        } elseif (isset($order->discount_amount) && is_numeric($order->discount_amount)) {
                            $rowDiscount = $order->discount_amount;
                        } elseif (isset($order->coupon_discount) && is_numeric($order->coupon_discount)) {
                            $rowDiscount = $order->coupon_discount;
                        }

                        $rowShipping = 0;
                        if (isset($order->shipping_amount) && is_numeric($order->shipping_amount)) {
                            $rowShipping = $order->shipping_amount;
                        } elseif (isset($order->shipping_charge) && is_numeric($order->shipping_charge)) {
                            $rowShipping = $order->shipping_charge;
                        } elseif (isset($order->shipping_cost) && is_numeric($order->shipping_cost)) {
                            $rowShipping = $order->shipping_cost;
                        } elseif (isset($order->shipping) && is_numeric($order->shipping)) {
                            $rowShipping = $order->shipping;
                        }
                    @endphp

                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $order->invoice_id ?? $order->id }}</td>
                        <td>{{ $order->customer_name ?? ($order->customer->name ?? '') }}</td>

                        <td class="text-end">{{ number_format($rowTotal, 2) }}</td>
                        <td class="text-end">{{ number_format($rowDiscount, 2) }}</td>
                        <td class="text-end">{{ number_format($rowShipping, 2) }}</td>

                        <td>
                            @if(isset($order->status) && is_object($order->status))
                                {{ $order->status->name ?? '-' }}
                            @else
                                {{ $order->order_status ?? $order->status ?? '-' }}
                            @endif
                        </td>

                        <td>{{ optional($order->created_at)->format('d M, Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            কোনো অর্ডার পাওয়া যায়নি।
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function toggleReportFields() {
        var type = document.getElementById('report-type').value;

        document.querySelectorAll('.type-month').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.type-year').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.type-range').forEach(el => el.style.display = 'none');

        if (type === 'month') {
            document.querySelectorAll('.type-month').forEach(el => el.style.display = 'block');
            document.querySelectorAll('.type-year').forEach(el => el.style.display = 'block');
        } else if (type === 'year') {
            document.querySelectorAll('.type-year').forEach(el => el.style.display = 'block');
        } else if (type === 'range') {
            document.querySelectorAll('.type-range').forEach(el => el.style.display = 'block');
        }
    }

    document.getElementById('report-type').addEventListener('change', toggleReportFields);
    toggleReportFields();
</script>
@endsection
