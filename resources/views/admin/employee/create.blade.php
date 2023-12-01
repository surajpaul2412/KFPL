@extends('layouts.dashboard')

@section('breadcrum')
Employee Management
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <ol class="breadcrumb fs-sm mb-1">
            <li class="breadcrumb-item"><a href="#">Employee Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Employee</li>
        </ol>
        <h4 class="main-title mb-0">Add Employee</h4>
    </div>
</div>

<div class="row g-3">
    <div class="col-xl-5">
        <div class="row g-3">
            <div class="col-6 col-md-3 col-xl-6">
                <div class="card card-one card-product">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-5">
                            <div class="card-icon"><i class="ri-shopping-bag-fill"></i></div>
                            <h6 class="fw-normal ff-numerals text-success mb-0">+28.5%</h6>
                        </div>
                        <h2 class="card-value ls--1"><span>$</span>14,803.80</h2>
                        <label class="card-label fw-medium text-dark">Total Sales</label>
                        <span class="d-flex gap-1 fs-xs">
                            <span class="d-flex align-items-center text-success">
                                <span class="ff-numerals">2.3%</span><i class="ri-arrow-up-line"></i>
                            </span>
                            <span class="text-secondary">than last week</span>
                        </span>
                    </div><!-- card-body -->
                </div><!-- card -->
            </div><!-- col -->
            <div class="col-6 col-md-3 col-xl-6">
                <div class="card card-one card-product">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-5">
                            <div class="card-icon"><i class="ri-shopping-basket-fill"></i></div>
                            <h6 class="fw-normal ff-numerals text-success mb-0">+20.9%</h6>
                        </div>
                        <h2 class="card-value ls--1">18,060</h2>
                        <label class="card-label fw-medium text-dark">Products Sold</label>
                        <span class="d-flex gap-1 fs-xs">
                            <span class="d-flex align-items-center text-success">
                                <span class="ff-numerals">5.8%</span><i class="ri-arrow-up-line"></i>
                            </span>
                            <span class="text-secondary">than last week</span>
                        </span>
                    </div><!-- card-body -->
                </div><!-- card -->
            </div><!-- col -->
        </div><!-- row -->
    </div><!-- col -->
</div><!-- row -->
@endsection
