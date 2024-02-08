@extends('layouts.dashboard')

@section('breadcrum')
Ticket Management
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between">
    <div>
        <ol class="breadcrumb fs-sm mb-3">
            <li class="breadcrumb-item"><a href="/accounts/employees">Ticket Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Ticket</li>
        </ol>
        <h4 class="main-title mb-0">{{$ticket->status->stage}}</h4>
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
                                <div class="font-weight-bold">  {{$ticket->basket_size}}  </div>
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
                                <div class="font-weight-bold"> {{$ticket->markup_percentage}} </div>
                            </div>
                            @if($ticket->utr_no)
                            <div class="col-3">
                                <div>UTR Number</div>
                                <div class="font-weight-bold">{{$ticket->utr_no}}</div>
                            </div>
                            @endif
                            <div class="col-3">
                                <div>AMC Form </div>
                                <div class="font-weight-bold"><a>Download <i class="ri-download-2-line"></i></a></div>
                            </div>
                            <div class="col-3">
                                <div>Demate PDF</div>
                                <div class="font-weight-bold"> <a>Download <i class="ri-download-2-line"></i></a> </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="row px-md-4">
                            <div class="col-3">
                                <div>Trade Value</div>
                                <div class="font-weight-bold"> {{$ticket->actual_total_amt}} </div>
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
                                    <span class='verification' onclick="setVerification1(0,1)">Accept</span>
                                    <span class='verification' onclick="setVerification1(1,2)">Reject</span>
                                    @error('verification')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-6 my-3">
                                <div class="w-25">
                                    Edit Ticket Rate
                                </div>
                                <div class="w-75">
                                    <input type="number" step="any" class="form-control w-100" placeholder="Edit Ticket Rate" name="rate" value="{{ $ticket->rate }}" disabled>
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
                                    <input type="text" class="form-control w-100" placeholder="Refund Amount" name="refund"
                                      value="" required>
                                </div>
                            </div>

                            <div class="col-6 my-3">
                                <div class="w-25 pb-1">
                                    Upload Deal Ticket
                                </div>
                                <div class="w-75">
                                    <input type="file" class="form-control w-100" placeholder="Upload" name="deal_ticket"
                                      value="" >
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($ticket->status_id == 13)
                        <div class="row px-md-4">
                            <div class="col-6 my-3">
                                <div class="w-25 pb-1">
                                    Received Units
                                </div>
                                <div class="w-75">
                                    <input type="text" class="form-control w-100" placeholder="Enter units" name="received_units" value="{{$ticket->basket_size * $ticket->basket_no}}">
                                </div>
                            </div>

                            <div class="col-6 my-3">
                                <div class="w-25 pb-1">
                                    Dispute Comment
                                </div>
                                <div class="w-75">
                                    <textarea class="form-control w-100" name="dispute_comment" placeholder="Write here">{{$ticket->dispute_comment}}</textarea>
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

@section('script')
<script>
    function setVerification1(x, y) {
        var verificationInput = document.querySelector("[name='verification']");
        var rateInput = document.querySelector("[name='rate']");

        if (verificationInput) {
            verificationInput.value = y;
        }

        // Toggle the "disabled" attribute based on the verification status
        if (rateInput) {
            rateInput.disabled = (y !== 1); // Adjust the value based on your accepted verification logic
        }

        // Highlight the selected verification status
        document.querySelectorAll(".verification").forEach(function(element) {
            element.classList.remove('selected');
        });

        document.querySelectorAll(".verification")[x].classList.add('selected');
    }
</script>
@endsection
