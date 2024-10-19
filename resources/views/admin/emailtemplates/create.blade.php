@extends('layouts.dashboard')

@section('breadcrum')
    AMC Master
@endsection

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <ol class="breadcrumb fs-sm mb-3">
                <li class="breadcrumb-item"><a href="{{ route('admin.emailtemplates.index') }}">AMC Email Templates Management</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Email Template</li>
            </ol>
            <h4 class="main-title mb-0">Add Email Template</h4>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-12">
            <div class="row g-3">
                <form action="{{ route('admin.emailtemplates.store') }}" method="POST" class="col-12 col-md-12 col-xl-12 pt-3">
                    @csrf
                    <div class="card card-one card-product">
                        <div class="card-body p-3">
                            <div class="row px-md-4">

                                <div class="col-6 my-3">
                                    <div class="pb-1">
                                        AMC Name <span class="required">*</span>
                                    </div>
                                    <div class="">
                                        <select name="amc_id" class="form-select mobile-w-100 @error('name') is-invalid @enderror" required>
                                            @foreach($amcs as $amc)
                                                <option value="{{ $amc->id }}">{{ $amc->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('amc_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

								<div class="col-6 my-3">
                                    <div class="pb-1">
                                        Template Name <span class="required">*</span>
                                    </div>
                                    <div class="">
                                        <input type="text" name="name" class="form-control w-100 @error('name') is-invalid @enderror" placeholder="Enter Template Name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
								
								<div class="col-6 my-3">
                                    <div class="pb-1">
                                        Type <span class="required">*</span>
                                    </div>
                                    <div class="">
                                        <select name="type" class="form-select mobile-w-100 @error('type') is-invalid @enderror" required>
                                            <option value='1'>Buy</option>
                                            <option value='2'>Sell</option>
                                        </select>
                                        @error('type')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-4 my-3">
                                    <div class="pb-1">
                                        Status <span class="required">*</span>
                                    </div>
                                    <div class="">
                                        <select name="status" class="form-select mb-3">
                                            <option value="">Choose Status</option>
                                            <option value="1" selected>Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                </div>
								
								<div class="col-12 my-3">
                                    <div class="pb-1">
                                        Email Message 
                                    </div>
                                    <div class="">
                                        <textarea name="template" style="width:100%;height:300px;"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="text-align-center">
                                <button type="submit" class="btn btn-primary active mb-4 px-5 text-ali">Create Email Template</button>
                            </div>
                        </div><!-- card-body -->
                    </div><!-- card -->
                </form><!-- col -->
            </div><!-- row -->
        </div><!-- col -->
    </div><!-- row -->
	<!-- CONTENT SECTION ENDS HERE -->
@endsection


