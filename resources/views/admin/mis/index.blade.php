@extends('layouts.dashboard')

@section('breadcrum')
Ticket Management
@endsection

@section('breadcrum-btn')
<a href="javascript:void(0)" class="btn btn-outline-primary csvExportButton" title="CSV Export" onclick="csvExport()">
    <i class="ri-download-2-line pe-2"></i>Download as CSV
</a>
@endsection


@section('content')


@include('topmessages')

<div class="d-sm-flex align-items-center justify-content-between">
    <form method="get" action="">
      <div style="display:inline-flex;margin-right:10px;">
        
        <select class="form-select mx-2" name="sel_role_id" onclick="showCSVBtn()" style="display:inline;width:auto !important;">
            <option value="trader" selected="selected">Trader </option>
            <option value="accounts">Accounts </option>
            <option value="dealer">Dealer </option>
            <option value="ops">Ops </option>
        </select>
        <select class="form-select mx-2" name="mode" style="display:inline;width:auto !important;">
            <option value="1" selected="selected">BUY </option>
            <option value="2">Sell </option>
        </select>

      </div>
	  
      <button type="button" class="btn btn-primary" title="Search" onclick="search()">
        <i class="ri-search-line"></i> Search
      </button>
	  
	  <!---
	  <button type="button" class="btn csvExportButton btn-primary" title="CSV Export" onclick="csvExport()">
        <i class="ri-download-2-fill"></i> CSV Export
      </button>
      -->

    </form>
</div>

<div class="row justify-content-center g-3">
    <div class="col-xl-12">
        <div class="row g-3">
            <div class="col-12 col-md-12 col-xl-12 pt-3">
                <div class="card card-one card-product text-center">
                    <div class="card-body p-0">
                        <!-- TRADER TABLE -->
						<table class="table tradertable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>AMC Name</th>
                                    <th>Symbol</th>
                                    <th>Type</th>
                                    <th>Quick Ticket (No of Basket)</th>
                                    <th>Quick Ticket Value</th>
                                    <th>Baskets Executed</th>
                                    <th>Amount Sent/Received</th>
                                    <th>Total Units</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
								  <td colspan="9" style="text-align:center"> No Data Found </td>
								</tr>
                            </tbody>
                        </table>
						
						<!-- OPS TABLE -->
						<table class="table opstable" style="display:none">
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
						
						<!-- Accounts TABLE -->
						<table class="table accountstable" style="display:none">
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
                                <!-- Dynamic content will be loaded here -->
                            </tbody>
                        </table>
						
						<!-- DEALER TABLE -->
						<table class="table dealertable" style="display:none">
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
    </div><!-- col -->
</div><!-- row -->
<!-- toggle status form : starts -->
<form id="toggleStatusForm" style="display:none" action="{{route('admin.employee.togglestatus')}}">
  <input name="item" value="">
  <input name="action" value="togglestatus">
</form>

<form class="csvExporForm" method="get" target="_blank" action="">
	<input type="hidden" name="usertype" value="">
    <input type="hidden" name="datamode" value="">
</form>

<script>
	    var base_url = "@php echo url('/admin/mis'); @endphp";
</script>
@endsection

@section('script')
<script>

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

function loadData(selectedValue) 
{
    var mode = jQuery("[name='mode']").val();	
	
	console.log("loadData function called with Mode : " + mode + ", usertype : " + selectedValue);
	
	$.ajax({
		url: "{{route('admin.mis.ajax')}}",  
		type: 'GET',
		data: { sel_role_id: mode, usertype : selectedValue }, // Decide, Is it a BUY or SELL data
		success: function(data) {
			
			console.log("AJAX SUCCESS : " + selectedValue);
			
			if( selectedValue == 'dealer')
			{
				$(".table").hide(); 
				$(".dealertable").show();
				
				var table = $('table.dealertable');
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
				if (mode == 1) { // BUY case
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
							'<td><a class="text-info" href="/admin/tickets?sel_from_date=' + selFromDate + '&sel_to_date=' + selToDate + '&sel_query='+ row.security.isin +'&type=1"><i class="ri-eye-fill"></i></a></td>' +
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

				} else if (mode == 2) { // SELL case
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
							'<td><a class="text-info" href="/admin/tickets?sel_from_date=' + selFromDate + '&sel_to_date=' + selToDate + '&sel_query='+ row.security.isin +'&type=2"><i class="ri-eye-fill"></i></a></td>' +
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
			}
			
			if( selectedValue == 'ops')
			{
				$(".table").hide(); 
				$(".opstable").show();
				
				var table = $('table.opstable');
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
				if (mode == 1) { // BUY case
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
						console.log(row.created_at);
						
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
							'<td><a class="text-info" href="/admin/tickets?sel_query='+ row.security.isin +'&type=1"><i class="ri-eye-fill"></i></a></td>' +
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

				} else if (mode == 2) { // SELL case
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
								'<td><a class="text-info" href="/admin/tickets?sel_query='+ row.security.isin +'&type=2"><i class="ri-eye-fill"></i></a></td>' +
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

			}
			
			if( selectedValue == 'trader' )
			{
				
				$(".table").hide(); 
				$(".tradertable").show();
				
				var table = $('table.tradertable');
				var thead = table.find('thead');
				var tbody = table.find('tbody');
				tbody.empty();

				// Initialize the total amount
				var totalQuickTicket = 0;
				var totalQuickTicketVal = 0;
				var totalQuickTicketUnits = 0;
				var amountReceived = 0;
				var totalTicket = 0;
				var amtSent = 0;

				// Get current date
				const currentDate = getCurrentDate();
				const selFromDate = `${currentDate}T00:00:00`;
				const selToDate = `${currentDate}T23:59:59`;

				// Change table headers based on the selected value
				if (mode == 1) { // BUY case
					thead.html(
						'<tr>' +
							'<th class="bg-success text-white">AMC Name</th>' +
							'<th class="bg-success text-white">Symbol</th>' +
							'<th class="bg-success text-white">Quick Ticket </th>' +
							'<th class="bg-warning text-white">View QT </th>' +
							'<th class="bg-success text-white">NAV</th>' +
							'<th class="bg-success text-white">Quick Ticket Value</th>' +
							'<th class="bg-success text-white">Ticket Raised</th>' +
							'<th class="bg-warning text-white">Pending Tickets</th>' +
							'<th class="bg-success text-white">Amount Sent</th>' +
							'<th class="bg-success text-white">Total Units</th>' +
							'<th class="bg-warning text-white">View </th>' +
						'</tr>'
					);

					data.forEach(function(row) {
						totalQuickTicket += parseFloat(row.total_quick_basket_no || 0);
						totalQuickTicketVal += parseFloat(row.total_quick_amt || 0);
						totalQuickTicketUnits += parseFloat(row.total_quick_units + row.total_ticket_units || 0);
						totalTicket += parseFloat(row.total_ticket_basket_no || 0);
						amtSent += parseFloat(row.total_ticket_actual_amt || 0);

						var tr = '<tr>' +
							'<td>' + (row.security ? row.security.amc.name : '-') + '</td>' +  // AMC Name
							'<td>' + (row.security ? row.security.symbol : '-') + '</td>' +  // Symbol
							'<td>' + (row.total_quick_basket_no == 0 ? '-' : row.total_quick_basket_no) + '</td>' +  // Total Basket No
							'<td>' + (row.total_quick_basket_no == 0 
								? '-' 
								: '<a class="text-info" href="/admin/quick_tickets?sel_from_date=' + selFromDate + '&sel_to_date=' + selToDate + '&sel_query='+ row.security.isin +'&type=1"><i class="ri-eye-fill"></i></a>'
							) + '</td>' +
							'<td>' + (row.total_quick_nav == 0 ? '-' : (row.total_quick_nav / row.total_quick_clubbed)) + '</td>' +  // NAV / Clubbed
							'<td>' + (row.total_quick_amt == 0 ? '-' : row.total_quick_amt) + '</td>' +
							'<td>' + (row.total_ticket_basket_no == 0 ? '-' : row.total_ticket_basket_no) + '</td>' +
							'<td>' + (row.total_quick_basket_no - row.total_ticket_basket_no) + '</td>' +
							'<td>' + (row.total_ticket_actual_amt == 0 ? '-' : row.total_ticket_actual_amt) + '</td>' +
							'<td>' + (row.total_quick_units + row.total_ticket_units) + '</td>' +
							'<td>' + (row.total_ticket_basket_no == 0 
								? '-' 
								: '<a class="text-info" href="/admin/tickets?sel_from_date=' + selFromDate + '&sel_to_date=' + selToDate + '&sel_query='+ row.security.isin +'&type=1"><i class="ri-eye-fill"></i></a>'
							) + '</td>' +
							'</tr>';
						tbody.append(tr);
					});

					// Append the total row
					var totalRow = '<tr style="background: grey; color: white;">' +
						'<td colspan="2">Total</td>' +
						'<td>' + totalQuickTicket + '</td>' +
						'<td></td>' +
						'<td></td>' +
						'<td>' + totalQuickTicketVal.toFixed(2) + '</td>' +
						'<td>' + totalTicket + '</td>' +
						'<td></td>' +
						'<td>' + amtSent.toFixed(2) + '</td>' +
						'<td>' + totalQuickTicketUnits.toFixed(2) + '</td>' +
						'<td></td>' +
						'</tr>';
					tbody.append(totalRow);

				} else if (mode == 2) { // SELL case
					thead.html(
						'<tr>' +
							'<th class="bg-danger text-white">AMC Name</th>' +
							'<th class="bg-danger text-white">Symbol</th>' +
							'<th class="bg-danger text-white">Quick Ticket </th>' +
							'<th class="bg-warning text-white">View QT </th>' +
							'<th class="bg-danger text-white">NAV</th>' +
							'<th class="bg-danger text-white">Quick Ticket Value</th>' +
							'<th class="bg-danger text-white">Ticket Raised</th>' +
							'<th class="bg-warning text-white">Pending Tickets</th>' +
							'<th class="bg-danger text-white">Amount Sent</th>' +
							'<th class="bg-danger text-white">Total Units</th>' +
							'<th class="bg-warning text-white">View </th>' +
						'</tr>'
					);

					data.forEach(function(row) {
						totalQuickTicket += parseFloat(row.total_quick_basket_no || 0);
						totalQuickTicketVal += parseFloat(row.total_quick_amt || 0);
						totalQuickTicketUnits += parseFloat(row.total_quick_units + row.total_ticket_units || 0);
						totalTicket += parseFloat(row.total_ticket_basket_no || 0);
						amtSent += parseFloat(row.total_ticket_actual_amt || 0);

						var tr = '<tr>' +
							'<td>' + (row.security ? row.security.amc.name : '-') + '</td>' +  // AMC Name
							'<td>' + (row.security ? row.security.symbol : '-') + '</td>' +  // Symbol
							'<td>' + (row.total_quick_basket_no == 0 ? '-' : row.total_quick_basket_no) + '</td>' +  // Total Basket No
							'<td>' + (row.total_quick_basket_no == 0 
								? '-' 
								: '<a class="text-info" href="/admin/quick_tickets?sel_from_date=' + selFromDate + '&sel_to_date=' + selToDate + '&sel_query='+ row.security.isin +'&type=2"><i class="ri-eye-fill"></i></a>'
							) + '</td>' +
							'<td>' + (row.total_quick_nav == 0 ? '-' : (row.total_quick_nav / row.total_quick_clubbed)) + '</td>' +  // NAV / Clubbed
							'<td>' + (row.total_quick_amt == 0 ? '-' : row.total_quick_amt) + '</td>' +
							'<td>' + (row.total_ticket_basket_no == 0 ? '-' : row.total_ticket_basket_no) + '</td>' +
							'<td>' + (row.total_quick_basket_no - row.total_ticket_basket_no) + '</td>' +
							'<td>' + (row.total_ticket_actual_amt == 0 ? '-' : row.total_ticket_actual_amt) + '</td>' +                                
							'<td>' + (row.total_quick_units + row.total_ticket_units) + '</td>' +
							'<td>' + (row.total_ticket_basket_no == 0 
								? '-' 
								: '<a class="text-info" href="/admin/tickets?sel_from_date=' + selFromDate + '&sel_to_date=' + selToDate + '&sel_query='+ row.security.isin +'&type=2"><i class="ri-eye-fill"></i></a>'
							) + '</td>' +
							'</tr>';
						tbody.append(tr);
					});

					// Append the total row
					var totalRow = '<tr style="background: grey; color: white;">' +
						'<td colspan="2">Total</td>' +
						'<td>' + totalQuickTicket + '</td>' +
						'<td></td>' +
						'<td></td>' +
						'<td>' + totalQuickTicketVal.toFixed(2) + '</td>' +
						'<td>' + totalTicket + '</td>' +
						'<td></td>' +
						'<td>' + amtSent.toFixed(2) + '</td>' +
						'<td>' + totalQuickTicketUnits.toFixed(2) + '</td>' +
						'<td></td>' +
						'</tr>';
					tbody.append(totalRow);
				}
			}
			
			if( selectedValue == 'accounts' )
			{
				
				$(".table").hide(); 
				$(".accountstable").show();
				
				var table = $('table.accountstable');
				var thead = table.find('thead');
				var tbody = table.find('tbody');
				tbody.empty();

				// Initialize the total amount
				var totalAmount = 0;
				var totalRefund = 0;
				var amountReceived = 0;

				// Get current date
				const currentDate = getCurrentDate();
				const selFromDate = `${currentDate}T00:00:00`;
				const selToDate = `${currentDate}T23:59:59`;

				// Change table headers based on the selected value
				if (mode == 1) { // BUY case
					thead.html(
						'<tr>' +
							'<th class="bg-success text-white">Ticket ID</th>' +
							'<th class="bg-success text-white">AMC Name</th>' +
							'<th class="bg-success text-white">Symbol</th>' +
							'<th class="bg-success text-white">Ticket Amount</th>' +
							'<th class="bg-success text-white">UTR Number</th>' +
							'<th class="bg-success text-white">Refund Received</th>' +
							'<th class="bg-success text-white">View</th>' +
						'</tr>'
					);

					data.forEach(function(row) {
						totalAmount += parseFloat(row.total_amt);
						totalRefund += parseFloat(row.refund);
						var tr = '<tr>' +
							'<td>' + row.id + '. </td>' +  // Assuming id is the Ticket ID
							'<td>' + row.security.amc.name + '</td>' +  // Placeholder for AMC Name, update with correct key
							'<td>' + row.security.symbol + '</td>' +  // Placeholder for Symbol, update with correct key
							'<td>' + row.total_amt + '</td>' +  // Assuming total_amt is the Ticket Amount
							'<td>' + (row.utr_no || 'N/A') + '</td>' +  // Assuming utr_no is the UTR Number
							'<td>' + (row.refund || 'N/A') + '</td>' +  // Assuming refund is the Refund Received
							'<td><a class="text-info" href="/accounts/tickets?sel_from_date=' + selFromDate + '&sel_to_date=' + selToDate + '&sel_query='+ row.security.isin +'&type=1"><i class="ri-eye-fill"></i></a></td>' +
							'</tr>';
						tbody.append(tr);
					});

					// Append the total row
					var totalRow = '<tr style="background: grey; color: white;">' +
						'<td colspan="3">Total</td>' +
						'<td>' + totalAmount.toFixed(2) + '</td>' +
						'<td></td>' +
						'<td>' + totalRefund.toFixed(2) + '</td>' +
						'<td></td>' +
						'</tr>';
					tbody.append(totalRow);

				} else if (mode == 2) { // SELL case
					thead.html(
						'<tr>' +
							'<th class="bg-danger text-white">Deal Date</th>' +
							'<th class="bg-danger text-white">Ticket ID</th>' +
							'<th class="bg-danger text-white">AMC Name</th>' +
							'<th class="bg-danger text-white">Symbol</th>' +
							'<th class="bg-danger text-white">Ticket Amount</th>' +
							'<th class="bg-danger text-white">Amount Received</th>' +
							'<th class="bg-danger text-white">View</th>' +
						'</tr>'
					);

					data.forEach(function(row) {
						totalAmount += parseFloat(row.total_amt);
						amountReceived += parseFloat(row.actual_total_amt);
						var tr = '<tr>' +
							'<td>' + formatDate(row.created_at) + '</td>' +  // Assuming created_at is the Deal Date
							'<td>' + row.id + '. </td>' +  // Assuming id is the Ticket ID
							'<td>' + row.security.amc.name + '</td>' +  // Placeholder for AMC Name, update with correct key
							'<td>' + row.security.symbol + '</td>' +  // Placeholder for Symbol, update with correct key
							'<td>' + row.total_amt + '</td>' +  // Assuming total_amt is the Ticket Amount
							'<td>' + row.actual_total_amt + '</td>' +  // Assuming actual_total_amt is the Amount Received
							'<td><a class="text-info" href="/accounts/tickets?sel_from_date=' + selFromDate + '&sel_to_date=' + selToDate + '&sel_query='+ row.security.isin +'&type=2"><i class="ri-eye-fill"></i></a></td>' +
							'</tr>';
						tbody.append(tr);
					});

					// Append the total row
					var totalRow = '<tr style="background: grey; color: white;">' +
						'<td colspan="4">Total</td>' +
						'<td>' + totalAmount.toFixed(2) + '</td>' +
						'<td>' + amountReceived.toFixed(2) + '</td>' +
						'<td></td>' +
						'</tr>';
					tbody.append(totalRow);
				}
				
			}
		},
		error: function(xhr, status, error) {
			console.error(error);
		}
	});
}



$('select[name="sel_role_id"]').change(function() {
	search();
});

$('select[name="mode"]').change(function() {
	search();
});

$(document).ready(function() { 

    // Trigger change event on page load
	search();
 
});

// Trigger when SEARCH button is hit
function search()
{
	
	var selectedValue = $('select[name="sel_role_id"]').val();
	if(selectedValue)
	{
		loadData(selectedValue);
	}
}

// SHOW Download CSV Button
function showCSVBtn()
{
	var t = jQuery("[name='sel_role_id'] option:selected").val();
    if( t == 'trader' || t == 'ops' )
    {
    	jQuery('.csvExportButton').removeClass("disabled pe-none");
    }
    else 
    {
     	jQuery('.csvExportButton').addClass("disabled pe-none");   	
    }
	
}

// Trigger new URL to start the CSV download
function csvExport()
{
	var t = jQuery("[name='sel_role_id'] option:selected").val();
    if( t == 'trader' || t == 'ops' )
    {
    	var mode = jQuery("[name='mode']").val();
    	var action = base_url + "/csvexport";
    	jQuery("[name='usertype']").val(t);
    	jQuery("[name='datamode']").val(mode);
    	jQuery(".csvExporForm").attr("action", action);
    	jQuery(".csvExporForm").submit();
    }
    else 
    {
    	alert("CSV Export available for OPS and TRADERS only");
    }
}

// Activate/De-Activate on Page LOAD
jQuery(document).ready(function(){
	showCSVBtn();
});
</script>
@endsection