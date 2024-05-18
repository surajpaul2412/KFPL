@extends('layouts.dashboard')

@section('breadcrum')
Dashboard
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <ol class="breadcrumb fs-sm mb-1">
            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Accounts Dashboard</li>
        </ol>
        <h4 class="main-title mb-0">Welcome to Dashboard</h4>
    </div>

    <div class="d-flex align-items-center gap-2 mt-3 mt-md-0">
      <form class="form-inline d-flex">
        <div class="form-group mr-3">
            <!-- <label for="from_date" class="mr-2">From:</label> -->
            <input type="date" class="form-control" id="from_date" name="from_date">
        </div>
        <div class="form-group mx-3">
            <!-- <label for="to_date" class="mr-2">To:</label> -->
            <input type="date" class="form-control" id="to_date" name="to_date">
        </div>
        <button type="submit" class="btn btn-white btn-icon"><i class="ri-search-line fs-18 lh-1"></i></button>
      </form>
    </div>
</div>

<div class="row g-3">
  <div class="col-6 col-xl-3">
    <div class="card card-one">
      <div class="card-body">
        <label class="card-title fs-sm fw-medium mb-1 text-success">BUY - Executed</label>
        <h3 class="card-value mb-1"><i class="ri-shopping-bag-3-line"></i> <span>₹</span> {{ convertToCrore($data['buyExecuted']) }} Cr</h3>
      </div><!-- card-body -->
    </div><!-- card-one -->
  </div><!-- col -->
  <div class="col-6 col-xl-3">
    <div class="card card-one">
      <div class="card-body">
        <label class="card-title fs-sm fw-medium mb-1 text-success">BUY - Quick Ticket</label>
        <h3 class="card-value mb-1"><i class="ri-briefcase-4-line"></i> <span>₹</span>{{ convertToCrore($data['buyQuickTicket']) }} Cr</h3>
      </div><!-- card-body -->
    </div><!-- card-one -->
  </div><!-- col -->
  <div class="col-6 col-xl-3">
    <div class="card card-one">
      <div class="card-body">
        <label class="card-title fs-sm fw-medium mb-1 text-danger">SELL - Executed</label>
        <h3 class="card-value mb-1"><i class="ri-shopping-bag-3-line"></i> <span>₹</span> {{ convertToCrore($data['sellExecuted']) }} Cr</h3>
      </div><!-- card-body -->
    </div><!-- card-one -->
  </div><!-- col -->
  <div class="col-6 col-xl-3">
    <div class="card card-one">
      <div class="card-body">
        <label class="card-title fs-sm fw-medium mb-1 text-danger">SELL - Quick Ticket</label>
        <h3 class="card-value mb-1"><i class="ri-briefcase-4-line"></i> <span>₹</span>{{ convertToCrore($data['sellQuickTicket']) }} Cr</h3>
      </div><!-- card-body -->
    </div><!-- card-one -->
  </div><!-- col -->

  <div class="col-12">
    <div class="row">
        <div class="col-xl-9">
          <div class="card card-one">
            <div class="card-header">
              <h6 class="card-title">Current Ticket Status</h6>
            </div><!-- card-header -->
            <div class="card-body">
              <div class="chartjs-one"><canvas id="chartJS1"></canvas></div>
            </div><!-- card-body -->
          </div><!-- card -->
        </div>
        <div class="col-xl-3">
          <div class="row">
            <div class="col-6 col-xl-6">
              <div class="card card-one">
                <div class="card-body" align="center">
                  <h2 class="">{{ $data['unitsToBeTransfered'] }}</h2>
                  <label class="">Units To Be Transfered</label>
                </div>
              </div>
            </div>
            <div class="col-6 col-xl-6">
              <div class="card card-one">
                <div class="card-body" align="center">
                  <h2 class="">{{ $data['unitsTransfered'] }}</h2>
                  <label class="">Units Transfered</label>
                </div>
              </div>
            </div>
            <div class="col-12 col-xl-12 mt-3">
              <div class="card card-one">
                <div class="card-body">
                  <div class="d-flex d-sm-block d-xl-flex align-items-center">
                    <div class="helpdesk-icon bg-ui-02 text-white"><i class="ri-blaze-fill"></i></div>
                    <div class="ms-3 ms-sm-0 ms-xl-3 mt-sm-3 mt-xl-0">
                      <h2 class="card-value d-flex align-items-baseline mb-0">{{ $data['redemptionAmountReceivable'] }}</h2>
                      <label class="card-label fs-sm fw-medium mb-1">Redemption Amount Receivable</label>                          
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 col-xl-12 mt-3">
                <div class="card card-one">
                  <div class="card-body">
                    <div class="d-flex d-sm-block d-xl-flex align-items-center">
                      <div class="helpdesk-icon bg-primary text-white"><i class="ri-bell-line"></i></div>
                      <div class="ms-3 ms-sm-0 ms-xl-3 mt-sm-3 mt-xl-0">
                        <h2 class="card-value d-flex align-items-baseline mb-0">{{ $data['redemptionAmountReceived'] }}</h2>
                        <label class="card-label fs-sm fw-medium mb-1">Redemption Amount Received</label>                            
                      </div>
                    </div>
                  </div><!-- card-body -->
                </div><!-- card -->
            </div>
            <div class="col-12 col-xl-12 mt-3">
                <div class="card card-one">
                  <div class="card-body">
                    <div class="d-flex d-sm-block d-xl-flex align-items-center">
                      <div class="helpdesk-icon bg-ui-03 text-white"><i class="ri-star-smile-line"></i></div>
                      <div class="ms-3 ms-sm-0 ms-xl-3 mt-sm-3 mt-xl-0">
                        <h2 class="card-value d-flex align-items-baseline mb-0">{{ $data['refundAmountReceived'] }}</h2>
                        <label class="card-label fs-sm fw-medium mb-1">Refund Amount to be Received</label>                            
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
    labels: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12','13','14'],
    datasets: [{
      data: <?php echo json_encode($data['arrangedBuyCounts']); ?>,
      backgroundColor: '#0cb785',
      barPercentage: 0.5
    }, {
      data: <?php echo json_encode($data['arrangedSellCounts']); ?>,
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
        beginAtZero:false,
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
