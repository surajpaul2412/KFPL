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

   @if(session()->get('success'))
		<div class="alert alert-success">
			{{ session()->get('success') }}
		</div>
   @endif

   @if ($message = Session::get('error'))
   <div class="alert alert-danger alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
   </div>
   @endif

   @if (count($errors) > 0)
    <div class="alert alert-danger">
     <ul>
     @foreach($errors->all() as $error)
      <li>{{ $error }}</li>
     @endforeach
     </ul>
    </div>
   @endif

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
                                <input type="text" class="form-control w-100" placeholder="Enter Name" name="name" required>
                            </div>
                        </div>
                        <hr/>
                        <div class="row px-md-4">
                            <div class="w-25">
                                Department <span class="required">*</span>
                            </div>
                            <div class="w-75">
                                <select id="select2D" class="form-select mobile-w-100" name="role_id[]" multiple required style="height:100px">
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
                                <input type="email" name="email" class="form-control w-100" placeholder="Enter Email Address" required>
                            </div>
                        </div>
                        <hr/>
                        <div class="row px-md-4">
                            <div class="w-25">
                                Phone Number
                            </div>
                            <div class="w-75">
                                <input type="text" class="form-control w-100" name="phone" placeholder="Enter Phone Number">
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
