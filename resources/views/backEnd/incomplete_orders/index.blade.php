@extends('backEnd.layouts.master')
@section('title','Incomplete Orders')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            @if($orders->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle text-center mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Products</th>
                            <th>Total Amount</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td><strong>#{{ $order->id }}</strong></td>
                            <td>{{ $order->name ?? '—' }}</td>
                            <td>{{ $order->phone ?? '—' }}</td>
                            <td style="max-width:200px;">{{ \Illuminate\Support\Str::limit($order->address ?? '—', 60) }}</td>

                            {{-- Product list --}}
                            <td class="text-start" style="min-width:260px;">
                                @if(!empty($order->items) && is_array($order->items))
                                    <div class="d-flex flex-column gap-2">
                                        @foreach($order->items as $it)
                                            <div class="d-flex align-items-center border-bottom pb-1" style="gap:8px;">
                                                @if(!empty($it['image']))
                                                    <a href="{{ $it['link'] ?? '#' }}" target="_blank">
                                                        <img src="{{ $it['image'] }}" width="55" height="55" class="rounded shadow-sm" style="object-fit:cover;">
                                                    </a>
                                                @endif
                                                <div>
                                                    <strong>{{ $it['name'] ?? 'Product' }}</strong><br>
                                                    <small>Qty: {{ $it['qty'] ?? 1 }}</small>
                                                    @if(!empty($it['price']))
                                                        <small class="text-muted d-block">৳ {{ number_format($it['price'],2) }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    @if($order->product_link)
                                        <a href="{{ $order->product_link }}" target="_blank">
                                            <img src="{{ asset($order->product_image ?? '') }}" width="80" height="80" style="object-fit:cover;">
                                        </a>
                                    @else
                                        <em>—</em>
                                    @endif
                                @endif
                            </td>

                            {{-- Total amount --}}
                            <td><strong>৳ {{ number_format($order->total_amount ?? 0, 2) }}</strong></td>

                            {{-- Date --}}
                            <td>{{ optional($order->created_at)->format('d M Y, h:i A') }}</td>

                            {{-- Action --}}
                            <td>
                                <div class="d-flex flex-column gap-1">

                                    {{-- ✅ ACCEPT BUTTON – ইনকমপ্লিট → রেগুলার অর্ডার --}}
                                    <form action="{{ route('admin.incomplete-orders.accept', $order->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('এই ইনকমপ্লিট অর্ডার থেকে অর্ডার বানাতে চান?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success w-100 mb-1">
                                            <i class="fa fa-check"></i> Accept
                                        </button>
                                    </form>

                                    {{-- ❌ DELETE BUTTON --}}
                                    <form action="{{ route('admin.incomplete-orders.destroy', $order->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Are you sure to delete this record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger w-100">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center my-3">
                {{ $orders->links('pagination::bootstrap-4') }}
            </div>
            @else
            <div class="p-5 text-center text-muted">
                <i class="fa fa-info-circle fa-2x mb-3"></i>
                <h6>No incomplete orders found.</h6>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
