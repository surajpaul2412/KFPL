@extends('layouts.dashboard')

@section('breadcrum')
Ticket Management
@endsection

@section('content')

@include('topmessages')

<div class="d-sm-flex align-items-center justify-content-between mb-4">

		<form method="get" action="">
          <div style="display:inline-flex;margin-right:10px;">

            <input type="date" class="form-select" name="sel_from_date" placeholder="From Date" style="margin-right:10px;" value="{{$sel_from_date}}"/>
            <input type="date" class="form-select" name="sel_to_date" placeholder="To Date" value="{{$sel_to_date}}"/>
            <input type="text" class="form-input form-control" name="sel_query" placeholder="Enter Name, Symbol, ISIN code or Ticket ID"
			value="{{$sel_query}}" style="margin-left:10px;"/>
          </div>

          <button type="submit" class="btn btn-primary" title="Search">
            <i class="ri-search-line"></i> Search
          </button>

		  <button type="reset" class="btn btn-primary actn-bttn" title="Reset Search" onclick="resetsearch()">
			<i class="ri-refresh-line"></i>
		  </button>

        </form>
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
                                    <td>{{$ticket->user->name}}</td>
                                    <td>
                                        @if( $ticket->status_id == 2 || $ticket->status_id == 5 || $ticket->status_id == 9 || 
										$ticket->status_id == 10 || $ticket->status_id == 13 || 
										  ( $ticket->type == 1 && $ticket->payment_type == 2 && $ticket->status_id == 14) || 
										  ( $ticket->type == 1 && $ticket->payment_type == 1 && $ticket->status_id == 14) 
										)
                                        <a href="{{url('/ops/tickets/' . $ticket->id . '/edit')}}" title="Edit">
                                            <i class="ri-pencil-fill"></i>
                                        </a>
                                        @elseif( $ticket->status_id == 6 )
                                        <a href="{{ route('ops.tickets.show', $ticket->id) }}" title="View">
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
                            {{ $tickets->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- row -->
    </div><!-- col -->
</div><!-- row -->
<script>
	var base_url = "@php echo url('/ops/tickets'); @endphp";
</script>
@endsection
