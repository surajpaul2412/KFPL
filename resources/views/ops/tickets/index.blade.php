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
                                    <th>ID #</th>
                                    <th>Security</th>
                                    <th>Buy/Sell</th>
                                    <th>Payment Mode</th>
                                    <th>Total Shares</th>
                                    <th>Trade Value</th>
                                    <th>Created On</th>
                                    <th>Trade Value</th>
                                    <th>Last Modified</th>
                                    <th>Curr. Stage</th>
                                    <th>Curr. Employee</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                              @if(count($tickets))
                               @foreach($tickets as $ticket)
                                <tr>
                                    <td>{{$ticket->id}}</td>
                                    <td>{{$ticket->security->name}}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                    	verify 
                                    	reject
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
