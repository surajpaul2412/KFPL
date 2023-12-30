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
                                <div class="font-weight-bold">  </div>
                            </div>
                            <div class="col-3">
                                <div>Ticket Type</div>
                                <div class="font-weight-bold">  </div>
                            </div>
                            <div class="col-3">
                                <div>Payment Mode</div>
                                <div class="font-weight-bold">  </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="row px-md-4">
                            <div class="col-3">
                                <div>Number of Baskets</div>
                                <div class="font-weight-bold">  </div>
                            </div>
                            <div class="col-3">
                                <div>Basket Size</div>
                                <div class="font-weight-bold">  </div>
                            </div>
                            <div class="col-3">
                                <div>Ticket Rate</div>
                                <div class="font-weight-bold">  </div>
                            </div>
                            <div class="col-3">
                                <div>Total Amount</div>
                                <div class="font-weight-bold">  </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="row px-md-4">
                            <div class="col-3">
                                <div>Markup Percentage</div>
                                <div class="font-weight-bold">  </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form class="col-12 col-md-12 col-xl-12 pt-3" method="post" action="{{route('accounts.tickets.update', $ticket->id)}}" enctype="multipart/form-data">
                @csrf
                @method('put')
                <div class="card card-one card-product">
                    <div class="card-body p-3">
                        <div class="row px-md-4">
                            @if($ticket->status_id == 3)
                            <div class="col-6 my-3">
                                <div class="pb-1">
                                    UTR Number
                                </div>
                                <div class="">
                                    <input type="text" name="utr_no" class="form-control @error('utr_no') is-invalid @enderror" value="{{$ticket->utr_no}}" placeholder="Enter UTR no" required>
                                    @error('utr_no')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-6 my-3">
                                <div class="w-25 pb-1">
                                    Upload Screenshot
                                </div>
                                <div class="w-75">
                                    <input type="file" class="form-control w-100 @error('screenshot') is-invalid @enderror" placeholder="Upload Screenshot" name="screenshot" accept="image/*">
                                    @error('screenshot')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            @endif

                            @if($ticket->status_id == 11)
                            <div class="col-6 my-3">
                                <div class="pb-1">
                                    Refund Verification
                                </div>
                                <div class="">
                                    <input type="hidden" name="refund_verification" value="" required>
                                    <span class='verification' onclick="setVerification(0,1)">Accept</span>
                                    <span class='verification' onclick="setVerification(1,2)">Reject</span>
                                    @error('refund_verification')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-6 my-3">
                                <div class="w-25 pb-1">
                                    Expected Refund
                                </div>
                                <div class="w-75">
                                    <input type="text" class="form-control w-100" placeholder="Enter Amount" name="expected_refund"
                                      value=""
                                    >
                                </div>
                            </div>

                            <div class="col-6 my-3">
                                <div class="w-25 pb-1">
                                    Dispute Comment
                                </div>
                                <div class="w-75">
                                    <textarea class="form-control w-100" name="remark" placeholder="Write here"></textarea>
                                </div>
                            </div>
                            @endif
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
