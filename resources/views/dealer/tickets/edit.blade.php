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

            <form class="col-12 col-md-12 col-xl-12 pt-3" method="post" action="{{route('dealer.tickets.update', $ticket->id)}}">
                @csrf
                @method('put')
                <div class="card card-one card-product">
                    <div class="card-body p-3">
                        <div class="row px-md-4">
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
    $(document).ready(function() {
        // Attach a change event listener to the actual_total_amt input
        $('input[name="actual_total_amt"]').on('input', function() {
            // Get the entered value
            var actualTotalAmt = $(this).val();

            // Perform an AJAX request to calculate and update the NAV value
            $.ajax({
                url: '/calculate-purchase-nav', // Replace with your actual route
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
    });
</script>
@endsection
