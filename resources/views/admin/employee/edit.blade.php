@extends('layouts.dashboard')

@section('breadcrum')
Employee Management
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <ol class="breadcrumb fs-sm mb-1">
            <li class="breadcrumb-item"><a href="#">Employee Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Employee</li>
        </ol>
        <h4 class="main-title mb-0">Add Employee</h4>
    </div>
</div>

<div class="row g-3">
    <div class="col-xl-12">
        <div class="row g-3">

            <form class="col-12 col-md-12 col-xl-12 pt-3">
                <div class="card card-one card-product">
                    <div class="card-body p-3">
                        <div class="row px-md-4">
                            <div class="w-25">
                                Name
                            </div>
                            <div class="w-75">
                                <input type="text" class="form-control w-100" placeholder="Enter Name" required>
                            </div>
                        </div>
                        <hr/>
                        <div class="row px-md-4">
                            <div class="w-25">
                                Department
                            </div>
                            <div class="w-75">
                                <select id="select2D" class="form-select mobile-w-100" multiple>
                                  <option value="Firefox">Firefox</option>
                                  <option value="Chrome">Chrome</option>
                                  <option value="Safari">Safari</option>
                                  <option value="Opera">Opera</option>
                                  <option value="Internet Explorer">Internet Explorer</option>
                                </select>
                            </div>
                        </div>
                        <hr/>
                        <div class="row px-md-4">
                            <div class="w-25">
                                Email
                            </div>
                            <div class="w-75">
                                <input type="email" class="form-control w-100" placeholder="Enter Email Address" required>
                            </div>
                        </div>
                        <hr/>
                        <div class="row px-md-4">
                            <div class="w-25">
                                Phone Number
                            </div>
                            <div class="w-75">
                                <input type="text" class="form-control w-100" placeholder="Enter Phone Number" required>
                            </div>
                        </div>
                        <div class="text-align-center">
                            <button type="button" class="btn btn-primary active my-5 px-5 text-ali">Add employee </button>
                        </div>
                    </div><!-- card-body -->
                </div><!-- card -->
            </form><!-- col -->


        </div><!-- row -->
    </div><!-- col -->
</div><!-- row -->
@endsection
