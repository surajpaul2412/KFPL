@extends('layouts.dashboard')

@section('breadcrum')
    Email Sender Master
@endsection

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between">
        <div>
            <ol class="breadcrumb fs-sm mb-3">
                <li class="breadcrumb-item"><a href="{{ route('senderemail.index') }}">Email Sender Management</a></li>
                <li class="breadcrumb-item active" aria-current="page">Email Sender AMC</li>
            </ol>
            <h4 class="main-title mb-0">Edit Email Sender</h4>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-12">
            <div class="row g-3">
                <form action="{{ route('senderemail.update', $emailsender->id) }}" method="POST" class="col-12 col-md-12 col-xl-12 pt-3">
                     @csrf
					 @method('PUT')
                    <div class="card card-one card-product">
                        <div class="card-body p-3">
                            <div class="row px-md-4">
                                <div class="col-6 my-3">
                                    <div class="pb-1">
                                        Host
                                    </div>
                                    <div class="">
                                        <input type="text" name="host" class="form-control w-100 @error('host') is-invalid @enderror" value="{{ old('host', $emailsender->host) }}" placeholder="Enter Host" required>
                                        @error('host')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-3 my-3">
                                    <div class="pb-1">
                                        Port
                                    </div>
                                    <div class="">
                                        <input type="text" name="port" class="form-control w-100 @error('port') is-invalid @enderror" value="{{ old('port', $emailsender->port) }}" placeholder="Enter Port" required>
                                        @error('port')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
								
								<div class="col-3 my-3">
                                    <div class="pb-1">
                                        Encryption
                                    </div>
                                    <div class="">
                                        <select name="encryption" class="form-select mb-3">
                                            <option value="">Choose Encryption</option>
                                            <option value="TLS" {{$emailsender->encryption=="TLS" ? "selected":""}}>TLS</option>
                                            <option value="SSL" {{$emailsender->encryption=="SSL" ? "selected":""}}>SSL</option>
                                        </select>
                                    </div>
                                </div>
								
								<div class="col-6 my-3">
                                    <div class="pb-1">
                                        Username
                                    </div>
                                    <div class="">
                                        <input type="text" name="username" class="form-control w-100 @error('username') is-invalid @enderror" value="{{ old('username', $emailsender->username) }}" placeholder="Enter Username" required>
                                        @error('username')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
								
								<div class="col-6 my-3">
                                    <div class="pb-1">
                                        Password
                                    </div>
                                    <div class="">
                                        <input type="text" name="password" class="form-control w-100 @error('password') is-invalid @enderror" value="{{ old('password', $emailsender->password) }}" placeholder="Enter Password" required>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
								
								
								
								
								<div class="col-6 my-3">
                                    <div class="pb-1">
                                        From Email
                                    </div>
                                    <div class="">
                                        <input type="text" name="from_address" class="form-control w-100 @error('from_address') is-invalid @enderror" value="{{ old('from_address', $emailsender->from_address) }}" placeholder="Enter Email" required>
                                        @error('from_address')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
								
								<div class="col-6 my-3">
                                    <div class="pb-1">
                                        From Name
                                    </div>
                                    <div class="">
                                        <input type="text" name="from_name" class="form-control w-100 @error('from_name') is-invalid @enderror" value="{{ old('from_name', $emailsender->from_name) }}" placeholder="Enter From Name" required>
                                        @error('from_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
								
								<div class="col-6 my-3">
                                    <div class="pb-1">
                                        Reply To Address
                                    </div>
                                    <div class="">
                                        <input type="text" name="reply_to_address" class="form-control w-100 @error('reply_to_address') is-invalid @enderror" value="{{ old('reply_to_address', $emailsender->reply_to_address) }}" placeholder="Enter From Name" />
                                        @error('reply_to_address')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-6 my-3">
                                    <div class="pb-1">
                                        Status
                                    </div>
                                    <div class="">
                                        <select name="status" class="form-select mb-3" required>
                                            <option value="">Choose Status</option>
                                            <option value="1" {{$emailsender->status=="1" ? "selected":""}}>Active</option>
                                            <option value="0" {{$emailsender->status=="0" ? "selected":""}}>Inactive</option>
                                        </select>
                                    </div>
                                </div>
								
                            </div>

                            <div class="text-align-center">
                                <button type="submit" class="btn btn-primary active mb-4 px-5 text-ali">Update Email Sender</button>
                            </div>
                        </div><!-- card-body -->
                    </div><!-- card -->
                </form><!-- col -->
            </div><!-- row -->
        </div><!-- col -->
    </div><!-- row -->
@endsection
