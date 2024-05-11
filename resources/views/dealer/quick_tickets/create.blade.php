@extends('layouts.dashboard')

@section('breadcrum')
Ticket Management
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between">
    <div>
        <ol class="breadcrumb fs-sm mb-3">
            <li class="breadcrumb-item"><a href="/dealer/quick_tickets">Ticket Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Quick Ticket</li>
        </ol>
        <h4 class="main-title mb-0">Add Ticket</h4>
    </div>
</div>

@include('topmessages')

<div class="row g-3">
    <div class="col-xl-12">
        <div class="row g-3">
            <form class="col-12 col-md-12 col-xl-12 pt-3" method="post" action="{{ route('dealer.quick_tickets.store') }}">
                @csrf
                <div class="card card-one card-product">
                    <div class="card-body p-3">
                        <div class="row px-md-4">
                            <div class="col-6 my-3">
                                <div class="pb-1">
                                    Name
                                </div>
                                <div class="">
                                    <select id="select2B" name="security_id" class="form-select mobile-w-100 @error('security_id') is-invalid @enderror" required>
                                        <option label="Choose one"></option>
                                        @foreach($securities as $security)
                                            <option value="{{ $security->id }}">{{ $security->name }} -- ({{ $security->symbol }})</option>
                                        @endforeach
                                    </select>
                                    @error('security_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-6 my-3">
                                <div class="pb-1">
                                    Ticket Type
                                </div>
                                <div class="">
                                    <input type="hidden" name="type" value="" required>
                                    <span class='ticketType' onclick="setTicketType(0,1);showhidefields(1);">Buy</span>
                                    <span class='ticketType' onclick="setTicketType(1,2);showhidefields(0);">Sell</span>
                                    @error('type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-6 my-3">
                                <div class="pb-1">
                                    Payment Type
                                </div>
                                <div class="">
                                    <input type="hidden" name="payment_type" value="" required>
                                    <span class='payMode defaultPayMode' onclick="setPaymode(0,1)" data-value="1">Cash</span>
                                    <span class='payMode' onclick="setPaymode(1,2)" data-value="2">Basket</span>
                                    @php
                                    // <span class='payMode' onclick="setPaymode(2,3)" data-value="3">Net Settlement</span>
                                    @endphp
                                    
                                    @error('payment_type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-6 my-3">
                                <div class="pb-1">

                                </div>
                                <div class="">

                                </div>
                            </div>

                            <div class="col-6 my-3">
                                <div class="pb-1">
                                    Enter No. of Basket
                                </div>
                                <div class="">
                                    <input type="text" name="basket_no" class="form-control w-100 @error('basket_no') is-invalid @enderror" value="{{ old('basket_no') }}" placeholder="Enter No. of Basket" id="no_basket" required>
                                    @error('basket_no')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-6 my-3">
                                <div class="pb-1">
                                  Basket Size
                                </div>
                                <div class="calcField">
                                    <input type="text" name="basket_size" class="form-control w-100 @error('basket_size') is-invalid @enderror" value="{{ old('basket_size') }}" placeholder="Basket Size" disabled>
                                    <input type="hidden" name="basket_size" class="form-control w-100 @error('basket_size') is-invalid @enderror" value="{{ old('basket_size') }}" placeholder="Basket Size">
                                    @error('basket_size')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-6 my-3">
                               <div style='width:49%;float:left;'>
                                <div class="pb-1">
                                    Actual Trade Value
                                </div>
                                <div class="">
                                    <input type="number" class="form-control w-100" placeholder="Add Actual Trade Value" name="actual_total_amt" value="" required>
                                    @error('total_amt')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                              </div>
                            </div>

                            <div class="col-6 my-3">
                                <div class="pb-1">
                                  Select trader
                                </div>
                                <div class="calcField">
                                    <select id="select2B" name="trader_id" class="form-select mobile-w-100 @error('security_id') is-invalid @enderror" required>
                                        <option value="">Select Trader</option>
                                        @foreach($traders as $trader)
                                        <option value="{{$trader->id}}">{{$trader->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('trader_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="text-align-center">
                            <button type="submit" class="btn btn-primary active mb-4 px-5 text-ali">Create Ticket</button>
                        </div>
                    </div><!-- card-body -->
                </div><!-- card -->
            </form><!-- col -->

        </div><!-- row -->
    </div><!-- col -->
</div>
@endsection

@section('script')
<script>
    function showhidefields(show)
    {
      if (show){
         jQuery(".sellopts").show();
      } else {
         jQuery(".sellopts").hide();
      }
    }

    $(document).ready(function () {
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
                    $('input[name="security_price"]').val(data.security.price);
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
                var totalAmount = (basketNo * basketSize * rate) + (basketNo * basketSize * rate) * markupPercentage / 100;
                totalAmount = Math.round(totalAmount);

                $('input[name="total_amt"]').val(totalAmount);
            }
        }
    });



    // $('input[name="actual_total_amt"]').on('input', function() {
    //       // Get the entered value
    //       var actualTotalAmt = $(this).val();

    //       // Perform an AJAX request to calculate and update the NAV value
    //       $.ajax({
    //           url: '/dealer-calculate-purchase-nav', // Replace with your actual route
    //           method: 'POST',
    //           data: { actual_total_amt: actualTotalAmt, _token: '{{ csrf_token() }}' },
    //           success: function(data) {
    //               // Update the NAV input with the calculated value
    //               $('input[name="nav"]').val(data.navValue);
    //           },
    //           error: function(error) {
    //               console.error('Error:', error);
    //           }
    //       });
    //    });
</script>
@endsection
