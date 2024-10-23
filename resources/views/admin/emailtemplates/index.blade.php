@extends('layouts.dashboard')

@section('breadcrum')
    Email Templates
@endsection

@section('breadcrum-btn')

@endsection

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between">
        <form class="d-flex" method="GET" action="{{ route('admin.emailtemplates.index') }}">
            <input class="form-control me-2" type="search" name="search" value="{{$search}}" placeholder="Search AMC" aria-label="Search AMC">
            <button class="btn btn-primary" type="submit">Search</button>
        </form>

        <div class="d-flex align-items-center gap-2 mt-3 mt-md-0">
            <a type="button" href="{{ route('admin.emailtemplates.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
                <i class="ri-add-circle-line fs-18 lh-1"></i><span class="d-none d-sm-inline"> Add Template</span>
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
                                        <th>ID#</th>
                                        <th>Template Name</th>
                                        <th>Subject</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($emailtemplates as $emailtemplate)
                                        <tr>
                                            <td>{{ $emailtemplate->id }}</td>
                                            <td>{{ $emailtemplate->name }}</td>
											<td>{{ $emailtemplate->subject }}</td>
                                            <td>
                                                @if ($emailtemplate->status == 1)
                                                    <span class="badge badge-pill bg-success">Active</span>
                                                @else
                                                    <span class="badge badge-pill bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.emailtemplates.edit', $emailtemplate->id) }}" title="Edit">
                                                    <i class="ri-pencil-fill"></i>
                                                </a>
                                                <form id='del{{$emailtemplate->id}}'action="{{ route('admin.emailtemplates.destroy', $emailtemplate->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a type="submit" class="" href="javascript:void(0)" onclick="submitDelForm('del{{$emailtemplate->id}}')">
                                                        <i class="ri-delete-bin-5-fill text-danger"></i>
                                                    </a>
                                                </form>
                                            </td>
                                        </tr>
									@empty 
										<tr>
										  <td colspan="7" style="text-align:center">No Email Templates Found</td>
										</tr>
                                    @endforelse
                                </tbody>
                            </table>
							<script>
							function submitDelForm(id)
							{
								var t = confirm('Are you sure you want to delete?');
								if(t)
								{
									jQuery("#" + id).submit();
								}
							}
							</script>
                            <!-- Pagination links -->
                            @if($emailtemplates->hasPages())
                                <div class="d-flex justify-content-center my-3">
                                    {{ $emailtemplates->withQueryString()->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">

    <form action="{{ url('/admin/upload-securities') }}" method="post" enctype="multipart/form-data" class="modal-content">
        @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Upload Excel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="file" name="csv_file" accept=".csv" required>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary text-white" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Upload CSV</button>
      </div>
    </form>

  </div>
</div>
@endsection
