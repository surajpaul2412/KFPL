@extends('layouts.dashboard')

@section('breadcrum')
    AMC Master
@endsection

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between">
        <form class="d-flex" method="GET" action="{{ route('amcs.index') }}">
            <input class="form-control me-2" type="search" name="search" value="{{$search}}" placeholder="Search AMC" aria-label="Search AMC">
            <button class="btn btn-primary" type="submit">Search</button>
        </form>

        <div class="d-flex align-items-center gap-2 mt-3 mt-md-0">
            <a type="button" href="{{ route('amcs.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
                <i class="ri-add-circle-line fs-18 lh-1"></i><span class="d-none d-sm-inline"> Add AMC</span>
            </a>
        </div>
    </div>

    <div class="row justify-content-center g-3">
        <div class="col-xl-12">
            <div class="row g-3">
                <div class="col-12 col-md-12 col-xl-12 pt-3">
                    <div class="card card-one card-product text-center">
                        <div class="card-body p-0">
                            <!-- Table for displaying AMC records -->
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Total No. of securities</th>
                                        <th>NAV (%)</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($amcs as $amc)
                                        <tr>
                                            <td>{{ $amc->id }}.</td>
                                            <td>{{ $amc->name }}</td>
                                            <td>{{ $amc->securities->count() }}</td>
                                            <td>{{ $amc->nav??0 }} %</td>
                                            <td>
                                                @if($amc->status == 1)
                                                <a type="button" class="badge badge-pill text-white bg-success px-4">Active</a>
                                                @else
                                                <a type="button" class="badge badge-pill text-white bg-warning px-4">Inactive</a>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('amcs.edit', $amc->id) }}" class="">Edit</a>

                                                <form action="{{ route('amcs.destroy', $amc->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="" onclick="return confirm('Are you sure you want to delete?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Pagination links -->
                            <div class="d-flex justify-content-center my-3">
                                {{ $amcs->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
