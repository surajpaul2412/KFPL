@extends('layouts.dashboard')

@section('breadcrum')
Employee Management
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div class="d-flex">
        <select class="form-select mx-2">
            <option value="status">Status</option>
        </select>
        <select class="form-select mx-2">
            <option value="department">Department</option>
        </select>
    </div>

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
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Phone Number</th>
                                    <th>Department</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Data 1</td>
                                    <td>Data 2</td>
                                    <td>
                                        <a type="button" class="badge badge-pill bg-success px-4">Active</a>
                                    </td>
                                    <td>Data 4</td>
                                    <td>Data 5</td>
                                    <td>Data 6</td>
                                </tr>
                                <tr>
                                    <td>Data 1</td>
                                    <td>Data 2</td>
                                    <td>
                                        <a type="button" class="badge badge-pill bg-success px-4">Active</a>
                                    </td>
                                    <td>Data 4</td>
                                    <td>Data 5</td>
                                    <td>Data 6</td>
                                </tr>
                                <tr>
                                    <td>Data 1</td>
                                    <td>Data 2</td>
                                    <td>
                                        <a type="button" class="badge badge-pill bg-success px-4">Active</a>
                                    </td>
                                    <td>Data 4</td>
                                    <td>Data 5</td>
                                    <td>Data 6</td>
                                </tr>
                                <tr>
                                    <td>Data 1</td>
                                    <td>Data 2</td>
                                    <td>
                                        <a type="button" class="badge badge-pill bg-success px-4">Active</a>
                                    </td>
                                    <td>Data 4</td>
                                    <td>Data 5</td>
                                    <td>Data 6</td>
                                </tr>
                                <tr>
                                    <td>Data 1</td>
                                    <td>Data 2</td>
                                    <td>
                                        <a type="button" class="badge badge-pill bg-success px-4">Active</a>
                                    </td>
                                    <td>Data 4</td>
                                    <td>Data 5</td>
                                    <td>Data 6</td>
                                </tr>
                                <tr>
                                    <td>Data 1</td>
                                    <td>Data 2</td>
                                    <td>
                                        <a type="button" class="badge badge-pill bg-success px-4">Active</a>
                                    </td>
                                    <td>Data 4</td>
                                    <td>Data 5</td>
                                    <td>Data 6</td>
                                </tr>
                                <tr>
                                    <td>Data 1</td>
                                    <td>Data 2</td>
                                    <td>
                                        <a type="button" class="badge badge-pill bg-success px-4">Active</a>
                                    </td>
                                    <td>Data 4</td>
                                    <td>Data 5</td>
                                    <td>Data 6</td>
                                </tr>
                                <tr>
                                    <td>Data 1</td>
                                    <td>Data 2</td>
                                    <td>
                                        <a type="button" class="badge badge-pill bg-success px-4">Active</a>
                                    </td>
                                    <td>Data 4</td>
                                    <td>Data 5</td>
                                    <td>Data 6</td>
                                </tr>
                                <tr>
                                    <td>Data 1</td>
                                    <td>Data 2</td>
                                    <td>
                                        <a type="button" class="badge badge-pill bg-success px-4">Active</a>
                                    </td>
                                    <td>Data 4</td>
                                    <td>Data 5</td>
                                    <td>Data 6</td>
                                </tr>
                                <tr>
                                    <td>Data 1</td>
                                    <td>Data 2</td>
                                    <td>
                                        <a type="button" class="badge badge-pill bg-success px-4">Active</a>
                                    </td>
                                    <td>Data 4</td>
                                    <td>Data 5</td>
                                    <td>Data 6</td>
                                </tr>
                                <tr>
                                    <td>Data 1</td>
                                    <td>Data 2</td>
                                    <td>
                                        <a type="button" class="badge badge-pill bg-success px-4">Active</a>
                                    </td>
                                    <td>Data 4</td>
                                    <td>Data 5</td>
                                    <td>Data 6</td>
                                </tr>
                                <tr>
                                    <td>Data 1</td>
                                    <td>Data 2</td>
                                    <td>
                                        <a type="button" class="badge badge-pill bg-success px-4">Active</a>
                                    </td>
                                    <td>Data 4</td>
                                    <td>Data 5</td>
                                    <td>Data 6</td>
                                </tr>
                                <!-- Add more rows as needed -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div><!-- row -->
    </div><!-- col -->
</div><!-- row -->
@endsection
