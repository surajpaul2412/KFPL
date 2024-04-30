@extends('layouts.dashboard')

@section('breadcrum')
Dashboard
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <ol class="breadcrumb fs-sm mb-1">
            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Admin Dashboard</li>
        </ol>
        <h4 class="main-title mb-0">Welcome to Dashboard</h4>
    </div>

    <div class="d-flex align-items-center gap-2 mt-3 mt-md-0">
        <button type="button" class="btn btn-white btn-icon"><i class="ri-share-line fs-18 lh-1"></i></button>
        <button type="button" class="btn btn-white btn-icon"><i class="ri-printer-line fs-18 lh-1"></i></button>
        <button type="button" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="ri-bar-chart-2-line fs-18 lh-1"></i>Generate<span class="d-none d-sm-inline"> Report</span>
        </button>
    </div>
</div>

<div class="row g-3">
  <div class="col-6 col-xl-3">
    <div class="card card-one py-2">
      <div class="card-body">
        <label class="card-title fs-sm fw-medium mb-1">Unique Purchases</label>
        <h3 class="card-value mb-1"><i class="ri-shopping-bag-3-line"></i> 8,327</h3>
      </div><!-- card-body -->
    </div><!-- card-one -->
  </div><!-- col -->
  <div class="col-6 col-xl-3">
    <div class="card card-one py-2">
      <div class="card-body">
        <label class="card-title fs-sm fw-medium mb-1">Order Value</label>
        <h3 class="card-value mb-1"><i class="ri-briefcase-4-line"></i> <span>$</span>12,105</h3>
      </div><!-- card-body -->
    </div><!-- card-one -->
  </div><!-- col -->
  <div class="col-6 col-xl-3">
    <div class="card card-one py-2">
      <div class="card-body">
        <label class="card-title fs-sm fw-medium mb-1">Order Quantity</label>
        <h3 class="card-value mb-1"><i class="ri-inbox-line"></i> 4,598</h3>
      </div><!-- card-body -->
    </div><!-- card-one -->
  </div><!-- col -->
  <div class="col-6 col-xl-3">
    <div class="card card-one py-2">
      <div class="card-body">
        <label class="card-title fs-sm fw-medium mb-1">Conversion Rate</label>
        <h3 class="card-value mb-1"><i class="ri-bar-chart-box-line"></i> 6.28<span>%</span></h3>
      </div><!-- card-body -->
    </div><!-- card-one -->
  </div><!-- col -->

  
  <div class="col-12">
    <div class="row">
        <div class="col-xl-9">
          <div class="card card-one py-2">
            <div class="card-header">
              <h6 class="card-title">Current Ticket Status</h6>
              <nav class="nav nav-icon nav-icon-sm ms-auto">
                <a href="" class="nav-link"><i class="ri-refresh-line"></i></a>
                <a href="" class="nav-link"><i class="ri-more-2-fill"></i></a>
              </nav>
            </div><!-- card-header -->
            <div class="card-body">
              <div class="chartjs-one"><canvas id="chartJS1"></canvas></div>
            </div><!-- card-body -->
          </div><!-- card -->
        </div>
        <div class="col-xl-3">
            <div class="row">
                <div class="col-12 col-xl-12">
                  <div class="card card-one">
                    <div class="card-body p-3">
                      <div class="d-flex d-sm-block d-xl-flex align-items-center">
                        <div class="helpdesk-icon bg-ui-02 text-white"><i class="ri-blaze-fill"></i></div>
                        <div class="ms-3 ms-sm-0 ms-xl-3 mt-sm-3 mt-xl-0">
                          <h2 class="card-value d-flex align-items-baseline mb-0">296 </h2>
                          <label class="card-label fs-sm fw-medium mb-1">Complaints Received</label>
                          
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-xl-12 mt-3">
                    <div class="card card-one">
                      <div class="card-body p-3">
                        <div class="d-flex d-sm-block d-xl-flex align-items-center">
                          <div class="helpdesk-icon bg-primary text-white"><i class="ri-bell-line"></i></div>
                          <div class="ms-3 ms-sm-0 ms-xl-3 mt-sm-3 mt-xl-0">
                            <h2 class="card-value d-flex align-items-baseline mb-0">387 </h2>
                            <label class="card-label fs-sm fw-medium mb-1">Support Requests</label>
                            
                          </div>
                        </div>
                      </div><!-- card-body -->
                    </div><!-- card -->
                </div>
                <div class="col-12 col-xl-12 mt-3">
                    <div class="card card-one">
                      <div class="card-body p-3">
                        <div class="d-flex d-sm-block d-xl-flex align-items-center">
                          <div class="helpdesk-icon bg-ui-03 text-white"><i class="ri-star-smile-line"></i></div>
                          <div class="ms-3 ms-sm-0 ms-xl-3 mt-sm-3 mt-xl-0">
                            <h2 class="card-value d-flex align-items-baseline mb-0">198 </h2>
                            <label class="card-label fs-sm fw-medium mb-1">Complaints Resolved</label>
                            
                          </div>
                        </div>
                      </div><!-- card-body -->
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>


</div><!-- row -->
@endsection

@section('script')
<script type="text/javascript">
var ctx1 = document.getElementById('chartJS1').getContext('2d');
var chart1 = new Chart(ctx1, {
  type: 'bar',
  data: {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    datasets: [{
      data: [20, 60, 50, 45, 50, 60, 70, 40, 45, 35, 25, 30],
      backgroundColor: '#0cb785',
      barPercentage: 0.5
    }, {
      data: [10, 40, 30, 40, 60, 55, 45, 35, 30, 20, 15, 20],
      backgroundColor: '#dc3545',
      barPercentage: 0.5
    }]
  },
  options: {
    maintainAspectRatio: false,
    responsive: true,
    plugins: {
      legend: {
        display: false
      }
    },
    scales: {
      y: {
        beginAtZero:true,
        max: 80,
        ticks: {
          color: '#a1aab3',
          font: {
            size: 10
          }
        },
        grid: {
          borderColor: '#e2e5ec',
          borderWidth: 1.5,
          color: 'rgba(65,80,95,.08)'
        }
      },
      x: {
        ticks: {
          color: '#313c47'
        },
        grid: {
          color: 'rgba(65,80,95,.08)'
        }
      }
    }
  }
});
</script>
@endsection
