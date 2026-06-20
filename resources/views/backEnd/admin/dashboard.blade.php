@extends('backEnd.layouts.master')
@section('title','Sales Dashboard')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/apexcharts@3.35.5/dist/apexcharts.css" rel="stylesheet">
<style>
.dashboard-banner {
  background: linear-gradient(90deg, #5e72e4 0%, #825ee4 100%);
  color: #fff;
  border-radius: 15px;
  padding: 25px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
}
.dashboard-banner h3 {
  color: #fff !important;
  font-weight: 600;
  margin-bottom: 5px;
}
.dashboard-banner p {
  color: #f5f5f5 !important;
  margin-bottom: 10px;
}
.dashboard-banner .btn-outline-light {
  color: #fff !important;
  border-color: #fff !important;
}
.dashboard-banner .btn-outline-light:hover {
  background: #fff;
  color: #5e72e4 !important;
}
.dashboard-card {
  background: #fff;
  border-radius: 15px;
  padding: 25px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.06);
  transition: 0.3s;
}
.dashboard-card:hover {transform: translateY(-3px);}
.metric-title {font-size:14px;color:#9da5b4;}
.metric-value {font-size:26px;font-weight:700;color:#273444;}
.chart-box {
  background:#fff;
  border-radius:15px;
  padding:20px;
  box-shadow:0 2px 10px rgba(0,0,0,0.06);
}
.table td, .table th {vertical-align: middle;}
</style>
@endsection

@section('content')
<div class="container-fluid py-3">

  <!-- Header -->
  <div class="mb-3">
    <h4 class="fw-bold">Hi! Welcome To Dashboard</h4>
    <small class="text-muted">Home → Sales Dashboard</small>
  </div>

  <!-- Banner -->
  <div class="dashboard-banner mb-4">
    <div>
      <h3>Congratulations {{ Auth::user()->name ?? 'Admin' }} 🎉</h3>
      <p>You have reached your sales milestone! Keep going strong 💪</p>
      <button class="btn btn-light btn-sm me-2" data-bs-toggle="modal" data-bs-target="#salesModal">Check Details</button>
      <button class="btn btn-outline-light btn-sm" onclick="noThanks()">No, Thanks</button>
    </div>
<div class="text-end">
  <h2 class="mb-0">TK {{ number_format($today_profit, 2) }}</h2>
  <p class="mb-0">Today's Profit</p>
</div>


  </div>

  <!-- Modal -->
  <div class="modal fade" id="salesModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-dark">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">📊 Sales Overview</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <ul class="list-group">
<ul class="list-group mb-3">

    <li class="list-group-item d-flex justify-content-between align-items-center">
        <span>Total Orders</span>
        <span class="badge bg-primary rounded-pill">
            {{ $total_order }}
        </span>
    </li>

    <li class="list-group-item d-flex justify-content-between align-items-center">
        <span>Today's Orders</span>
        <span class="badge bg-success rounded-pill">
            {{ $today_order }}
        </span>
    </li>

    <li class="list-group-item d-flex justify-content-between align-items-center">
        <span>Total Products</span>
        <span class="badge bg-info rounded-pill text-dark">
            {{ $total_product }}
        </span>
    </li>

    <li class="list-group-item d-flex justify-content-between align-items-center">
        <span>Total Customers</span>
        <span class="badge bg-warning rounded-pill text-dark">
            {{ $total_customer }}
        </span>
    </li>

    <li class="list-group-item d-flex justify-content-between align-items-center">
        <span>Total Delivered</span>
        <span class="badge bg-success rounded-pill">
            {{ $total_delivery }}
        </span>
    </li>

</ul>
 
          </ul>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Cards Section -->
  <div class="row g-3">
    <div class="col-md-3">
      <div class="dashboard-card">
        <div class="metric-title">Product Sold</div>
        <div class="metric-value">{{ number_format($total_order) }}</div>
        <small class="text-success">↑ 2.6% than last week</small>
      </div>
    </div>
<div class="col-md-3">
  <div class="dashboard-card">
    <div class="metric-title">Total Balance</div>
    <div class="metric-value">TK {{ number_format($fund_balance, 2) }}</div>
    <small class="text-info">Available fund balance</small>
  </div>
</div>

    <div class="col-md-3">
      <div class="dashboard-card">
        <div class="metric-title">Sales Profit</div>
        <div class="metric-value">TK {{ number_format($today_delivery * 12105) }}</div>
        <small class="text-danger">Available Sales Profit</small>
      </div>
    </div>
    <div class="col-md-3">
      <div class="dashboard-card">
        <div class="metric-title">Total Expenses</div>
        <div class="metric-value">TK {{ number_format($total_expenses, 2) }}</div>
        <small class="text-success">Available Total Expenses</small>
      </div>
    </div>
  </div>

  <!-- Charts -->
  <div class="row g-3 mt-2">
    <div class="col-lg-4">
      <div class="chart-box">
        <h5 class="fw-bold mb-3">Sales By Category</h5>
        <div id="categoryChart"></div>
      </div>
    </div>
    <div class="col-lg-8">
      <div class="chart-box">
        <h5 class="fw-bold mb-3">Monthly Sales Statistics</h5>
        <div id="salesChart"></div>
      </div>
    </div>
  </div>

  <!-- Orders & Customers -->
  <div class="row g-3 mt-2">
    <div class="col-lg-8">
      <div class="chart-box">
        <h5 class="fw-bold mb-3">Recent Orders</h5>
        <div class="table-responsive">
          <table class="table table-borderless">
            <thead class="table-light">
              <tr><th>Customer</th><th>Invoice</th><th>Status</th><th>Date</th><th>Action</th></tr>
            </thead>
            <tbody>
            @foreach($latest_order as $order)
              <tr>
                <td>{{ $order->customer->name ?? 'Guest' }}</td>
                <td><a href="{{ route('admin.order.invoice', $order->invoice_id) }}" class="fw-bold">#{{ $order->invoice_id }}</a></td>
                <td>
                  @if($order->order_status == 6)
                    <span class="badge bg-success">Delivered</span>
                  @elseif($order->order_status == 1)
                    <span class="badge bg-info">Pending</span>
                  @else
                    <span class="badge bg-warning">Processing</span>
                  @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}</td>
                <td>
                    <a href="{{ route('admin.order.edit', $order->invoice_id) }}" class="btn btn-sm btn-primary"><i class="fe-eye"></i> View</a>
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="chart-box">
        <h5 class="fw-bold mb-3">Recent Customers</h5>
        <ul class="list-unstyled mb-0">
          @foreach($latest_customer as $cust)
          <li class="py-2 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <strong>{{ $cust->name }}</strong><br>
                <small class="text-muted">{{ $cust->phone ?? 'N/A' }}</small>
            </div>
            <a href="{{ route('customers.edit', $cust->id) }}" class="btn btn-sm btn-light text-primary" title="Edit Customer"><i class="fe-edit"></i></a>
          </li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>

</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.35.5"></script>
<script>
function noThanks() {
  toastr.warning('Maybe later! Keep up the great work 💪');
}

// Donut Chart
var donut = new ApexCharts(document.querySelector("#categoryChart"), {
  chart:{type:'donut',height:270},
  labels:['Men','Women','Kids','Electronics','Furniture'],
  series:[44,33,12,21,10],
  colors:['#7367F0','#28C76F','#EA5455','#FF9F43','#00CFE8'],
  legend:{position:'bottom'}
});
donut.render();

// Line Chart
var salesDates = {!! json_encode($monthly_sale->pluck('date')) !!};
var salesData = {!! json_encode($monthly_sale->pluck('amount')) !!};
var options = {
  chart:{type:'area',height:300,toolbar:{show:false}},
  series:[{name:'Sales',data:salesData}],
  xaxis:{categories:salesDates},
  colors:['#7367F0'],
  stroke:{curve:'smooth',width:2},
  fill:{type:'gradient',gradient:{shadeIntensity:1,opacityFrom:0.6,opacityTo:0.1}},
};
new ApexCharts(document.querySelector("#salesChart"), options).render();
</script>
@endsection
