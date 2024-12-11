@extends('layouts.dashboard')

@section('breadcrum')
Ticket Management
@endsection

@section('content')

@include('topmessages')
<div class="row justify-content-center g-3">
    <form method="get" action="">
        <div style="display:inline-flex;margin-right:10px;">
            <select class="form-select mx-2" name="sel_role_id" style="display:inline;width:auto !important;">
                <option value="1" selected>BUY </option>
                <option value="2">SELL </option>
            </select>
        </div>
    </form>

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
                                    <th class="bg-success text-white">Deal Accepted</th>
                                    <th class="bg-success text-white">Value</th>
                                    <th class="bg-success text-white">NAV</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dynamic content will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div><!-- row -->
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        function formatDate(dateString) {
            // Convert the date string to a JavaScript Date object
            const date = new Date(dateString);

            // Format the date in a fixed timezone (e.g., UTC)
            const options = { year: 'numeric', month: '2-digit', day: '2-digit', timeZone: 'UTC' };

            // Use Intl.DateTimeFormat to format the date correctly
            return new Intl.DateTimeFormat('en-US', options).format(date);
        }

        function getCurrentDate() {
            const date = new Date();
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function loadData(selectedValue) {
            $.ajax({
                url: '{{ route("dealer.mis.ajax") }}',
                type: 'GET',
                data: { sel_role_id: selectedValue },
                success: function(data) {
                    var table = $('table.table');
                    var thead = table.find('thead');
                    var tbody = table.find('tbody');
                    tbody.empty();

                    // Initialize the total amount
                    var totalAmount = 0;
                    var check = '<i class="ri-check-fill text-success icon-large"></i>';
                    var cross = '<i class="ri-close-fill text-danger icon-large"></i>';

                    // Get current date
                    const currentDate = getCurrentDate();
                    const selFromDate = `${currentDate}T00:00:00`;
                    const selToDate = `${currentDate}T23:59:59`;

                    // Change table headers based on the selected value
                    if (selectedValue == 1) { // BUY case
                        thead.html(
                            '<tr>' +
                                '<th class="bg-success text-white">Ticket ID</th>' +
                                '<th class="bg-success text-white">AMC Name</th>' +
                                '<th class="bg-success text-white">Symbol</th>' +
                                '<th class="bg-success text-white">Deal Accepted</th>' +
                                '<th class="bg-success text-white">Value</th>' +
                                '<th class="bg-success text-white">NAV</th>' +
                                '<th class="bg-success text-white">View</th>' +
                            '</tr>'
                        );

                        data.forEach(function(row) {
                            totalAmount += parseFloat(row.actual_total_amt);

                            var tr = '<tr>' +
                                '<td>' + row.id + '. </td>' +  // Assuming id is the Ticket ID
                                '<td>' + row.security.amc.name + '</td>' +  // Placeholder for AMC Name, update with correct key
                                '<td>' + row.security.symbol + '</td>' +  // Placeholder for Symbol, update with correct key
                                '<td>' + (row.status_id > 7 ? ''+check+'' : ''+cross+'') + '</td>' + 
                                '<td>' + (row.actual_total_amt || 'N/A') + '</td>' +  // Assuming utr_no is the UTR Number
                                '<td>' + (row.nav || 'N/A') + '</td>' +  // Assuming refund is the Refund Received
                                '<td><a class="text-info" href="/dealer/tickets?sel_from_date=' + selFromDate + '&sel_to_date=' + selToDate + '&sel_query='+ row.security.isin +'&type=1"><i class="ri-eye-fill"></i></a></td>' +
                                '</tr>';
                            tbody.append(tr);
                        });

                        // Append the total row
                        var totalRow = '<tr style="background: grey; color: white;">' +
                            '<td colspan="3">Total</td>' +
                            '<td></td>' +
                            '<td>' + totalAmount.toFixed(2) + '</td>' +
                            '<td></td>' +
                            '<td></td>' +
                            '</tr>';
                        tbody.append(totalRow);

                    } else if (selectedValue == 2) { // SELL case
                        thead.html(
                            '<tr>' +
                                '<th class="bg-danger text-white">Ticket ID</th>' +
                                '<th class="bg-danger text-white">AMC Name</th>' +
                                '<th class="bg-danger text-white">Symbol</th>' +
                                '<th class="bg-danger text-white">Deal Accepted</th>' +
                                '<th class="bg-danger text-white">Value</th>' +
                                '<th class="bg-danger text-white">NAV</th>' +
                                '<th class="bg-danger text-white">View</th>' +
                            '</tr>'
                        );

                        data.forEach(function(row) {
                            totalAmount += parseFloat(row.actual_total_amt);

                            var tr = '<tr>' +
                                '<td>' + row.id + '. </td>' +  // Assuming id is the Ticket ID
                                '<td>' + row.security.amc.name + '</td>' +  // Placeholder for AMC Name, update with correct key
                                '<td>' + row.security.symbol + '</td>' +  // Placeholder for Symbol, update with correct key
                                '<td>' + (row.status_id > 7 ? ''+check+'' : ''+cross+'') + '</td>' + 
                                '<td>' + row.actual_total_amt + '</td>' +  // Assuming total_amt is the Ticket Amount
                                '<td>' + row.nav + '</td>' +  // Assuming actual_total_amt is the Amount Received
                                '<td><a class="text-info" href="/dealer/tickets?sel_from_date=' + selFromDate + '&sel_to_date=' + selToDate + '&sel_query='+ row.security.isin +'&type=2"><i class="ri-eye-fill"></i></a></td>' +
                                '</tr>';
                            tbody.append(tr);
                        });

                        // Append the total row
                        var totalRow = '<tr style="background: grey; color: white;">' +
                            '<td colspan="4">Total</td>' +
                            '<td>' + totalAmount.toFixed(2) + '</td>' +
                            '<td></td>' +
                            '<td></td>' +
                            '</tr>';
                        tbody.append(totalRow);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        // Trigger change event on page load
        var defaultValue = $('select[name="sel_role_id"]').val();
        loadData(defaultValue);

        $('select[name="sel_role_id"]').change(function() {
            var selectedValue = $(this).val();
            loadData(selectedValue);
        });
    });
</script>
@endsection
