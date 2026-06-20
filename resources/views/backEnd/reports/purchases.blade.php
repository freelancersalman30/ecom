@extends('backEnd.layouts.master')
@section('title','Purchase Report')

@section('content')
<div class="container-fluid">

    <div class="d-flex align-items-center mb-3">
        <h4 class="mb-0">Purchase Report</h4>
        <span class="ms-3 text-muted">{{ $label ?? '' }}</span>
    </div>

    {{-- Filter Form --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.purchases') }}" class="row g-3">

                <div class="col-md-3">
                    <label class="form-label">Report Type</label>
                    <select name="type" class="form-control" id="report-type">
                        <option value="today" {{ ($type ?? '')=='today' ? 'selected' : '' }}>Today</option>
                        <option value="month" {{ ($type ?? '')=='month' ? 'selected' : '' }}>This / Any Month</option>
                        <option value="year"  {{ ($type ?? '')=='year'  ? 'selected' : '' }}>This / Any Year</option>
                        <option value="range" {{ ($type ?? '')=='range' ? 'selected' : '' }}>Custom Range</option>
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

    {{-- Summary Cards --}}
    <div class="row mb-3">
<style>
    /* রঙিন কার্ডগুলোর সব টেক্সট সাদা */
    .card.bg-primary *,
    .card.bg-success *,
    .card.bg-warning * {
        color: white !important;
    }
</style>

<div class="col-md-4 mb-2">
    <div class="card bg-primary">
        <div class="card-body">
            <h6 class="mb-1">Total Purchase Amount</h6>
            <h3 class="mb-0">{{ number_format($totalPurchaseAmount ?? 0, 2) }} ৳</h3>
        </div>
    </div>
</div>

<div class="col-md-4 mb-2">
    <div class="card bg-success">
        <div class="card-body">
            <h6 class="mb-1">Total Paid</h6>
            <h3 class="mb-0">{{ number_format($totalPaid ?? 0, 2) }} ৳</h3>
        </div>
    </div>
</div>

<div class="col-md-4 mb-2">
    <div class="card bg-warning">
        <div class="card-body">
            <h6 class="mb-1">Total Due</h6>
            <h3 class="mb-0">{{ number_format($totalDue ?? 0, 2) }} ৳</h3>
        </div>
    </div>
</div>

    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-header">
            <strong>Purchase List</strong>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Invoice</th>
                    <th>Supplier</th>
                    <th class="text-end">Total (৳)</th>
                    <th class="text-end">Paid (৳)</th>
                    <th class="text-end">Due (৳)</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
                @forelse($purchases as $p)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $p->invoice_no ?? $p->id }}</td>
                        <td>{{ $p->supplier->name ?? '-' }}</td>

                        {{-- ✅ আসল কলাম --}}
                        <td class="text-end">{{ number_format($p->grand_total ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($p->paid_amount ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($p->due_amount ?? 0, 2) }}</td>

                        <td>{{ optional($p->purchase_date)->format('d M, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            কোনো পারচেস পাওয়া যায়নি।
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
