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
                                <div class="font-weight-bold"> {{$ticket->security->symbol}} </div>
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
                                <div class="font-weight-bold">  {{$ticket->security->basket_size}}  </div>
                            </div>
                            <div class="col-3">
                                <div>Ticket Rate</div>
                                <div class="font-weight-bold"> {{$ticket->rate}} </div>
                            </div>
                            <div class="col-3">
                                <div>Total Amount</div>
                                <div class="font-weight-bold"> {{$ticket->total_amt}} </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="row px-md-4">
                            <div class="col-3">
                                <div>Markup Percentage</div>
                                <div class="font-weight-bold"> {{$ticket->security->markup_percentage}} </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form class="col-12 col-md-12 col-xl-12 pt-3" method="post" action="{{route('ops.tickets.update', $ticket->id)}}">
                @csrf
                @method('put')
                <div class="card card-one card-product">
                    <div class="card-body p-3">
                        @if($ticket->status_id == 2)
                        <div class="row px-md-4">
                            <div class="col-6 my-3">
                                <div class="pb-1">
                                    Verification
                                </div>
                                <div class="">
                                    <input type="hidden" name="verification" value="" required>
                                    <span class='verification' onclick="setVerification(0,1)">Accept</span>
                                    <span class='verification' onclick="setVerification(1,2)">Reject</span>
                                    @error('verification')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-6 my-3">
                                <div class="w-25">
                                    Edit Transaction Rate
                                </div>
                                <div class="w-75">
                                    <input type="text" class="form-control w-100" placeholder="Edit Transaction Rate" name="rate"
                                      value="{{$ticket->rate}}"
                                    >
                                </div>
                            </div>

                            <div class="col-6 my-3">
                                <div class="w-25">
                                    Remark
                                </div>
                                <div class="w-75">
                                    <textarea class="form-control w-100" name="remark" placeholder="Write here">{{$ticket->remark}}</textarea>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($ticket->status_id == 9)
                        <div class="row px-md-4">
                            <div class="col-6 my-3">
                                <div class="w-25 pb-1">
                                    Refund Amount
                                </div>
                                <div class="w-75">
                                    <input type="text" class="form-control w-100" placeholder="Refund Amount" name="refund_amt"
                                      value="{{$ticket->refund_amt}}" readonly
                                    >
                                </div>
                            </div>

                            <div class="col-6 my-3">
                                <div class="w-25 pb-1">
                                    Upload Deal Ticket
                                </div>
                                <div class="w-75">
                                    <input type="file" class="form-control w-100" placeholder="Upload" name="deal_ticket"
                                      value=""
                                    >
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($ticket->status_id == 13)
                        <div class="row px-md-4">
                            <div class="col-6 my-3">
                                <div class="pb-1">
                                    Units verification
                                </div>
                                <div class="">
                                    <input type="hidden" name="" value="" required>
                                    <span class='verification' onclick="setVerification(0,1)">Accept</span>
                                    <span class='verification' onclick="setVerification(1,2)">Reject</span>
                                    @error('units_verification')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-6 my-3">
                                <div class="w-25 pb-1">
                                    Received Units
                                </div>
                                <div class="w-75">
                                    <input type="text" class="form-control w-100" placeholder="Enter units" name=""
                                      value=""
                                    >
                                </div>
                            </div>

                            <div class="col-6 my-3">
                                <div class="w-25 pb-1">
                                    Dispute Comment
                                </div>
                                <div class="w-75">
                                    <textarea class="form-control w-100" name="" placeholder="Write here"></textarea>
                                </div>
                            </div>
                        </div>
                        @endif

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
