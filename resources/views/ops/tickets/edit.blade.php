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
                        @include('ticket')
                    </div>
                </div>
            </div>

            <form class="col-12 col-md-12 col-xl-12 pt-3" onsubmit="showWait()" method="post" action="{{route('ops.tickets.update', $ticket->id)}}" enctype="multipart/form-data">
                @csrf
                @method('put')
                <div class="card card-one card-product">
                    <div class="card-body p-3">
                        @if($ticket->status_id == 2 && $ticket->type == 1)
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
                        @endif

                        @if($ticket->status_id == 5)
                          @if($ticket->type == 2)
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
                        @endif

                        @if($ticket->status_id == 9)
                            @if($ticket->type == 1)
                            <div class="col-6 my-3">
                                <div class="w-25 pb-1">
                                    Refund Amount
                                </div>
                                <div class="w-75">
                                    <input type="text" class="form-control w-100" placeholder="Refund Amount" name="refund"
                                      value="{{$ticket->total_amt - $ticket->actual_total_amt}}" readonly  required>
                                </div>
                            </div>

                            @if($ticket->screenshot == null)
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

                            <div class="col-6 my-3">
                                <div class="w-25 pb-1">
                                    Upload Deal Ticket
                                </div>
                                <div class="w-75">
                                    <input type="file" class="form-control w-100" placeholder="Upload" name="deal_ticket"
                                      value="" >
                                </div>
                            </div>
                            @elseif($ticket->type == 2)
                            <div class="col-6 my-3">
                              <div class="w-75 pb-1">
                                Redemption Amount
                              </div>
                              <div class="w-75">
                                <input type="text" class="form-control w-100" placeholder="Redemption Amount" name="refund"
                                  value="" required>
                              </div>
                            </div>
                            @endif                                  
                        @endif

                        @if($ticket->status_id == 10)
                            @if($ticket->type == 2)
                                @if($ticket->screenshot == null)
                                <div class="row">
                                    <div class="col-6 my-3">
                                      <div class="w-25 pb-1">
                                        Upload Screenshot
                                      </div>
                                      <div class="w-75">
                                        <input type="file" class="form-control w-100 @error('screenshot') is-invalid @enderror" placeholder="Upload Screenshot" name="screenshot" accept="image/*" required>
                                        @error('screenshot')
                                          <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                          </span>
                                        @enderror
                                      </div>
                                    </div>
                                    @endif

                                    <div class="col-6 my-3">
                                        <div class="w-75 pb-1">
                                            Upload Deal Ticket
                                        </div>
                                        <div class="w-75">
                                            <input type="file" class="form-control w-100" placeholder="Upload" name="deal_ticket"
                                              value="" required>
                                        </div>
                                    </div>
                                </div>
                              @endif
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
