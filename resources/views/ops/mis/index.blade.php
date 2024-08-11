@extends('layouts.dashboard')

@section('breadcrum')
Ticket Management
@endsection

@section('content')

@include('topmessages')
<style>
    .icon-large {
        font-size: 24px; /* Adjust the size as needed */
    }
</style>
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
                                    <th class="bg-success text-white">Date</th>
                                    <th class="bg-success text-white">AMC Name </th>
                                    <th class="bg-success text-white">Symbol</th>
                                    <th class="bg-success text-white">ISIN</th>
                                    <th class="bg-success text-white">No Of baskets</th>
                                    <th class="bg-success text-white">Qty</th>
                                    <th class="bg-success text-white">Deal Accept</th>
                                    <th class="bg-success text-white">Fund Remitted</th>
                                    <th class="bg-success text-white">Appl Sent</th>
                                    <th class="bg-success text-white">Order Recd</th>
                                    <th class="bg-success text-white">Deal Recd</th>
                                    <th class="bg-success text-white">Amt Recd</th>
                                    <th class="bg-success text-white">Units Recd</th>
                                    <th class="bg-success text-white"></th>
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
            const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
            const date = new Date(dateString);
            return date.toLocaleDateString('en-CA', options); // 'en-CA' gives YYYY-MM-DD format
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
                url: '{{ route("ops.mis.ajax") }}',
                type: 'GET',
                data: { sel_role_id: selectedValue },
                success: function(data) {
                    var table = $('table.table');
                    var thead = table.find('thead');
                    var tbody = table.find('tbody');
                    tbody.empty();

                    // Initialize the total amount
                    var totalBasket = 0;
                    var totalQty = 0;
                    var amountReceived = 0;
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
                                '<th class="bg-success text-white">Date</th>' +
                                '<th class="bg-success text-white">AMC Name </th>' +
                                '<th class="bg-success text-white">Symbol</th>' +
                                '<th class="bg-success text-white">ISIN</th>' +
                                '<th class="bg-success text-white">No Of baskets</th>' +
                                '<th class="bg-success text-white">Qty</th>' +
                                '<th class="bg-success text-white">Deal Accept</th>' +
                                '<th class="bg-success text-white">Fund Remitted</th>' +
                                '<th class="bg-success text-white">Appl Sent</th>' +
                                '<th class="bg-success text-white">Order Recd</th>' +
                                '<th class="bg-success text-white">Deal Recd</th>' +
                                '<th class="bg-success text-white">Amt Recd</th>' +
                                '<th class="bg-success text-white">Units Recd</th>' +
                                '<th class="bg-success text-white">View</th>' +
                            '</tr>'
                        );

                        data.forEach(function(row) {
                            totalBasket += parseFloat(row.basket_no);
                            totalQty += parseFloat(row.basket_no * row.basket_size);
                            
                            var tr = '<tr>' +
                                '<td>' + row.id + '</td>' +
                                '<td>' + formatDate(row.created_at) + '. </td>' +  // Assuming id is the Ticket ID
                                '<td>' + row.security.amc.name + '</td>' +  // Placeholder for AMC Name, update with correct key
                                '<td>' + row.security.symbol + '</td>' +  // Placeholder for Symbol, update with correct key
                                '<td>' + row.security.isin + '</td>' +  // Placeholder for Symbol, update with correct key
                                '<td>' + row.basket_no + '</td>' +  // Placeholder for Symbol, update with correct key
                                '<td>' + row.basket_no * row.basket_size + '</td>' +  // Placeholder for Symbol, update with correct key
                                '<td>' + (row.status_id > 2 ? ''+check+'' : ''+cross+'') + '</td>' + 
                                '<td>' + (row.utr_no ? check : cross) + '</td>' + 
                                '<td>' + (row.status_id > 6 ? ''+check+'' : ''+cross+'') + '</td>' + 
                                '<td>' + (row.status_id > 7 ? ''+check+'' : ''+cross+'') + '</td>' + 
                                '<td>' + (row.status_id > 9 ? ''+check+'' : ''+cross+'') + '</td>' + 
                                '<td>' + (row.status_id > 11 ? ''+check+'' : ''+cross+'') + '</td>' + 
                                '<td>' + (row.status_id > 13 ? ''+check+'' : ''+cross+'') + '</td>' + 
                                '<td><a class="text-info" href="/ops/tickets?sel_query='+ row.security.isin +'&type=1"><i class="ri-eye-fill"></i></a></td>' +
                                '</tr>';
                            tbody.append(tr);
                        });

                        // Append the total row
                        var totalRow = '<tr style="background: grey; color: white;">' +
                            '<td colspan="3">Sub Total</td>' +
                            '<td></td>' +
                            '<td></td>' +
                            '<td>' + totalBasket.toFixed(2) + '</td>' +
                            '<td>' + totalQty.toFixed(2) + '</td>' +
                            '<td></td>' +
                            '<td></td>' +
                            '<td></td>' +
                            '<td></td>' +
                            '<td></td>' +
                            '<td></td>' +
                            '<td></td>' +
                            '<td></td>' +
                            '</tr>';
                        tbody.append(totalRow);

                    } else if (selectedValue == 2) { // SELL case
                        thead.html(
                            '<tr>' +
                                '<th class="bg-danger text-white">Ticket ID</th>' +
                                '<th class="bg-danger text-white">Date</th>' +
                                '<th class="bg-danger text-white">AMC Name </th>' +
                                '<th class="bg-danger text-white">Symbol</th>' +
                                '<th class="bg-danger text-white">ISIN</th>' +
                                '<th class="bg-danger text-white">No Of baskets</th>' +
                                '<th class="bg-danger text-white">Qty</th>' +
                                '<th class="bg-danger text-white">Units Sent</th>' +
                                '<th class="bg-danger text-white">Appl Sent</th>' +
                                '<th class="bg-danger text-white">Order Recd</th>' +
                                '<th class="bg-danger text-white">Deal Recd</th>' +
                                '<th class="bg-danger text-white">Unit Trf</th>' +
                                '<th class="bg-danger text-white">Amt Recd</th>' +
                                '<th class="bg-danger text-white">View</th>' +
                            '</tr>'
                        );

                        data.forEach(function(row) {
                            totalBasket += parseFloat(row.basket_no);
                            totalQty += parseFloat(row.basket_no * row.basket_size);
                            var tr = '<tr>' +
                                    '<td>' + row.id + '. </td>' +  // Assuming id is the Ticket ID
                                    '<td>' + formatDate(row.created_at) + '</td>' +  // Assuming created_at is the Deal Date
                                    '<td>' + row.security.amc.name + '</td>' +  // Placeholder for AMC Name, update with correct key
                                    '<td>' + row.security.symbol + '</td>' +  // Placeholder for Symbol, update with correct key
                                    '<td>' + row.security.isin + '</td>' +  // Placeholder for isin, update with correct key
                                    '<td>' + row.basket_no + '</td>' +  // Assuming total_amt is the Ticket Amount
                                    '<td>' + row.basket_no * row.basket_size + '</td>' +  // Assuming actual_total_amt is the Amount Received
                                    '<td>' + '' + '</td>' +  // Placeholder for Symbol, update with correct key
                                    '<td>' + (row.status_id > 6 ? ''+check+'' : ''+cross+'') + '</td>' + 
                                    '<td>' + (row.status_id > 7 ? ''+check+'' : ''+cross+'') + '</td>' + 
                                    '<td>' + (row.status_id > 9 ? ''+check+'' : ''+cross+'') + '</td>' +                                     
                                    '<td>' + (row.status_id > 13 ? ''+check+'' : ''+cross+'') + '</td>' + 
                                    '<td>' + (row.status_id > 12 ? ''+check+'' : ''+cross+'') + '</td>' + 
                                    '<td><a class="text-info" href="/ops/tickets?sel_query='+ row.security.isin +'&type=2"><i class="ri-eye-fill"></i></a></td>' +
                                '</tr>';
                            tbody.append(tr);
                        });

                        // Append the total row
                        var totalRow = '<tr style="background: grey; color: white;">' +
                                '<td colspan="3">Sub Total</td>' +
                                '<td></td>' +
                                '<td></td>' +
                                '<td>' + totalBasket.toFixed(2) + '</td>' +
                                '<td>' + totalQty.toFixed(2) + '</td>' +
                                '<td></td>' +
                                '<td></td>' +
                                '<td></td>' +
                                '<td></td>' +
                                '<td></td>' +
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
