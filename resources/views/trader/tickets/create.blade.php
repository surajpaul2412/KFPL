@extends('layouts.dashboard')

@section('breadcrum')
Ticket Management
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between">
    <div>
        <ol class="breadcrumb fs-sm mb-3">
            <li class="breadcrumb-item"><a href="/trader/tickets">Ticket Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Ticket</li>
        </ol>
        <h4 class="main-title mb-0">Add Ticket</h4>
    </div>
</div>

@include('topmessages')

<div class="row g-3">
    <div class="col-xl-12">
        <div class="row g-3">
            <form class="col-12 col-md-12 col-xl-12 pt-3" method="post" action="{{ route('tickets.store') }}">
                @csrf
                <div class="card card-one card-product">
                    <div class="card-body p-3">
                        <div class="row px-md-4">
                            <div class="col-6 my-3">
                                <div class="pb-1">
                                    Name
                                </div>
                                <div class="">
                                    <select name="security_id" class="form-select mobile-w-100 @error('security_id') is-invalid @enderror">
                                        @foreach($securities as $security)
                                            <option value="">--Select Security--</option>
                                            <option value="{{ $security->id }}">{{ $security->name }}</option>
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
                                    <span class='ticketType' onclick="setTicketType(0,1)">Buy</span>
                                    <span class='ticketType' onclick="setTicketType(1,2)">Sell</span>
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
                                    <input type="hidden" name="pay_mode" value="" required>
                                    <span class='payMode' onclick="setPaymode(0,1)">Cash</span>
                                    <span class='payMode' onclick="setPaymode(1,2)">Basket</span>
                                    <span class='payMode' onclick="setPaymode(2,3)">Net Settlement</span>
                                    @error('pay_mode')
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
                                    <input style="width:150px;" type="text" name="no_basket" class="form-control w-100 @error('no_basket') is-invalid @enderror"
                                    value="{{ old('no_basket') }}" placeholder="Enter No. of Basket" required>
                                    @error('no_basket')
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

                                </div>
                            </div>

                            <div class="col-6 my-3">
                                <div class="pb-1">
                                    Enter Rate
                                </div>
                                <div class="">
                                    <input style="width:150px;" type="text" name="rate" class="form-control w-100 @error('rate') is-invalid @enderror"
                                    value="{{ old('rate') }}" placeholder="Enter Rate" required>
                                    @error('rate')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-6 my-3">
                                <div class="pb-1">
                                  Current Rate
                                </div>
                                <div class="calcField">

                                </div>
                            </div>

                            <div class="col-6 my-3">
                               <div style='width:49%;float:left;'>
                                <div class="pb-1">
                                  Total Amount
                                </div>
                                <div class="calcField">

                                </div>
                              </div>
                              <div style='width:49%;float:right;'>
                                <div class="pb-1">
                                  Marup Percentage
                                </div>
                                <div class="calcField">

                                </div>
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
                    // $('input[name="no_basket"]').val(data.basket_size);
                    console.log("hii");
                    console.log(data.security.basket_size);
                    // Update other fields similarly
                },
                error: function () {
                    console.log('Error fetching security details');
                }
            });
        });
    });
</script>
@endsection
