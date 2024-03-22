@extends('layouts.dashboard')

@section('breadcrum')
    Ticket Management
@endsection

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between">
        <div>
            <ol class="breadcrumb fs-sm mb-3">
                <li class="breadcrumb-item"><a href="/trader/tickets">Ticket Management</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Ticket</li>
            </ol>
            <h4 class="main-title mb-0">{{$ticket->status->stage}}</h4>
        </div>
    </div>

    @include('topmessages')

    <div class="row g-3">
        <div class="col-xl-12">
            <div class="row g-3">
                <form class="col-12 col-md-12 col-xl-12 pt-3" method="post" action="{{ route('trader.tickets.update', $ticket->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="card card-one card-product">
                        <div class="card-body p-3">
                            <div class="row px-md-4">
                                <!-- Include your form fields with their values based on the $ticket variable -->

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
                                <div class="col-3 my-3">
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
                                <div class="col-3 my-3">
                                    <div class="pb-1">
                                      Total Qty
                                    </div>
                                    <div class="calcField">
                                        <input type="text" name="total_qty" class="form-control w-100" value="" placeholder="Basket Size" disabled>
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
                                <div class="col-3 my-3">
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
                                <div class="col-3 my-3 sellopts">
                                    <div class="pb-1">
                                      Markup Price
                                    </div>
                                    <div class="calcField">
                                        <input type="text" name="markup_price" class="form-control w-100" disabled>
                                    </div>
                                </div>

                                <!-- Total Amount -->
                                <div class="col-3 my-3">
                                    <div class="pb-1">
                                        Total Amount
                                    </div>
                                    <div class="calcField">
                                        <input type="text" name="total_amt" class="form-control w-100 @error('total_amt') is-invalid @enderror" value="{{ $ticket->total_amt }}" placeholder="Enter Total Amt" disabled>
                                        <!-- <input type="hidden" name="total_amt" class="form-control w-100 @error('total_amt') is-invalid @enderror" value="{{ $ticket->total_amt }}" placeholder="Enter Total Amt"> -->
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

                                <!-- Update button text for edit page -->
                                <div class="text-align-center">
                                    <button type="submit" class="btn btn-primary active mb-4 px-5 text-ali">Update Ticket</button>
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
            var basketNo = parseFloat($('#no_basket').val());
            var basketSize = parseFloat($('input[name="basket_size"]').val());
            var rate = parseFloat($('input[name="rate"]').val());
            var markupPercentage = parseFloat($('input[name="markup_percentage"]').val());

            // Check if all values are available and not empty
            if (basketNo && basketSize && rate && markupPercentage) {
                var totalAmount = (basketNo * basketSize * rate) + (basketNo * basketSize * rate) * markupPercentage / 100;
                var markupPrice = rate + rate * markupPercentage/100;
                totalAmount = parseFloat(totalAmount.toFixed(2));
                markupPrice = parseFloat(markupPrice.toFixed(2));

                $('input[name="total_amt"]').val(totalAmount);
                $('input[name="total_qty"]').val(basketNo * basketSize);
                $('input[name="markup_price"]').val(markupPrice);
            }
        }
    });
</script>
@endsection
