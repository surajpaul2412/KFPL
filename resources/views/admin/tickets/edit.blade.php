@extends('layouts.dashboard')

@section('breadcrum')
    Ticket Management
@endsection

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between">
        <div>
            <ol class="breadcrumb fs-sm mb-3">
                <li class="breadcrumb-item"><a href="/admin/tickets">Ticket Management</a></li>
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
                                    <div class="font-weight-bold">
                                      {{$ticket->type == 1 ? "Buy" : "Sell"}}
                                    </div>
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

                <form class="col-12 col-md-12 col-xl-12 pt-3" method="post" action="{{ route('admin.tickets.update', $ticket->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card card-one card-product">
                        <div class="card-body p-3">
                            <div class="row px-md-4">
                                <!-- Include your form fields with their values based on the $ticket variable -->

                                @if($ticket->status_id == 1)
                                    <div class="col-6 my-3">
                                        <div class="pb-1">
                                            Name
                                        </div>
                                        <div class="">
                                            <select id="select2B" name="security_id" class="form-select mobile-w-100 @error('security_id') is-invalid @enderror" required>
                                                <option label="Choose one"></option>
                                                @foreach($securities as $security)
                                                    <option value="{{ $security->id }}" {{ $ticket->security_id == $security->id ? 'selected' : '' }}>{{ $security->name }} -- ({{ $security->symbol }})</option>
                                                @endforeach
                                            </select>
                                            @error('security_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Ticket Type -->
                                    <div class="col-6 my-3">
                                        <div class="pb-1">
                                            Ticket Type
                                        </div>
                                        <div class="">
                                            <input type="hidden" name="type" value="{{ $ticket->type }}" required>
                                            <span class='ticketType {{ $ticket->type == 1 ? "selected" : "" }}' onclick="setTicketType(0,1)">Buy</span>
                                            <span class='ticketType {{ $ticket->type == 2 ? "selected" : "" }}' onclick="setTicketType(1,2)">Sell</span>
                                            @error('type')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Payment Type -->
                                    <div class="col-12 my-3">
                                        <div class="pb-1">
                                            Payment Type
                                        </div>
                                        <div class="">
                                            <input type="hidden" name="payment_type" value="{{ $ticket->payment_type }}" required>
                                            <span class='payMode {{ $ticket->payment_type == 1 ? "selected" : "" }}' onclick="setPaymode(0,1)">Cash</span>
                                            <span class='payMode {{ $ticket->payment_type == 2 ? "selected" : "" }}' onclick="setPaymode(1,2)">Basket</span>
                                            <span class='payMode {{ $ticket->payment_type == 3 ? "selected" : "" }}' onclick="setPaymode(2,3)">Net Settlement</span>
                                            @error('payment_type')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Enter No. of Basket -->
                                    <div class="col-6 my-3">
                                        <div class="pb-1">
                                            Enter No. of Basket
                                        </div>
                                        <div class="">
                                            <input type="text" name="basket_no" class="form-control w-100 @error('no_basket') is-invalid @enderror" value="{{ $ticket->basket_no }}" placeholder="Enter No. of Basket" id="no_basket" required>
                                            @error('basket_no')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Basket Size -->
                                    <div class="col-6 my-3">
                                        <div class="pb-1">
                                            Basket Size
                                        </div>
                                        <div class="calcField">
                                            <input type="text" name="basket_size" class="form-control w-100 @error('basket_size') is-invalid @enderror" value="{{ $ticket->security->basket_size }}" placeholder="Basket Size" disabled>
                                            <input type="hidden" name="basket_size" class="form-control w-100 @error('basket_size') is-invalid @enderror" value="{{ $ticket->security->basket_size }}" placeholder="Basket Size">
                                            @error('basket_size')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Repeat similar blocks for other form fields -->

                                    <!-- Enter Rate -->
                                    <div class="col-6 my-3">
                                        <div class="pb-1">
                                            Enter Rate
                                        </div>
                                        <div class="">
                                            <input type="text" name="rate" class="form-control w-100 @error('rate') is-invalid @enderror"
                                                value="{{ $ticket->rate }}" placeholder="Enter Rate" required>
                                            @error('rate')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Current Price -->
                                    <div class="col-6 my-3">
                                        <div class="pb-1">
                                            Current Price
                                        </div>
                                        <div class="calcField">
                                            <input type="text" name="price" class="form-control w-100 @error('price') is-invalid @enderror"
                                                value="{{ $ticket->security->price }}" placeholder="Enter Price" disabled>
                                            <input type="hidden" name="price" class="form-control w-100 @error('price') is-invalid @enderror"
                                                value="{{ $ticket->security->price }}" placeholder="Enter Price" disabled>
                                            @error('price')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Total Amount -->
                                    <div class="col-3 my-3">
                                        <div class="pb-1">
                                            Total Amount
                                        </div>
                                        <div class="calcField">
                                            <input type="text" name="total_amt" class="form-control w-100 @error('total_amt') is-invalid @enderror" value="{{ $ticket->total_amt }}" placeholder="Enter Total Amt" disabled>
                                            <input type="hidden" name="total_amt" class="form-control w-100 @error('total_amt') is-invalid @enderror" value="{{ $ticket->total_amt }}" placeholder="Enter Total Amt">
                                            @error('total_amt')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Markup Percentage -->
                                    <div class="col-3 my-3">
                                        <div class="pb-1">
                                            Markup Percentage
                                        </div>
                                        <div class="calcField">
                                            <input type="text" name="markup_percentage" class="form-control w-100 @error('markup_percentage') is-invalid @enderror" value="{{ $ticket->security->markup_percentage }}" placeholder="Enter Markup Percentage" disabled>
                                            <input type="hidden" name="markup_percentage" class="form-control w-100 @error('markup_percentage') is-invalid @enderror" value="{{ $ticket->security->markup_percentage }}" placeholder="Enter Markup Percentage">
                                            @error('markup_percentage')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                @endif

                                <!-- EXTRA FIELDS ADDITION :: STARTS -->
                                @if($ticket->status_id == 8)
                                    <div class="col-6 my-3">
                                        <div class="w-25 pb-1">
                                            Actual Trade Value
                                        </div>
                                        <div class="w-75">
                                            <input type="text" class="form-control w-100" placeholder="Add Actual Trade Value" name="actual_total_amt" value="" required>
                                        </div>
                                    </div>

                                    <div class="col-6 my-3">
                                        <div class="w-25 pb-1">
                                            NAV value
                                        </div>
                                        <div class="w-75">
                                            <input type="text" class="form-control w-100" placeholder="NAV Value" name="nav" readonly value="" >
                                        </div>
                                    </div>
                                @endif
                                @if($ticket->status_id == 2)
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

                                @if($ticket->status_id == 9)
                                	<div class="col-6 my-3">
                                		<div class="w-25 pb-1">
                                			Refund Amount
                                		</div>
                                		<div class="w-75">
                                			<input type="text" class="form-control w-100" placeholder="Refund Amount" name="refund"
                                			  value="{{$ticket->total_amt - $ticket->actual_total_amt}}" readonly  required>
                                		</div>
                                	</div>

                                	<div class="col-6 my-3">
                                		<div class="w-25 pb-1">
                                			Upload Deal Ticket
                                		</div>
                                		<div class="w-75">
                                			<input type="file" class="form-control w-100" placeholder="Upload" name="deal_ticket"
                                			  value="" required >
                                		</div>
                                	</div>
                                @endif

                                @if($ticket->status_id == 3)
                                    <div class="col-6 my-3">
                                    	<div class="pb-1">
                                    		Enter Total Amount
                                    	</div>
                                    	<div class="">
                                    		<input type="text" name="total_amt" class="form-control @error('total_amt') is-invalid @enderror" placeholder="Enter Total Amount" required>
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
                                            <span class='verification' onclick="setVerification2(0,1)">Accept</span>
                                            <span class='verification' onclick="setVerification2(1,2)">Reject</span>
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

                                    <div class="col-6 my-3">
                                        <div class="w-25 pb-1">
                                            Dispute Comment
                                        </div>
                                        <div class="w-75">
                                            <textarea class="form-control w-100" name="dispute" placeholder="Write here">{{Session::get('error')??$ticket->dispute}}</textarea>
                                        </div>
                                    </div>
                                @endif

                                @if($ticket->status_id == 13)
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
                                @endif

                                @if($ticket->status_id == 14)
                                    <div class="col-12 my-3" align="center">
                                        <div class="">
                                            Congratulations !! your ticket has been closed.
                                        </div>
                                    </div>
                                @endif

                                <!-- EXTRA FIELDS ADDITION :: ENDS -->

                                <!-- Update button text for edit page -->
                                <div class="text-align-center">
                                    @if($ticket->status_id != 14)
                                    <button type="submit" class="btn btn-primary active mb-4 px-5 text-ali">Update Ticket</button>
                                    @endif
                                </div>
                            </div>
                        </div><!-- card-body -->
                    </div><!-- card -->
                </form><!-- col -->

            </div><!-- row -->
        </div><!-- col -->
    </div><!-- row -->
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

    function setVerification2(x, y) {
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

    $(document).ready(function () {

      // Attach a change event listener to the actual_total_amt input
      $('input[name="actual_total_amt"]').on('input', function() {
          // Get the entered value
          var actualTotalAmt = $(this).val();

          // Perform an AJAX request to calculate and update the NAV value
          $.ajax({
              url: '/admin-calculate-purchase-nav', // Replace with your actual route
              method: 'POST',
              data: { actual_total_amt: actualTotalAmt, ticket_id: '{{$ticket->id}}', _token: '{{ csrf_token() }}' },
              success: function(data) {
                  // Update the NAV input with the calculated value
                  $('input[name="nav"]').val(data.navValue);
              },
              error: function(error) {
                  console.error('Error:', error);
              }
          });
       });


        // Change event handler for the security select
        $('select[name="security_id"]').change(function () {
            var securityId = $(this).val();

            // Make an Ajax request to fetch security details
            $.ajax({
                url: '/get-security-details/' + securityId, // Replace with your actual route
                type: 'GET',
                success: function (data) {
                    // Fill the fields with the fetched data
                    $('input[name="basket_size"]').val(data.security.basket_size);
                    $('input[name="price"]').val(data.security.price);
                    $('input[name="rate"]').val(data.security.price);
                    $('input[name="markup_percentage"]').val(data.security.markup_percentage);

                    // Update other fields similarly

                    // Recalculate total amount when security is changed
                    calculateTotalAmount();
                },
                error: function () {
                    console.log('Error fetching security details');
                }
            });
        });

        // Event handler for basket_no, basket_size, and rate input fields
        $('#no_basket, input[name="basket_size"], input[name="rate"], input[name="markup_percentage"]').on('input', function () {
            calculateTotalAmount();
        });

        // Function to calculate total amount
        function calculateTotalAmount() {
            var basketNo = $('#no_basket').val();
            var basketSize = $('input[name="basket_size"]').val();
            var rate = $('input[name="rate"]').val();
            var markupPercentage = $('input[name="markup_percentage"]').val();

            // Check if all values are available and not empty
            if (basketNo && basketSize && rate && markupPercentage) {
                var totalAmount = (basketNo * basketSize * rate) + (basketNo * basketSize * rate * markupPercentage / 100);
                $('input[name="total_amt"]').val(totalAmount);
            }
        }
    });
</script>
@endsection
