@extends('layouts.dashboard')

@section('breadcrum')
Ticket Details
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between">
    <div>
        <ol class="breadcrumb fs-sm mb-3">
            <li class="breadcrumb-item"><a href="/accounts/employees">Tickets</a></li>
            <li class="breadcrumb-item active" aria-current="page">Ticket Details</li>
        </ol>
        <h4 class="main-title mb-0">Ticket Details</h4>
    </div>
</div>

@include('topmessages')

<div class="row g-3">
    <div class="col-xl-12">
        <div class="row g-3">

            <div class="col-12 col-md-12 col-xl-12 pt-3" method="post" action="{{route('accounts.tickets.update', $ticket->id)}}">
                <div class="card card-one card-product">
                    <div class="card-body p-3 py-4">
                        <div class="row px-md-4">
                            <div class="col-3">
                                <div>Name</div>
                                <div class="font-weight-bold">{{$ticket->security->amc->name}}</div>
                            </div>
                            <div class="col-3">
                                <div>Symbol</div>
                                <div class="font-weight-bold">{{$ticket->security->symbol}}</div>
                            </div>
                            <div class="col-3">
                                <div>Ticket Type</div>
                                <div class="font-weight-bold"> {{$ticket->type == 1 ? "Buy" : "Sell"}} </div>
                            </div>
                            <div class="col-3">
                                <div>Payment Mode</div>
                                <div class="font-weight-bold">
                                  @php
                                  if($ticket->payment_type == 1) echo "Cash";
                                  else if($ticket->payment_type == 2) echo "Basket";
                                  else if($ticket->payment_type == 3) echo "Net Settlement";
                                  @endphp
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="row px-md-4">
                            <div class="col-3">
                                <div>Number of Baskets</div>
                                <div class="font-weight-bold"> {{$ticket->basket_no}} </div>
                            </div>
                            <div class="col-3">
                                <div>Basket Size</div>
                                <div class="font-weight-bold"> {{$ticket->security->basket_size}} </div>
                            </div>
                            <div class="col-3">
                                <div>Ticket Rate</div>
                                <div class="font-weight-bold"> {{$ticket->rate}}  </div>
                            </div>
                            <div class="col-3">
                                <div>Total Amount</div>
                                <div class="font-weight-bold">{{$ticket->total_amt}}  </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="row px-md-4">
                            <div class="col-3">
                                <div>Markup Percentage</div>
                                <div class="font-weight-bold"> {{$ticket->security->markup_percentage}} </div>
                            </div>
                            <div class="col-3">
                                <div>UTR Number</div>
                                <div class="font-weight-bold">{{$ticket->utr_no}}</div>
                            </div>
                            <div class="col-3">
                                <div>AMC Form </div>
                                <div class="font-weight-bold"><a>Download <i class="ri-download-2-line"></i></a></div>
                            </div>
                            <div class="col-3">
                                <div>Demate PDF</div>
                                <div class="font-weight-bold"> <a>Download <i class="ri-download-2-line"></i></a> </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form class="col-12 col-md-12 col-xl-12 pt-3" method="post" action="{{route('dealer.tickets.update', $ticket->id)}}">
                @csrf
                @method('put')
                <div class="card card-one card-product">
                    <div class="card-body p-3">
                        <div class="row px-md-4">
                            <div class="col-6 my-3">
                                <div class="w-25 pb-1">
                                    Trade Value
                                </div>
                                <div class="w-75">
                                    <input type="text" class="form-control w-100" placeholder="Edit Trade Value" name=""
                                      value="{{$ticket->total_amt}}"
                                    >
                                </div>
                            </div>

                            <div class="col-6 my-3">
                                <div class="w-25 pb-1">
                                    NAV value
                                </div>
                                <div class="w-75">
                                    <input type="text" class="form-control w-100" placeholder="" name="" readonly value="{{$ticket->security->amc->nav}}" >
                                </div>
                            </div>
                        </div>

                        <div class="text-align-center">
                            <button type="submit" class="btn btn-primary active my-5 px-5 text-ali">Submit </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
