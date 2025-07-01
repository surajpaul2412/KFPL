@extends('layouts.dashboard')

@section('breadcrum')
    Email Senders Master
@endsection

@section('content')
    <style>
	.alert{
		margin-bottom: -10px;
		margin-top: 10px;
	}
	</style>
    <div class="d-sm-flex align-items-center justify-content-between">
        <form class="d-flex" method="GET" action="{{ route('senderemail.index') }}">
            <input class="form-control me-2" type="search" name="search" value="{{$search}}" placeholder="Search Email Senders" aria-label="Search Email Senders">
            <button class="btn btn-primary" type="submit">Search</button>
        </form>

        <div class="d-flex align-items-center gap-2 mt-3 mt-md-0">
            <a type="button" href="{{ route('senderemail.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
                <i class="ri-add-circle-line fs-18 lh-1"></i><span class="d-none d-sm-inline"> Add Email Sender</span>
            </a>
        </div>
    </div>

    @include('topmessages')

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
                                        <th>Driver</th>
                                        <th>From Email</th>
                                        <th>From Name</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($senderemails as $emailsender)
                                        <tr>
                                            <td>{{ $emailsender->id }}.</td>
                                            <td>{{ $emailsender->driver }}</td>
                                            <td>{{ $emailsender->from_address }}</td>
                                            <td>{{ $emailsender->from_name }}</td>
                                            <td>
                                                @if($emailsender->status == 1)
                                                <a type="button" class="badge badge-pill text-white bg-success px-4">Active</a>
                                                @else
                                                <a type="button" class="badge badge-pill text-white bg-warning px-4">Inactive</a>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('senderemail.edit', $emailsender->id) }}" title="Edit">
                                                    <i class="ri-pencil-fill"></i>
                                                </a>
                                                <form id='deleteForm{{$emailsender->id}}' action="{{ route('senderemail.destroy', $emailsender->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a type="submit" class="" title="DELETE Email Sender"
                                                        onclick="deletePrompt({{$emailsender->id}}, {{$emailsender->status}})"> 
													     <i class="ri-delete-bin-line" title="Delete"></i>
                                                    </a>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Pagination links -->
                            <div class="d-flex justify-content-center my-3">
                                {{ $senderemails->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function deletePrompt(formid, mode)
        {
            var k = confirm('Are you sure you want to DELETE?');
            if(k)
            {
                document.getElementById('deleteForm' + formid).submit();
            }
        }
    </script>
@endsection
