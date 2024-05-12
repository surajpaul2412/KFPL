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

                  <div class="card card-one py-2">
                    <div class="card-body">
                      <label class="card-title fs-sm fw-medium mb-1">Unique Purchases</label>
                      <h3 class="card-value mb-1"><i class="ri-shopping-bag-3-line"></i> 8,327</h3>
                    </div><!-- card-body -->
                  </div><!-- card-one -->

                </div>
                <div class="col-12 col-xl-12 mt-3">
                  <div class="card card-one py-2">
                    <div class="card-body">
                      <label class="card-title fs-sm fw-medium mb-1">Unique Purchases</label>
                      <h3 class="card-value mb-1"><i class="ri-shopping-bag-3-line"></i> 8,327</h3>
                    </div><!-- card-body -->
                  </div><!-- card-one -->
                </div>
                <div class="col-12 col-xl-12 mt-3">
                    <div class="card card-one">
                      <div class="card card-one py-2">
                        <div class="card-body">
                          <label class="card-title fs-sm fw-medium mb-1">Unique Purchases</label>
                          <h3 class="card-value mb-1"><i class="ri-shopping-bag-3-line"></i> 8,327</h3>
                        </div><!-- card-body -->
                      </div><!-- card-one -->
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>

  <div class="col-12">
    <div class="row">
        <div class="col-xl-12">
          <div class="card card-one py-2">

            <div class="card-body p-3">
              <div class="table-responsive">
                <table class="table table-four table-bordered">
                  <thead>
                    <tr>
                      <th>&nbsp;</th>
                      <th colspan="2">Buy</th>
                      <th colspan="2">Sell</th>
                    </tr>
                    <tr>
                      <th>Symbol Name</th>
                      <th>No. of Basket</th>
                      <th>NAV</th>
                      <th>No. of Basket</th>
                      <th>NAV</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><a href="">Organic search</a></td>
                      <td>350</td>
                      <td>22</td>
                      <td>5,628</td>
                      <td>25.60%</td>
                    </tr>
                    <tr>
                      <td><a href="">Social media</a></td>
                      <td>276</td>
                      <td>18</td>
                      <td>5,100</td>
                      <td>23.66%</td>
                    </tr>
                    <tr>
                      <td><a href="">Referral</a></td>
                      <td>246</td>
                      <td>17</td>
                      <td>4,880</td>
                      <td>26.22%</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

          </div><!-- card -->
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
    labels: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12','13','14'],
    datasets: [{
      data: [1, 2, 3, 4, 5, 6, 7, 4, 4, 3, 2, 3, 0, 2],
      backgroundColor: '#0cb785',
      barPercentage: 0.5
    }, {
      data: [1, 2, 3, 4, 5, 6, 7, 4, 4, 3, 2, 3, 0, 2],
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
        max: 10,
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
