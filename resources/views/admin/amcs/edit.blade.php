@extends('layouts.dashboard')

@section('breadcrum')
    AMC Master
@endsection

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between">
        <div>
            <ol class="breadcrumb fs-sm mb-3">
                <li class="breadcrumb-item"><a href="{{ route('amcs.index') }}">AMC Management</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit AMC</li>
            </ol>
            <h4 class="main-title mb-0">Edit AMC</h4>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-12">
            <div class="row g-3">
                <form action="{{ route('amcs.update', $amc->id) }}" method="POST" class="col-12 col-md-12 col-xl-12 pt-3">
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
                                        <input type="text" name="name" class="form-control w-100 @error('name') is-invalid @enderror" placeholder="Enter Name" value="{{ old('name', $amc->name) }}" required>
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
                                        <input type="email" name="email" class="form-control w-100 @error('email') is-invalid @enderror" placeholder="Enter Email Id" value="{{ old('email', $amc->email) }}" required>
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
                                                <option value="{{ $pdf->id }}" {{ $amc->pdf_id == $pdf->id ? 'selected' : '' }}>{{ $pdf->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('pdf_id')
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
                                        <select name="status" class="form-select mb-3">
                                            <option value="1" {{ $amc->status == 1 ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ $amc->status == 0 ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="text-align-center">
                                <button type="submit" class="btn btn-primary active mb-4 px-5 text-ali">Update AMC</button>
                            </div>
                        </div><!-- card-body -->
                    </div><!-- card -->
                </form><!-- col -->
            </div><!-- row -->
        </div><!-- col -->
    </div><!-- row -->
@endsection
