@extends('layouts.dashboard')

@section('breadcrum')
Dashboard
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <ol class="breadcrumb fs-sm mb-1">
            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Trader Dashboard</li>
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
        <label class="card-title fs-sm fw-medium mb-1 text-success">BUY - Executed</label>
        <h3 class="card-value mb-1"><i class="ri-shopping-bag-3-line"></i> <span>₹</span> {{ convertToCrore($data['buyExecuted']) }} Cr</h3>
      </div><!-- card-body -->
    </div><!-- card-one -->
  </div><!-- col -->
  <div class="col-6 col-xl-3">
    <div class="card card-one py-2">
      <div class="card-body">
        <label class="card-title fs-sm fw-medium mb-1 text-success">BUY - Quick Ticket</label>
        <h3 class="card-value mb-1"><i class="ri-briefcase-4-line"></i> <span>₹</span>{{ convertToCrore($data['buyQuickTicket']) }} Cr</h3>
      </div><!-- card-body -->
    </div><!-- card-one -->
  </div><!-- col -->
  <div class="col-6 col-xl-3">
    <div class="card card-one py-2">
      <div class="card-body">
        <label class="card-title fs-sm fw-medium mb-1 text-danger">SELL - Executed</label>
        <h3 class="card-value mb-1"><i class="ri-shopping-bag-3-line"></i> <span>₹</span> {{ convertToCrore($data['sellExecuted']) }} Cr</h3>
      </div><!-- card-body -->
    </div><!-- card-one -->
  </div><!-- col -->
  <div class="col-6 col-xl-3">
    <div class="card card-one py-2">
      <div class="card-body">
        <label class="card-title fs-sm fw-medium mb-1 text-danger">SELL - Quick Ticket</label>
        <h3 class="card-value mb-1"><i class="ri-briefcase-4-line"></i> <span>₹</span>{{ convertToCrore($data['sellQuickTicket']) }} Cr</h3>
      </div><!-- card-body -->
    </div><!-- card-one -->
  </div><!-- col -->
    
  <div class="col-12">
    <div class="row">
        <div class="col-xl-9">
          <div class="card card-one py-2">

            <div class="card-body p-3">
              <div class="table-responsive">
                <table class="table table-four table-bordered">
                  <thead>
                      <tr>
                          <th colspan="4">Buy</th>
                          <th colspan="4">Sell</th>
                      </tr>
                      <tr>
                          <th>ID</th>
                          <th>Symbol</th>
                          <th>No of Basket</th>
                          <th>NAV</th>
                          <th>ID</th>
                          <th>Symbol</th>
                          <th>No of Basket</th>
                          <th>NAV</th>
                      </tr>
                  </thead>
                  <tbody>
                      @php
                          $maxRows = max(count($data['buyTickets']), count($data['sellTickets']));
                      @endphp

                      @for ($i = 0; $i < $maxRows; $i++)
                          <tr>
                              <td>{{ $data['buyTickets'][$i]->id ?? '-' }}</td>
                              <td>{{ $data['buyTickets'][$i]->security->symbol ?? '-' }}</td>
                              <td>
                                  @if (isset($data['buyTickets'][$i]))
                                      @php
                                          $basketNo = $data['buyTickets'][$i]->basket_no ?? 0;
                                          $basketSize = $data['buyTickets'][$i]->basket_size ?? 0;
                                          $total = $basketNo * $basketSize;
                                      @endphp
                                      {{ $total }}
                                  @else
                                      -
                                  @endif
                              </td>
                              <td>{{ $data['buyTickets'][$i]->nav ?? '-' }}</td>

                              <td>{{ $data['sellTickets'][$i]->id ?? '-' }}</td>
                              <td>{{ $data['sellTickets'][$i]->security->symbol ?? '-' }}</td>
                              <td>
                                  @if (isset($data['sellTickets'][$i]))
                                      @php
                                          $basketNo = $data['sellTickets'][$i]->basket_no ?? 0;
                                          $basketSize = $data['sellTickets'][$i]->basket_size ?? 0;
                                          $total = $basketNo * $basketSize;
                                      @endphp
                                      {{ $total }}
                                  @else
                                      -
                                  @endif
                              </td>
                              <td>{{ $data['sellTickets'][$i]->nav ?? '-' }}</td>
                          </tr>
                      @endfor
                  </tbody>
                </table>
              </div>
            </div>

          </div><!-- card -->
        </div>
        <div class="col-xl-3">
            <div class="row">
                <div class="col-12 col-xl-12">

                  <div class="card card-one">
                    <div class="card-body p-3">
                      <div class="d-block fs-40 lh-1 text-primary mb-1"><i class="ri-calendar-todo-line"></i></div>
                      <h1 class="card-value mb-0 ls--1 fs-32">{{$data['buyQuickTicketCount']}}</h1>
                      <label class="d-block mb-1 fw-medium text-success">Buy Quick Tickets</label>
                    </div><!-- card-body -->
                  </div>

                </div>
                <div class="col-12 col-xl-12 mt-3">
                  <div class="card card-one">
                    <div class="card-body p-3">
                      <div class="d-block fs-40 lh-1 text-primary mb-1"><i class="ri-calendar-check-line"></i></div>
                      <h1 class="card-value mb-0 ls--1 fs-32">{{$data['sellQuickTicketCount']}}</h1>
                      <label class="d-block mb-1 fw-medium text-danger">Sell Quick Tickets</label>
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

@endsection
