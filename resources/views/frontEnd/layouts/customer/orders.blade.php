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
                    <h5 class="account-title">Order History</h5>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Invoice ID</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Due</th>
                                    <th>Status</th>
                                    <th>Files</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($orders as $value)
                                @php
                                    $advancePaid = 0;
                                    if ($value->payment_gateway) {
                                        $advancePaid = \App\Models\Payment::where('order_id', $value->id)
                                                        ->where('payment_method', $value->payment_gateway)
                                                        ->sum('amount');
                                    }
                                    $dueAmount = $value->amount - $advancePaid;
                                    $digitalDownloads = \App\Models\DigitalDownload::where('order_id', $value->id)->get();
                                @endphp
                                <tr>
                                    <td><strong>#{{ $value->invoice_id }}</strong></td>
                                    <td>{{ $value->created_at->format('d M, Y') }}</td>
                                    <td>৳{{ number_format($value->amount, 0) }}</td>
                                    <td><span class="text-success">৳{{ number_format($advancePaid, 0) }}</span></td>
                                    <td><span class="{{$dueAmount > 0 ? 'text-danger' : 'text-muted'}}">৳{{ number_format($dueAmount, 0) }}</span></td>
                                    <td>
                                        @php
                                            $status_class = 'status-pending';
                                            if($value->order_status == 4) $status_class = 'status-completed';
                                            if($value->order_status == 5) $status_class = 'status-canceled';
                                        @endphp
                                        <span class="status-badge {{ $status_class }}">
                                            {{ $value->status ? $value->status->name : 'Unknown' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($digitalDownloads->count() > 0)
                                            @if($value->payment_status == 'paid')
                                                @foreach($digitalDownloads as $dl)
                                                    <a href="{{ route('digital.download', $dl->token) }}"
                                                       class="btn btn-xs btn-primary py-0" target="_blank" title="Download Product">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                @endforeach
                                            @else
                                                <span class="badge badge-warning">Unpaid</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('customer.invoice',['id'=>$value->id]) }}" class="invoice_btn" title="View Invoice">
                                            <i data-feather="eye"></i>
                                        </a>
                                        <a href="{{ route('customer.invoice_download', $value->id)}}" class="invoice_btn" title="Download PDF">
                                            <i data-feather="download"></i>
                                        </a>
                                        @if($value->admin_note)
                                            <a href="{{ route('customer.order_note',['id'=>$value->id]) }}" class="invoice_btn" style="background: #e7f0ff; color: #007bff;" title="View Note">
                                                <i data-feather="file-text"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>
</section>

@endsection
