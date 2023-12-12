@extends('layouts.dashboard')

@section('breadcrum')
Employee Management
@endsection



@section('content')
<div class="d-sm-flex align-items-center justify-content-between">
    <div>
        <ol class="breadcrumb fs-sm mb-3">
            <li class="breadcrumb-item"><a href="/admin/employees">Employee Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Employee</li>
        </ol>
        <h4 class="main-title mb-0">Edit Employee</h4>
    </div>
</div>

@include('topmessages')

<div class="row g-3">
    <div class="col-xl-12">
        <div class="row g-3">

            <form class="col-12 col-md-12 col-xl-12 pt-3" method="post" action="{{route('employees.update', $employee->id)}}">
                @csrf
                @method('put')
                <div class="card card-one card-product">
                    <div class="card-body p-3">
                        <div class="row px-md-4">
                            <div class="w-25">
                                Name <span class="required">*</span>
                            </div>
                            <div class="w-75">
                                <input type="text" class="form-control w-100" placeholder="Enter Name" name="name"
                                value="@if(old('name')!=''){{old('name')}}@else{{$employee->name}}@endif"
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

                                @php
                                foreach($roles as $role)
                                {
                                    echo "<option value='" . $role->id . "' ";
                                    foreach($employee->roles as $erole)
                                    {
                                      echo $erole->id == $role->id ? " selected='selected'" : "" ;
                                    }
                                    echo  ">" . $role->name . "</option>";
                                }
                                @endphp
                              </select>
                            </div>
                        </div>
                        <hr/>
                        <div class="row px-md-4">
                            <div class="w-25">
                                Email <span class="required">*</span>
                            </div>
                            <div class="w-75">
                                <input type="email" class="form-control w-100" placeholder="Enter Email Address" name="email"
                                  value="@if(old('email')!=''){{old('email')}}@else{{$employee->email}}@endif"
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
                                  value="@if(old('phone')!=''){{old('phone')}}@else{{$employee->phone}}@endif"
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
                                    <option value="1" @if($employee->status){{" selected='selected'"}}@endif>Active</option>
                                    <option value="0" @if(!$employee->status){{" selected='selected'"}}@endif>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="text-align-center">
                            <button type="submit" class="btn btn-primary active my-5 px-5 text-ali">Save Employee </button>
                        </div>
                    </div><!-- card-body -->
                </div><!-- card -->
            </form><!-- col -->


        </div><!-- row -->
    </div><!-- col -->
</div><!-- row -->
@endsection
