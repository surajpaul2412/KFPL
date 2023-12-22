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
                            <div class="w-25">
                                Email <span class="required">*</span>
                            </div>
                            <div class="w-75">
                                <input type="email" class="form-control w-100" placeholder="Enter Email Address" name="email"
                                  value="@if(old('email')!=''){{old('email')}}@else{{$ticket->email}}@endif"
                                required >
                            </div>
                        </div>
                        <hr/>
                        <div class="row px-md-4">
                            <div class="w-25">
                                Phone Number
                            </div>
                            <div class="w-75">
                                <input type="text" class="form-control w-100" placeholder="Enter Phone Number" name="phone"
                                  value="@if(old('phone')!=''){{old('phone')}}@else{{$ticket->phone}}@endif"
                                >
                            </div>
                        </div>
                    </div><!-- card-body -->
                </div><!-- card -->
            </div>

            <form class="col-12 col-md-12 col-xl-12 pt-3" method="post" action="{{route('accounts.tickets.update', $ticket->id)}}">
                @csrf
                @method('put')
                <div class="card card-one card-product">
                    <div class="card-body p-3">
                        <div class="row px-md-4">
                            <div class="w-25">
                                Email <span class="required">*</span>
                            </div>
                            <div class="w-75">
                                <input type="email" class="form-control w-100" placeholder="Enter Email Address" name="email"
                                  value="@if(old('email')!=''){{old('email')}}@else{{$ticket->email}}@endif"
                                required >
                            </div>
                        </div>
                        <hr/>
                        <div class="row px-md-4">
                            <div class="w-25">
                                Phone Number
                            </div>
                            <div class="w-75">
                                <input type="text" class="form-control w-100" placeholder="Enter Phone Number" name="phone"
                                  value="@if(old('phone')!=''){{old('phone')}}@else{{$ticket->phone}}@endif"
                                >
                            </div>
                        </div>

                        <div class="text-align-center">
                            <button type="submit" class="btn btn-primary active my-5 px-5 text-ali">Save Employee </button>
                        </div>
                    </div><!-- card-body -->
                </div><!-- card -->
            </form>


        </div>
    </div>
</div>
@endsection
