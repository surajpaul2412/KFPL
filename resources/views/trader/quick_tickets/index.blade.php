@extends('layouts.dashboard')

@section('breadcrum')
Quick Ticket Management
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


    <div class="d-flex align-items-center gap-2 mt-3 mt-md-0">
        <a type="button" href="{{route('trader.tickets.create')}}" class="btn btn-primary d-flex align-items-center gap-2">
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
                                    <th>No of Basket</th>
                                    <th>NAV</th>
                                    <th>Created On</th>
                                    <th>Ticket Creator</th>
                                    <th>Convert</th>
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
                                    <td>{{$ticket->basket_no}}</td>
                                    <td>{{$ticket->nav}}</td>
                                    <td>{{$ticket->created_at->format('Y-m-d')}}</td>
                                    <td>{{$ticket->user->name}}</td>
                                    <td><a href="{{ route('trader.quick_tickets.show', $ticket->id) }}"><i class='ri-toggle-line'></i></a></td>
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
    var base_url = "@php echo url('/trader/quick_tickets'); @endphp";
</script>
@endsection
