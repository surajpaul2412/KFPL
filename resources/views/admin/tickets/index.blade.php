@extends('layouts.dashboard')

@section('breadcrum')
Ticket Management
@endsection

@section('content')

@include('topmessages')

<div class="d-sm-flex align-items-center justify-content-between mb-4">

        <form method="get" action="">
          <div style="display:inline-flex;margin-right:10px;">
            <select class="form-select mx-2" name="sel_status_id" style="width:130px;">
                <option value="">All Status </option>
                <option value="1" {!! $sel_status_id==1?"selected='selected'":"" !!}>Order Initiated</option>
                <option value="2" {!! $sel_status_id==2?"selected='selected'":"" !!}>Order Check</option>
                <option value="3" {!! $sel_status_id==3?"selected='selected'":"" !!}>Fund Remitted</option>
                <option value="4" {!! $sel_status_id==4?"selected='selected'":"" !!}>Share Transfer</option>
                <option value="5" {!! $sel_status_id==5?"selected='selected'":"" !!}>ETF Transfer</option>
                <option value="6" {!! $sel_status_id==6?"selected='selected'":"" !!}>Mail to AMC</option>
                <option value="7" {!! $sel_status_id==7?"selected='selected'":"" !!}>Order Received</option>
                <option value="8" {!! $sel_status_id==8?"selected='selected'":"" !!}>Order Executed</option>
                <option value="9" {!! $sel_status_id==9?"selected='selected'":"" !!}>Deal Slip Received</option>
                <option value="10" {!! $sel_status_id==10?"selected='selected'":"" !!}>ETF Transfer 1+1</option>
                <option value="11" {!! $sel_status_id==11?"selected='selected'":"" !!}>Refund Received</option>
                <option value="12" {!! $sel_status_id==12?"selected='selected'":"" !!}>Redemption Received</option>
                <option value="13" {!! $sel_status_id==13?"selected='selected'":"" !!}>Units Received</option>
                <option value="14" {!! $sel_status_id==14?"selected='selected'":"" !!}>Shares Received</option>
            </select>
            <input type="date" class="form-select" name="sel_from_date" placeholder="From Date" style="margin-right:10px;" value="{{$sel_from_date}}"/>
            <input type="date" class="form-select" name="sel_to_date" placeholder="To Date" value="{{$sel_to_date}}"/>
            <select class="form-select mx-2" name="sel_role_id" style="display:inline;width:auto !important;">
                <option value="">All Departments </option>
                @foreach($roles as $role)
                  <option value="{{$role->id}}" {!! $sel_role_id==$role->id?"selected='selected'":""!!}>{{$role->name}}</option>
                @endforeach
            </select>
            <input type="text" class="form-input form-control" name="sel_query" placeholder="Enter Name, Symbol, ISIN code or Ticket ID" value="{{$sel_query}}"/>
          </div>

          <button type="submit" class="btn btn-primary" title="Search">
            <i class="ri-search-line"></i> Search
          </button>
        </form>


    <div class="d-flex align-items-center gap-2 mt-3 mt-md-0">
        <a type="button" href="{{route('admin.tickets.create')}}" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="ri-bar-chart-2-line fs-18 lh-1"></i><span class="d-none d-sm-inline">Raise Ticket</span>
        </a>
    </div>
</div>

<div class="row justify-content-center g-3">
    <div class="col-xl-12">
        <div class="row g-3">
            <div class="col-12 col-md-12 col-xl-12 pt-3">
                <div class="card card-one card-product text-center">
                    <div class="card-body p-0">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Security Name</th>
                                    <th>Buy/Sell</th>
                                    <th>Payment Mode</th>
                                    <th>Total No. of Units</th>
                                    <th>Ticket Value</th>
                                    <th>Created On</th>
                                    <th>Last Modified</th>
                                    <th>Stage</th>
                                    <th>Employee</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                              @if(count($tickets))
                               @foreach($tickets as $ticket)
                                <tr>
                                    <td>{{$ticket->id}}</td>
                                    <td>{{$ticket->security->name}}</td>
                                    <td>{{$ticket->type == 1 ? "Buy" : "Sell"}}</td>
                                    <td>
                                        @if($ticket->payment_type == 1)
                                            Cash
                                        @elseif($ticket->payment_type == 2)
                                            Basket
                                        @else
                                            Net Settlement
                                        @endif
                                    </td>
                                    <td>{{$ticket->basket_no * $ticket->basket_size}}</td>
                                    <td>
										@if( $ticket->type == 2 || ($ticket->type == 1 && $ticket->payment_type == 2) )
											0.00
										@else
											{{$ticket->total_amt}}
										@endif
									</td>
                                    <td>{{$ticket->created_at->format('d-m-Y')}}</td>
                                    <td>{{$ticket->updated_at->format('d-m-Y')}}</td>
                                    <td>{{$ticket->status_id}}</td>
                                    <td>
									@php 
									if($ticket->user)
									{
										echo $ticket->user->name;
									}
									@endphp
									</td>
                                    <td>
                                        @if($ticket->status_id == 6 || $ticket->status_id == 7)
                                        <a href="{{ route('admin.tickets.show', $ticket->id) }}" title="View">
                                            <i class="ri-pencil-fill"></i>
                                        </a>
                                        @else
                                        <a href="{{url('/admin/tickets/' . $ticket->id . '/edit')}}" title="Edit">
                                            <i class="ri-pencil-fill"></i>
                                        </a>
                                        @endif

                                        @if($ticket->status_id < 6)
                                            <a data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal" 
                                            href="javascript:void(0)" 
                                            onclick="setItem({{$ticket->id}})" title="Delete">
                                                <i class="ri-delete-bin-line"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                               @else
                                <tr style="text-align:center">
                                   <td colspan="12" style="text-align:center">
                                      No Data Found
                                   </td>
                                </tr>
                               @endif
                                <!-- Add more rows as needed -->
                            </tbody>
                        </table>

                        <!-- Pagination links -->
                        <div class="d-flex justify-content-center my-3">
                            {{ $tickets->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- row -->
    </div><!-- col -->
</div><!-- row -->


<!-- Delete form : starts -->
<form id="deleteForm" style="display:none" action="{{route('admin.tickets.destroy', 'XXX')}}" method="post">
  @csrf
  @method('DELETE')
  <input id="deleteitem" name="deleteitem" value="">
  <input name="action" value="delete">
</form>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this item?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
      </div>
    </div>
  </div>
</div>

<script>
var base_url = "@php echo url('/admin/tickets'); @endphp";

var selectedItem = "";

function setItem(id)
{
    selectedItem = id;
}

document.getElementById('confirmDelete').addEventListener('click', function () {
    // Perform delete action (AJAX, redirect, etc.)
    document.getElementById("deleteitem").value = selectedItem;
    var action = document.getElementById("deleteForm").getAttribute('action');
    action = action.replace(/XXX/, selectedItem);
    document.getElementById("deleteForm").setAttribute('action', action)
    document.getElementById("deleteForm").submit();

    // Close the modal after action
    var deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
    deleteModal.hide();
});
</script>

@endsection
