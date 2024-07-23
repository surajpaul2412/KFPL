@extends('layouts.dashboard')

@section('breadcrum')
Ticket Management
@endsection

@section('content')

@include('topmessages')
<div class="row justify-content-center g-3">
    <div class="col-xl-12">
        <div class="row g-3">
            <div class="col-12 col-md-12 col-xl-12 pt-3">
                <div class="card card-one card-product text-center">
                    <div class="card-body p-0">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="bg-success text-white">Ticket ID</th>
                                    <th class="bg-success text-white">AMC Name</th>
                                    <th class="bg-success text-white">Symbol</th>
                                    <th class="bg-success text-white">Ticket Amount</th>
                                    <th class="bg-success text-white">UTR Number</th>
                                    <th class="bg-success text-white">Refund Received</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1.</td>
                                    <td>test</td>
                                    <td>test</td>
                                    <td>test</td>
                                    <td>test</td>
                                    <td>test</td>
                                </tr>
                                <tr>
                                    <td>1.</td>
                                    <td>test</td>
                                    <td>test</td>
                                    <td>test</td>
                                    <td>test</td>
                                    <td>test</td>
                                </tr>
                                <tr>
                                    <td>1.</td>
                                    <td>test</td>
                                    <td>test</td>
                                    <td>test</td>
                                    <td>test</td>
                                    <td>test</td>
                                </tr>
                                <tr style="background: grey;color: #fff;">
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>100.00</td>
                                    <td>100.00</td>
                                </tr>
                            </tbody>
                        </table>




                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="bg-danger text-white">Deal Date</th>
                                    <th class="bg-danger text-white">Ticket ID</th>
                                    <th class="bg-danger text-white">AMC Name </th>
                                    <th class="bg-danger text-white">Symbol</th>
                                    <th class="bg-danger text-white">Ticket Amount</th>
                                    <th class="bg-danger text-white">Amount Received</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1.</td>
                                    <td>test</td>
                                    <td>test</td>
                                    <td>test</td>
                                    <td>test</td>
                                    <td>test</td>
                                </tr>
                                <tr>
                                    <td>1.</td>
                                    <td>test</td>
                                    <td>test</td>
                                    <td>test</td>
                                    <td>test</td>
                                    <td>test</td>
                                </tr>
                                <tr>
                                    <td>1.</td>
                                    <td>test</td>
                                    <td>test</td>
                                    <td>test</td>
                                    <td>test</td>
                                    <td>test</td>
                                </tr>
                                <tr style="background: grey;color: #fff;">
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>100.00</td>
                                    <td>100.00</td>
                                </tr>
                            </tbody>
                        </table>

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
