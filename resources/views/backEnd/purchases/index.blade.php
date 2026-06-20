@extends('backEnd.layouts.master')
@section('title','Purchases')

@section('content')
<div class="container-fluid">

    <div class="d-flex align-items-center mb-3">
        <h4 class="mb-0">Purchases / পারচেস</h4>
    </div>

    {{-- SUMMARY --}}
    <div class="row mb-4">

       <style>
    /* সব রঙিন কার্ডের ভিতরের টেক্সট সাদা */
    .card.bg-success *, 
    .card.bg-info *, 
    .card.bg-primary *, 
    .card.bg-danger * {
        color: white !important;
    }
</style>

<div class="col-md-3 mb-3">
    <div class="card bg-success">
        <div class="card-body">
            <h6 class="mb-1">This Year ({{ $currentYear }})</h6>
            <h3 class="mb-0">{{ number_format($yearlyTotal,2) }} ৳</h3>
            <small class="d-block mt-1">এই বছরে মোট পারচেস</small>
        </div>
    </div>
</div>

<div class="col-md-3 mb-3">
    <div class="card bg-info">
        <div class="card-body">
            <h6 class="mb-1">
                This Month ({{ \Carbon\Carbon::createFromDate(now()->year, $currentMonth, 1)->format('F') }})
            </h6>
            <h3 class="mb-0">{{ number_format($monthlyTotal,2) }} ৳</h3>
            <small class="d-block mt-1">এই মাসে মোট পারচেস</small>
        </div>
    </div>
</div>

<div class="col-md-3 mb-3">
    <div class="card bg-primary">
        <div class="card-body">
            <h6 class="mb-1">Today ({{ now()->format('d M, Y') }})</h6>
            <h3 class="mb-0">{{ number_format($todayTotal,2) }} ৳</h3>
            <small class="d-block mt-1">আজকের মোট পারচেস</small>
        </div>
    </div>
</div>

<div class="col-md-3 mb-3">
    <div class="card bg-danger">
        <div class="card-body">
            <h6 class="mb-1">Total Supplier Due</h6>
            <h3 class="mb-0">{{ number_format($totalDue,2) }} ৳</h3>
            <small class="d-block mt-1">মোট বাকী</small>
        </div>
    </div>
</div>


    </div>

    <div class="row">

        {{-- CREATE Purchase --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <strong>+ New Purchase</strong>
                </div>
                <div class="card-body">

                    <form action="{{ route('purchases.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Supplier *</label>
                            <select name="supplier_id" class="form-control" required>
                                <option value="">-- Select --</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->phone }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 d-flex gap-2">
                            <div class="flex-fill">
                                <label class="form-label">Invoice No *</label>
                                <input type="text" name="invoice_no" class="form-control"
                                       value="{{ 'PUR-'.time() }}" required>
                            </div>
                            <div class="flex-fill">
                                <label class="form-label">Date *</label>
                                <input type="date" name="purchase_date" class="form-control"
                                       value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                        </div>

                        <hr>

                        <h6>Product Info</h6>

                        <div class="mb-3">
                            <label class="form-label">Product *</label>
                            <select name="product_id" class="form-control" required>
                                <option value="">-- Select --</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} (Stock: {{ $p->stock }})</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- চাইলে variant select আলাদা করে নিও --}}
                        <input type="hidden" name="variant_price_id" value="">

                        <div class="mb-3 d-flex gap-2">
                            <div class="flex-fill">
                                <label class="form-label">Qty *</label>
                                <input type="number" name="qty" class="form-control" min="1" value="1" required>
                            </div>
                            <div class="flex-fill">
                                <label class="form-label">Unit Cost (৳) *</label>
                                <input type="number" step="0.01" name="unit_cost" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3 d-flex gap-2">
                            <div class="flex-fill">
                                <label class="form-label">Discount</label>
                                <input type="number" step="0.01" name="discount" class="form-control" value="0">
                            </div>
                            <div class="flex-fill">
                                <label class="form-label">Shipping Cost</label>
                                <input type="number" step="0.01" name="shipping_cost" class="form-control" value="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Paid Amount (From Fund)</label>
                            <input type="number" step="0.01" name="paid_amount" class="form-control" value="0">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Note (optional)</label>
                            <textarea name="note" class="form-control" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Save Purchase
                        </button>
                    </form>

                </div>
            </div>
        </div>

        {{-- Export + Filter --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <strong>📤 Export Purchase Report</strong>
                </div>
                <div class="card-body">
                    <form action="{{ route('purchases.export') }}" method="GET" target="_blank">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Year</label>
                                <input type="number" name="year" class="form-control"
                                       value="{{ request('year') }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Month (1-12)</label>
                                <input type="number" name="month" class="form-control"
                                       value="{{ request('month') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">From Date</label>
                                <input type="date" name="from_date" class="form-control"
                                       value="{{ request('from_date') }}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">To Date</label>
                                <input type="date" name="to_date" class="form-control"
                                       value="{{ request('to_date') }}">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-outline-primary w-100 mt-2">
                            ⬇ Download CSV
                        </button>
                    </form>
                </div>
            </div>

            {{-- ছোট Filter form (লিস্টের জন্য) চাইলে এখানে আরেকটা বানাতে পারো --}}
        </div>
    </div>

    {{-- History --}}
    <div class="card shadow-sm">
        <div class="card-header">
            <strong>🧾 Purchase History</strong>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Invoice</th>
                    <th>Supplier</th>
                    <th class="text-end">Grand Total</th>
                    <th class="text-end">Paid</th>
                    <th class="text-end">Due</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse($purchases as $p)
                    <tr>
                        <td>{{ $loop->iteration + ($purchases->currentPage()-1)*$purchases->perPage() }}</td>
                        <td>{{ $p->purchase_date }}</td>
                        <td>{{ $p->invoice_no }}</td>
                        <td>{{ optional($p->supplier)->name }}</td>
                        <td class="text-end">{{ number_format($p->grand_total,2) }}</td>
                        <td class="text-end">{{ number_format($p->paid_amount,2) }}</td>
                        <td class="text-end">{{ number_format($p->due_amount,2) }}</td>
                        <td class="d-flex gap-1">
                            <a href="{{ route('purchases.invoice',$p->id) }}"
                               class="btn btn-sm btn-outline-secondary" target="_blank">
                                Invoice
                            </a>

                            @if($p->due_amount > 0)
                                <form action="{{ route('purchases.pay_due',$p->id) }}" method="POST" class="d-flex">
                                    @csrf
                                    <input type="number" step="0.01" name="amount"
                                           class="form-control form-control-sm me-1"
                                           placeholder="Pay" style="width:90px;">
                                    <input type="date" name="payment_date"
                                           class="form-control form-control-sm me-1"
                                           value="{{ now()->format('Y-m-d') }}" style="width:130px;">
                                    <button class="btn btn-sm btn-success">Pay</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            কোনো Purchase পাওয়া যায়নি।
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $purchases->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

</div>
@endsection
