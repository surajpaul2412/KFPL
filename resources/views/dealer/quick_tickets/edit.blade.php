@extends('layouts.dashboard')

@section('breadcrum')
Ticket Management
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between">
    <div>
        <ol class="breadcrumb fs-sm mb-3">
            <li class="breadcrumb-item"><a href="/dealer/quick_tickets">Ticket Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Quick Ticket</li>
        </ol>
        <h4 class="main-title mb-0">Edit Quick Ticket</h4>
    </div>
</div>

@include('topmessages')

<div class="row g-3">
    <div class="col-xl-12">
        <div class="row g-3">
            <form class="col-12 col-md-12 col-xl-12 pt-3" method="post" action="{{ route('dealer.quick_tickets.update', $ticket->id) }}">
                @csrf
                @method('PUT')
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

                            <div class="col-12 my-3">
                                <div class="pb-1">
                                    Payment Type
                                </div>
                                <div class="">
                                    <input type="hidden" name="payment_type" value="{{ $ticket->payment_type }}" required>
                                    <span class='payMode {{ $ticket->payment_type == 1 ? "selected" : "" }}' onclick="setPaymode(0,1)">Cash</span>
                                    <span class='payMode {{ $ticket->payment_type == 2 ? "selected" : "" }}' onclick="setPaymode(1,2)">Basket</span>
                                    @php
                                    //<span class='payMode {{ $ticket->payment_type == 3 ? "selected" : "" }}' onclick="setPaymode(2,3)">Net Settlement</span>
                                    @endphp
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

                            <div class="col-6 my-3 sellopts">
                               <div style='width:49%;float:left;'>
                                <div class="pb-1">
                                    Actual Trade Value
                                </div>
                                <div class="">
                                    <input type="number" class="form-control w-100" placeholder="Add Actual Trade Value" step=".01" name="actual_total_amt" 
									value="{{ number_format($ticket->actual_total_amt,2) }}" required>
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
                                        <option value="0" {!! $ticket->trader_id == '0' ? ' selected="selected" ':'' !!}>-- All Traders --</option>
										@foreach($traders as $trader)
                                        <option value="{{ $trader->id }}" {{ $ticket->trader_id == $trader->id ? 'selected' : '' }}>{{ $trader->name }}</option>
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
                            <button type="submit" class="btn btn-primary active mb-4 px-5 text-ali">Update Quick Ticket</button>
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



    $('input[name="actual_total_amt"]').on('input', function() {
          // Get the entered value
          var actualTotalAmt = $(this).val();

          // Perform an AJAX request to calculate and update the NAV value
          $.ajax({
              url: '/dealer-calculate-purchase-nav', // Replace with your actual route
              method: 'POST',
              data: { actual_total_amt: actualTotalAmt, _token: '{{ csrf_token() }}' },
              success: function(data) {
                  // Update the NAV input with the calculated value
                  $('input[name="nav"]').val(data.navValue);
              },
              error: function(error) {
                  console.error('Error:', error);
              }
          });
       });
</script>
@endsection
