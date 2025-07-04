@extends('layouts.dashboard')

@section('breadcrum')
    AMC Security Master
@endsection

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <ol class="breadcrumb fs-sm mb-3">
                <li class="breadcrumb-item"><a href="{{ route('securities.index') }}">Security Management</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Security</li>
            </ol>
            <h4 class="main-title mb-0">Add Security</h4>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-12">
            <div class="row g-3">
                <form action="{{ route('securities.store') }}" method="POST" class="col-12 col-md-12 col-xl-12 pt-3">
                    @csrf
                    <div class="card card-one card-product">
                        <div class="card-body p-3">
                            <div class="row px-md-4">
                                <div class="col-6 my-3">
                                    <div class="pb-1">
                                        Name
                                    </div>
                                    <div class="">
                                        <input type="text" name="name" class="form-control w-100 @error('name') is-invalid @enderror" placeholder="Enter Name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-6 my-3">
                                    <div class="pb-1">
                                        AMC Name
                                    </div>
                                    <div class="">
                                        <select name="amc_id" class="form-select mobile-w-100 @error('name') is-invalid @enderror">
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
                                        Symbol
                                    </div>
                                    <div class="">
                                        <input type="text" name="symbol" class="form-control w-100" placeholder="Enter Symbol" value="{{ old('symbol') }}" required>
                                        @error('symbol')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-6 my-3">
                                    <div class="pb-1">
                                        ISIN
                                    </div>
                                    <div class="">
                                        <input type="text" name="isin" class="form-control w-100" placeholder="Enter ISIN" value="{{ old('isin') }}" required>
                                        @error('isin')
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
                                    <div class="">
                                        <input type="text" name="basket_size" class="form-control w-100" placeholder="Enter Basket Size" value="{{ old('basket_size') }}" required>
                                        @error('basket_size')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-6 my-3">
                                    <div class="pb-1">
                                        Markup Percentage
                                    </div>
                                    <div class="">
                                        <input type="text" name="markup_percentage" class="form-control w-100" placeholder="Enter Markup Percentage" value="{{ old('markup_percentage') }}" required>
                                        @error('markup_percentage')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-4 my-3">
                                    <div class="pb-1">
                                        Price
                                    </div>
                                    <div class="">
                                        <input type="text" name="price" class="form-control w-100" placeholder="Enter Price" value="{{ old('price') }}" required>
                                        @error('price')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-4 my-3">
                                    <div class="pb-1">
                                        Cash Component
                                    </div>
                                    <div class="">
                                        <input type="text" name="cash_component" class="form-control w-100" placeholder="Enter Cash Component" value="{{ old('cash_component') }}" required>
                                        @error('cash_component')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-4 my-3">
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
                            </div>

                            <div class="text-align-center">
                                <button type="submit" class="btn btn-primary active mb-4 px-5 text-ali">Create Security</button>
                            </div>
                        </div><!-- card-body -->
                    </div><!-- card -->
                </form><!-- col -->
            </div><!-- row -->
        </div><!-- col -->
    </div><!-- row -->
@endsection
