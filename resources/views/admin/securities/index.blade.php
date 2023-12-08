@extends('layouts.dashboard')

@section('breadcrum')
    AMC Securities
@endsection

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <form class="d-flex" method="GET" action="{{ route('securities.index') }}">
            <input class="form-control me-2" type="search" name="search" value="{{$search}}" placeholder="Search AMC" aria-label="Search AMC">
            <button class="btn btn-primary" type="submit">Search</button>
        </form>

        <div class="d-flex align-items-center gap-2 mt-3 mt-md-0">
            <a type="button" href="{{ route('securities.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
                <i class="ri-add-circle-line fs-18 lh-1"></i><span class="d-none d-sm-inline"> Add Security</span>
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
                                        <th>Symbol</th>
                                        <th>ISIN</th>
                                        <th>Basket Size</th>
                                        <th>Markup %</th>
                                        <th>Price</th>
                                        <th>Last Updated at </th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($securities as $security)
                                        <tr>
                                            <td>{{ $security->id }}</td>
                                            <td>{{ $security->name }}</td>
                                            <td>{{ $security->symbol }}</td>
                                            <td>{{ $security->isin }}</td>
                                            <td>{{ number_format($security->basket_size, 0, ',', ',') }}</td>
                                            <td>{{ $security->markup_percentage }} %</td>
                                            <td>{{ $security->price }}</td>
                                            <td>{{ $security->updated_at }}</td>
                                            <td>
                                                @if ($security->status == 1)
                                                    <span class="badge badge-pill bg-success">Active</span>
                                                @else
                                                    <span class="badge badge-pill bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('securities.edit', $security->id) }}" class="">Edit</a>

                                                <form action="{{ route('securities.destroy', $security->id) }}" method="POST" class="d-inline">
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
                                {{ $securities->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
