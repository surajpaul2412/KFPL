@extends('layouts.dashboard')

@section('breadcrum')
Ticket Management
@endsection

@section('content')

@include('topmessages')

<div class="d-sm-flex align-items-center justify-content-between mb-4">

        <form method="get" action"">
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
          </div>
          <input type="text" class="form-input" name="sel_query" placeholder="Enter Name, Symbol, ISIN code or Ticket ID" value="{{$sel_query}}"/>
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
                                    <th>Trade Value</th>
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
                                    <td>{{$ticket->total_amt}}</td>
                                    <td>{{$ticket->created_at->format('Y-m-d')}}</td>
                                    <td>{{$ticket->updated_at->format('Y-m-d')}}</td>
                                    <td>{{$ticket->status_id}}</td>
                                    <td>{{$ticket->user->name}}</td>
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
                            {{ $tickets->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- row -->
    </div><!-- col -->
</div><!-- row -->
<!-- toggle status form : starts -->
<form id="toggleStatusForm" style="display:none" action="{{route('admin.employee.togglestatus')}}">
  <input name="item" value="">
  <input name="action" value="togglestatus">
</form>

<script>
	    var base_url = "@php echo url('/admin/employees'); @endphp";
</script>
@endsection
