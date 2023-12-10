@extends('layouts.dashboard')

@section('breadcrum')
Employee Management
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <ol class="breadcrumb fs-sm mb-1">
            <li class="breadcrumb-item"><a href="/admin/employees">Employee Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Employee</li>
        </ol>
        <h4 class="main-title mb-0">Add Employee</h4>
    </div>
</div>

@include('topmessages')

<div class="row g-3">
    <div class="col-xl-12">
        <div class="row g-3">
            <form class="col-12 col-md-12 col-xl-12 pt-3" method="post" action="{{ route('employees.store') }}">
                @csrf
                <div class="card card-one card-product">
                    <div class="card-body p-3">
                        <div class="row px-md-4">
                            <div class="w-25">
                                Name <span class="required">*</span>
                            </div>
                            <div class="w-75">
                                <input type="text" class="form-control w-100" placeholder="Enter Name" name="name"
                                value="{{old('name')}}"
                                required>
                            </div>
                        </div>
                        <hr/>
                        <div class="row px-md-4">
                            <div class="w-25">
                                Department <span class="required">*</span>
                            </div>
                            <div class="w-75">
                                <select id="select2D" class="form-select mobile-w-100" name="role_id[]" multiple required>
                                  @foreach($roles as $role)
                                    <option value="{{$role->id}}">{{$role->name}}</option>
                                  @endforeach
                                </select>
                            </div>
                        </div>
                        <hr/>
                        <div class="row px-md-4">
                            <div class="w-25">
                                Email <span class="required">*</span>
                            </div>
                            <div class="w-75">
                                <input type="email" name="email" class="form-control w-100" placeholder="Enter Email Address"
                                value="{{old('email')}}" required>
                            </div>
                        </div>
                        <hr/>
                        <div class="row px-md-4">
                            <div class="w-25">
                                Phone Number
                            </div>
                            <div class="w-75">
                                <input type="text" class="form-control w-100" name="phone" placeholder="Enter Phone Number"
                                  value="{{old('phone')}}"
                                >
                            </div>
                        </div>
                        <hr/>
                        <div class="row px-md-4">
                            <div class="w-25">
                                Status <span class="required">*</span>
                            </div>
                            <div class="w-75">
                                <select id="select2D" class="form-select mobile-w-100" name="status" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="text-align-center">
                            <button type="submit" class="btn btn-primary active my-5 px-5 text-ali">Add employee </button>
                        </div>
                    </div><!-- card-body -->
                </div><!-- card -->
            </form><!-- col -->


        </div><!-- row -->
    </div><!-- col -->
</div><!-- row -->
@endsection
