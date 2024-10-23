@extends('layouts.dashboard')

@section('breadcrum')
    AMC Master
@endsection

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between">
        <div>
            <ol class="breadcrumb fs-sm mb-3">
                <li class="breadcrumb-item"><a href="{{ route('amcs.index') }}">AMC Management</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add AMC</li>
            </ol>
            <h4 class="main-title mb-0">Add AMC</h4>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-12">
            <div class="row g-3">
                <form action="{{ route('amcs.store') }}" method="POST" class="col-12 col-md-12 col-xl-12 pt-3">
                    @csrf
                    <div class="card card-one card-product">
                        <div class="card-body p-3">
                            <div class="row px-md-4">
                                <div class="col-6 my-3">
                                    <div class="pb-1">
                                        Name
                                    </div>
                                    <div class="">
                                        <input type="text" name="name" class="form-control w-100 @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Enter Name" required>
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-6 my-3">
                                    <div class="pb-1">
                                        Automailer Mail ID
                                    </div>
                                    <div class="">
                                        <input type="text" name="email" class="form-control w-100 @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Enter Email Id" required>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-6 my-3">
                                    <div class="pb-1">
                                        Demat Account PDF
                                    </div>
                                    <div class="">
                                        <select name="pdf_id" class="form-select mobile-w-100 @error('pdf_id') is-invalid @enderror">
                                            @foreach($pdfs as $pdf)
                                                <option value="{{ $pdf->id }}">{{ $pdf->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('pdf_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-3 my-3">
                                    <div class="pb-1">
                                        Expense Percentage
                                    </div>
                                    <div class="">
                                        <input type="text" name="expense_percentage" class="form-control w-100" placeholder="Enter Expense Percentage" value="{{ old('expense_percentage') }}" required>
                                        @error('expense_percentage')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-3 my-3">
                                    <div class="pb-1">
                                        Status
                                    </div>
                                    <div class="">
                                        <select name="status" class="form-select mb-3">
                                            <option value="">Choose Status</option>
                                            <option value="1" selected>Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                </div>
								
								<div class="col-6 my-3">
                                    <div class="pb-1">
                                        Investor Details
                                    </div>
                                    <div class="">
                                        <textarea name="investordetails" style="width:100%;height:100px" value="{{old('investordetails')}}"></textarea>
                                    </div>
									@error('investordetails')
										<span class="invalid-feedback" role="alert">
											<strong>{{ $message }}</strong>
										</span>
                                    @enderror
                                </div>
								
								<div class="col-6 my-3">
                                    <div class="pb-1">
                                        Bank Details
                                    </div>
                                    <div class="">
                                        <textarea name="bankdetails" style="width:100%;height:100px" value="{{old('bankdetails')}}"></textarea>
                                    </div>
									@error('bankdetails')
										<span class="invalid-feedback" role="alert">
											<strong>{{ $message }}</strong>
										</span>
                                    @enderror
                                </div>
								
								<hr/>
								
								<h5>Email Template Assignments</h5>
								
								<div class="col-3 my-3">
                                    <div class="pb-1">
                                        Buy - Cash
                                    </div>
                                    <div class="">
                                        <select name="buycashtmpl" class="form-select mobile-w-100 @error('buycashtmpl') is-invalid @enderror">
											<option value="">Select an Option</option>
											@foreach($emailtemplates as $emailtemplate)
                                                <option value="{{ $emailtemplate->id }}">{{ $emailtemplate->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('buycashtmpl')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
								
								<div class="col-3 my-3">
                                    <div class="pb-1">
                                        Sell - Cash
                                    </div>
                                    <div class="">
                                        <select name="sellcashtmpl" class="form-select mobile-w-100 @error('sellcashtmpl') is-invalid @enderror">
											<option value="">Select an Option</option>
											@foreach($emailtemplates as $emailtemplate)
                                                <option value="{{ $emailtemplate->id }}">{{ $emailtemplate->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('sellcashtmpl')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
								
								<div class="col-3 my-3">
                                    <div class="pb-1">
                                        Sell - Cash (Without SS)
                                    </div>
                                    <div class="">
                                        <select name="sellcashwosstmpl" class="form-select mobile-w-100 @error('sellcashwosstmpl') is-invalid @enderror">
											<option value="">Select an Option</option>
											@foreach($emailtemplates as $emailtemplate)
                                                <option value="{{ $emailtemplate->id }}">{{ $emailtemplate->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('sellcashwosstmpl')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
								
								<div class="col-3 my-3">
                                    <div class="pb-1">
                                        Mail To Self
                                    </div>
                                    <div class="">
                                        <select name="mailtoselftmpl" class="form-select mobile-w-100 @error('mailtoselftmpl') is-invalid @enderror">
											<option value="">Select an Option</option>
											@foreach($emailtemplates as $emailtemplate)
                                                <option value="{{ $emailtemplate->id }}">{{ $emailtemplate->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('mailtoselftmpl')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="text-align-center">
                                <button type="submit" class="btn btn-primary active mb-4 px-5 text-ali">Create AMC</button>
                            </div>
                        </div><!-- card-body -->
                    </div><!-- card -->
                </form><!-- col -->
            </div><!-- row -->
        </div><!-- col -->
    </div><!-- row -->
@endsection
