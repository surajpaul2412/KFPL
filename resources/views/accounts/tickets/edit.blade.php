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

                <div class="col-12 col-md-12 col-xl-12 pt-3">
                    <div class="card card-one card-product">
                        <div class="card-body p-3 py-4">
                            @include('ticket')
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
                                        Enter Total Amount
                                    </div>
                                    <div class="">
                                        <input type="text" name="total_amt_input" class="form-control @error('total_amt_input') is-invalid @enderror" placeholder="Enter Total Amount" required>
                                        @error('total_amt')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

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
                                    <div class="w-25 pb-1">
                                        Refund Received
                                    </div>
                                    <div class="w-75">
                                        <input type="text" class="form-control w-100" placeholder="Enter Amount" name="expected_refund"
                                          value="{{$ticket->refund??$ticket->expected_refund}}" disabled>
                                    </div>
                                </div>

                                @if($ticket->deal_ticket == null)
                                <div class="col-6 my-3">
                                    <div class="w-25 pb-1">
                                        Upload Deal Ticket
                                    </div>
                                    <div class="w-75">
                                        <input type="file" class="form-control w-100" placeholder="Upload" name="deal_ticket"
                                          value="" >
                                    </div>
                                </div>
                                @endif

                                <div class="col-6 my-3">
                                    <div class="w-25 pb-1">
                                        Dispute Comment
                                    </div>
                                    <div class="w-75">
                                        <textarea class="form-control w-100" name="dispute" placeholder="Write here">{{Session::get('error')??$ticket->dispute}}</textarea>
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

@section('script')
<script>
    function setVerification1(x, y) {
        var verificationInput = document.querySelector("[name='verification']");
        var rateInput = document.querySelector("[name='expected_refund']");

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
