@extends('layouts.dashboard')

@section('breadcrum')
Employee Management
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">

        <!--
        <select class="form-select mx-2">
            <option value="status">Status</option>
        </select>
        -->
        <form>
          <div style="display:inline-block;margin-right:10px;">
            <select class="form-select mx-2" name="role_id">
                <option value="">All Departments </option>
                @foreach($roles as $role)
                  <option value="{{$role->id}}" {!! $role_id==$role->id?"selected='selected'":""!!}>{{$role->name}}</option>
                @endforeach
            </select>
          </div>
          <button type="submit" class="actn-bttn" title="Search">
            <i class="ri-search-line"></i>
          </button>
          <button type="reset" class="actn-bttn" title="Reset Search" onclick="resetsearch()">
            <i class="ri-refresh-line"></i>
          </button>
        </form>


    <div class="d-flex align-items-center gap-2 mt-3 mt-md-0">
        <a type="button" href="{{route('employees.create')}}" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="ri-bar-chart-2-line fs-18 lh-1"></i><span class="d-none d-sm-inline"> Add Employee</span>
        </a>
    </div>
</div>

<div class="row justify-content-center g-3">
    <div class="col-xl-12">
        <div class="row g-3">
            <div class="col-12 col-md-12 col-xl-12 pt-3">
                <div class="card card-one card-product text-center"> <!-- Added text-center class -->
                    <div class="card-body p-0">
                        <!-- table -->
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID #</th>
                                    <th>Name</th>
                                    <th>Phone Number</th>
                                    <th>Department</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                              @if(count($employees))
                               @foreach($employees as $employee)
                                <tr>
                                    <td>{{$employee->id}}</td>
                                    <td>{{$employee->name}}</td>
                                    <!--
                                    <td>
                                        <a type="button" class="badge badge-pill bg-success px-4">Active</a>
                                    </td>
                                    -->
                                    <td>{{$employee->phone??'N/A'}}</td>
                                    <td>
                                     @php
                                     $str = '';
                                     foreach($employee->roles as $role)
                                     {
                                       $str .= $role->name . ", ";
                                     }
                                     $str = rtrim(trim($str), ",");
                                     echo $str;
                                     @endphp
                                    </td>
                                    <td>
                                      <a href="{{url('/admin/employees/edit/' . $employee->id)}}" title="Edit">
                                        <i class="ri-pencil-line"></i>
                                      </a>
                                      &nbsp;
                                      <a href="javascript:void(0)" title="Delete">
                                        <i class="ri-delete-bin-line"></i>
                                      </a>
                                    </td>
                                </tr>
                                @endforeach
                               @else
                                <tr style="text-align:center">
                                   <td colspan="5" style="text-align:center">
                                      No Data Found
                                   </td>
                                </tr>
                               @endif
                                <!-- Add more rows as needed -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div><!-- row -->
    </div><!-- col -->
</div><!-- row -->

<script>
	    var base_url = "@php echo url('/admin/employees'); @endphp";
</script>
@endsection
