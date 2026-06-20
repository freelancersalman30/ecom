@extends('backEnd.layouts.master')
@section('title','Profit & Loss Report')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex align-items-center mb-3">
        <h4 class="mb-0">Profit & Loss Report</h4>
        <span class="ms-3 text-muted">{{ $label ?? '' }}</span>
    </div>

    {{-- Filter Form --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.profit_loss') }}" class="row g-3">

                <div class="col-md-3">
                    <label class="form-label">Report Type</label>
                    <select name="type" class="form-control" id="report-type">
                        @php $type = $type ?? request('type','today'); @endphp
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

    {{-- Summary Cards --}}
    <div class="row mb-3">
      <style>
    .card.bg-info *,
    .card.bg-warning *,
    .card.bg-secondary *,
    .card.bg-success *,
    .card.bg-danger * {
        color: white !important;
    }
</style>

<div class="col-md-3 mb-2">
    <div class="card bg-info">
        <div class="card-body">
            <h6 class="mb-1">Total Sales</h6>
            <h3 class="mb-0">{{ number_format($salesAmount ?? 0, 2) }} ৳</h3>
        </div>
    </div>
</div>

<div class="col-md-3 mb-2">
    <div class="card bg-warning">
        <div class="card-body">
            <h6 class="mb-1">COGS (Cost of Goods Sold)</h6>
            <h3 class="mb-0">{{ number_format($cogs ?? 0, 2) }} ৳</h3>
        </div>
    </div>
</div>

<div class="col-md-3 mb-2">
    <div class="card bg-secondary">
        <div class="card-body">
            <h6 class="mb-1">Expenses</h6>
            <h3 class="mb-0">{{ number_format($totalExpense ?? 0, 2) }} ৳</h3>
        </div>
    </div>
</div>

<div class="col-md-3 mb-2">
    <div class="card {{ ($netProfit ?? 0) >= 0 ? 'bg-success' : 'bg-danger' }}">
        <div class="card-body">
            <h6 class="mb-1">Net Profit</h6>
            <h3 class="mb-0">{{ number_format($netProfit ?? 0, 2) }} ৳</h3>
        </div>
    </div>
</div>

    </div>

    {{-- Details Table --}}
    <div class="card">
        <div class="card-header">
            <strong>Summary</strong>
        </div>
        <div class="card-body">
            <table class="table table-bordered w-auto">
                <tbody>
                    <tr>
                        <th>Sales</th>
                        <td class="text-end">{{ number_format($salesAmount ?? 0, 2) }} ৳</td>
                    </tr>
                    <tr>
                        <th>COGS</th>
                        <td class="text-end">{{ number_format($cogs ?? 0, 2) }} ৳</td>
                    </tr>
                    <tr>
                        <th>Gross Profit</th>
                        <td class="text-end">{{ number_format($grossProfit ?? 0, 2) }} ৳</td>
                    </tr>
                    <tr>
                        <th>Expenses</th>
                        <td class="text-end">{{ number_format($totalExpense ?? 0, 2) }} ৳</td>
                    </tr>
                    <tr>
                        <th>Net Profit</th>
                        <td class="text-end">{{ number_format($netProfit ?? 0, 2) }} ৳</td>
                    </tr>
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
