@extends('backEnd.layouts.master')
@section('title', 'Manual Fraud Check')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm p-4">
        <h4 class="text-center fw-bold mb-4">
            আপনার যাচাই করতে চাওয়া মোবাইল নাম্বারটি দিয়ে নিচে সার্চ দিন
        </h4>

        {{-- 🔍 Search Box --}}
        <form action="{{ route('manualFraud.check') }}" method="POST" class="text-center mb-5">
            @csrf
            <div class="input-group justify-content-center" style="max-width:400px; margin:auto;">
                <input type="text" name="mobile" value="{{ $mobile ?? '' }}" class="form-control text-center"
                    placeholder="017XXXXXXXX" required>
                <button type="submit" class="btn btn-success px-4">সার্চ দিন</button>
            </div>
        </form>

        {{-- ✅ Result Section --}}
        @if(isset($data))
        <div class="row justify-content-center">

            {{-- 🟢 Left: Overall Success --}}
            <div class="col-md-4 text-center mb-4">
                <div class="card bg-light border-0 shadow-sm p-4">
                    <h5 class="fw-bold text-white bg-success py-2 rounded">মোট সফলতার হার</h5>
                    <div class="mt-3 mb-2">
                        <h3 class="fw-bold text-success"># {{ $mobile }}</h3>
                    </div>

                    @php
                        // ✅ Safe access for fraud rate
                        $fraudRate = data_get($data, 'fraud.rate', null);
                        $fraudText = $fraudRate !== null && is_numeric($fraudRate)
                            ? $fraudRate . '%'
                            : 'N/A';
                    @endphp

                    <div style="
                        width:140px; height:140px;
                        border-radius:50%;
                        border:10px solid #28a745;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        margin:15px auto;
                        background-color:#e9f7ef;
                    ">
                        <span class="fw-bold text-success fs-3">{{ $fraudText }}</span>
                    </div>

                    @if($fraudRate === null)
                        <p class="text-muted mt-2">
                            <i class="fa fa-frown-o"></i> কোনো তথ্য খুঁজে পাওয়া যায়নি।
                        </p>
                        <small class="text-danger d-block">
                            এই নাম্বারের বিষয়ে বিস্তারিত তথ্য পাওয়া যায়নি। অতিরিক্ত যাচাইয়ের জন্য সাপোর্টে যোগাযোগ করুন।
                        </small>
                    @else
                        <p class="text-muted mt-2">
                            <i class="fa fa-check-circle text-success"></i>
                            সফলতার হার: <strong>{{ $fraudRate }}%</strong>
                        </p>
                    @endif
                </div>
            </div>

            {{-- 📦 Right: Courier Details --}}
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center">
                            <thead class="table-success">
                                <tr>
                                    <th>Courier</th>
                                    <th>Total Orders</th>
                                    <th>Complete Orders</th>
                                    <th>Cancelled Orders</th>
                                    <th>Success Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // ✅ External courier logos (change if you want)
                                    $courier_logos = [
                                        'pathao' => 'https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEgcPXG_8CWTYYU0_FyPinFgEYz1jHIBKahM-nuhaUAXNDHSUeeyhVOgJ-HGwlyaxa6I2mioxBTxgY7b4HtgHfSA3InL6bb2pvEAb_1fLUba_m22FUd2fvGFwsJE1l78bn7uPiN3pakGlmqBi0rPUqr0kNDnYj_FrCsu9KR59-S9U738gkwm9x_5F4w_4jdD/s1600/images.jpg',
                                        'redx' => 'https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEgmF9SvE23xvfNH51QUaogIAY5WxaO7stjnKxTpDIoXBcAgYHSiqwepC1msZ1tZd3Yu8gUstvIPzlziG4cd5KOLXGXk-qcWqwheAZD_i58Ckgkq0VnsUxMo8leUTbwFI_Cx931IvT1wXd2Lxond4APjCFSVb_3DZr_1DWMxvEaoUfB2QKgf7zoQL22hdK73/s320/redx.png',
                                        'steadfast' => 'https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEikvpCwwbVvg0ntt23WlqiX_oZ5LHUgeWJ1UqVAJE4SOMRv6UErkBnymB6JxToyPOIJ5jXbvrCPW_5pz-drWNn1bQzzggETH1gf_RW88vy_22iGtSjxcZCi88UEnUsK-wyOTPnVlevSj2ieuNWlqnZCiG8FP6iGRffRzzQAZCiGQmCwieWnr2TSD5F7FlY-/s1600/steadfast.png',
                                        'paperfly' => 'https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEh1WHCAITk3w5R-YdpMli28iD1OJbiGsR5EUQTksLhZtvLRhGbI2GVf7pAsbfj_zhYrkhV4TNLC07VsKA7X12EzbAsNCSZhJWqaO_bh7aQ4_ydGZuYnNq6B63CwkfEdDkIxz1neK1ukIdcsCn-3_tuuLfLfvOHLRtDjzsW6k1VIkaKL2livspAsbkgLToa7/s1600/paperfly.png',
                                    ];

                                    $couriers = [
                                        'pathao' => 'Pathao',
                                        'redx' => 'RedX',
                                        'steadfast' => 'SteadFast',
                                        'paperfly' => 'PaperFly',
                                    ];
                                @endphp

                                @foreach($couriers as $key => $name)
                                    @php
                                        $total = data_get($data, "{$key}.total", 0);
                                        $success = data_get($data, "{$key}.success", 0);
                                        $cancel = data_get($data, "{$key}.cancel", 0);
                                        $rate = data_get($data, "{$key}.rate", 0);
                                    @endphp
                                    <tr>
                                        <td class="fw-bold text-dark">
                                            <img src="{{ $courier_logos[$key] ?? '' }}" alt="{{ $name }}" width="35" height="35"
                                                style="object-fit:contain; margin-right:5px;">
                                            {{ $name }}
                                        </td>
                                        <td>{{ $total }}</td>
                                        <td>{{ $success }}</td>
                                        <td>{{ $cancel }}</td>
                                        <td>
                                            <span class="badge {{ $rate >= 70 ? 'bg-success' : ($rate >= 40 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                                {{ $rate }}%
                                            </span>
                                            <div class="small text-muted mt-1">
                                                @if($total == 0)
                                                    No History
                                                @elseif($rate == 0)
                                                    New Customer
                                                @elseif($rate < 50)
                                                    Risky Customer
                                                @else
                                                    High Return Customer
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
