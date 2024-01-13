@extends('layouts.dashboard')

@section('breadcrum')
Ticket Management
@endsection

@section('content')

@include('topmessages')

<div class="d-sm-flex align-items-center justify-content-between mb-4">

        <form>
          <div style="display:inline-block;margin-right:10px;">
            <select class="form-select mx-2" name="role_id">
                <option value="">All Stages </option>
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
        <a type="button" href="{{route('trader.tickets.create')}}" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="ri-bar-chart-2-line fs-18 lh-1"></i><span class="d-none d-sm-inline">Raise Ticket</span>
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
                                    <th>S. no</th>
                                    <th>Security Name</th>
                                    <th>Buy/Sell</th>
                                    <th>Payment Mode</th>
                                    <th>Total No. of Units</th>
                                    <th>Trade Value</th>
                                    <th>Created On</th>
                                    <th>Last Modified</th>
                                    <th>Stage</th>
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
                                    <td>
                                        @if($ticket->status_id == 1)
                                        <a href="{{url('/trader/tickets/' . $ticket->id . '/edit')}}" title="Edit">
                                            <i class="ri-pencil-line"></i>
                                        </a>
                                        @else
                                        <a href="{{ route('trader.tickets.show', $ticket->id) }}" title="View">
                                            <i class="ri-pencil-line"></i>
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
@endsection
