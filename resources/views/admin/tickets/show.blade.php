@extends('layouts.dashboard')

@section('breadcrum')
Ticket Details
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between">
    <div>
        <ol class="breadcrumb fs-sm mb-3">
            <li class="breadcrumb-item"><a href="/accounts/employees">Ticket Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Ticket</li>
        </ol>
        <h4 class="main-title mb-0">{{$ticket->status->stage}}</h4>
    </div>
</div>

@include('topmessages')

<div class="row g-3">
    <div class="col-xl-12">
        <div class="row g-3">

            <div class="col-12 col-md-12 col-xl-12 pt-3"  method="post" action="{{route('admin.tickets.update', $ticket->id)}}">
                <div class="card card-one card-product">
                    <form class="card-body p-3 py-4">
                        @include('ticket')
                        
                        @if($ticket->status_id == 6)
                        <div class="text-align-center">
                            <a href="{{route('admin.tickets.mail', $ticket)}}" onclick="showWait()" class="btn btnSubmit btn-primary active my-5 px-5 text-ali">Submit </a>
                        </div>
                        @elseif($ticket->status_id == 7)
                        <div class="text-align-center">
                            <a href="{{route('admin.tickets.statusUpdate', $ticket)}}" onclick="showWait()" class="btn btnSubmit btn-primary active my-5 px-5 text-ali">Submit </a>
                        </div>
                        @endif
                    </form>
					<div class='waitmsg' style='display:none;text-align:center;padding-bottom:10px;font-weight:bold;'>Please Wait ... </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
function showWait()
{
  jQuery('.btnSubmit').remove();
  jQuery('.waitmsg').show();
}
</script>
@endsection
