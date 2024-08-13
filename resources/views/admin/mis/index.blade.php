@extends('layouts.dashboard')

@section('breadcrum')
Ticket Management
@endsection

@section('content')

@include('topmessages')
<div class="d-sm-flex align-items-center justify-content-between">
    <form method="get" action="">
      <div style="display:inline-flex;margin-right:10px;">
        
        <select class="form-select mx-2" name="sel_role_id" style="display:inline;width:auto !important;">
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
	  
    </form>
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
	    var base_url = "@php echo url('/admin/mis'); @endphp";
</script>
@endsection

@section('script')
<script>

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

function loadData(selectedValue) 
{
    var mode = jQuery("[name='mode']").val();	
	
	console.log("loadData function called with Mode : " + mode + ", usertype : " + selectedValue);
	
	$.ajax({
		url: "{{route('admin.mis.ajax')}}",  
		type: 'GET',
		data: { sel_role_id: mode, usertype : selectedValue }, // Decide, Is it a BUY or SELL data
		success: function(data) {
			var table = $('table.table');
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
							: '<a class="text-info" href="/trader/quick_tickets?sel_from_date=' + selFromDate + '&sel_to_date=' + selToDate + '&sel_query='+ row.security.isin +'&type=1"><i class="ri-eye-fill"></i></a>'
						) + '</td>' +
						'<td>' + (row.total_quick_nav == 0 ? '-' : (row.total_quick_nav / row.total_quick_clubbed)) + '</td>' +  // NAV / Clubbed
						'<td>' + (row.total_quick_amt == 0 ? '-' : row.total_quick_amt) + '</td>' +
						'<td>' + (row.total_ticket_basket_no == 0 ? '-' : row.total_ticket_basket_no) + '</td>' +
						'<td>' + (row.total_quick_basket_no - row.total_ticket_basket_no) + '</td>' +
						'<td>' + (row.total_ticket_actual_amt == 0 ? '-' : row.total_ticket_actual_amt) + '</td>' +
						'<td>' + (row.total_quick_units + row.total_ticket_units) + '</td>' +
						'<td>' + (row.total_ticket_basket_no == 0 
							? '-' 
							: '<a class="text-info" href="/trader/tickets?sel_from_date=' + selFromDate + '&sel_to_date=' + selToDate + '&sel_query='+ row.security.isin +'&type=1"><i class="ri-eye-fill"></i></a>'
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
							: '<a class="text-info" href="/trader/quick_tickets?sel_from_date=' + selFromDate + '&sel_to_date=' + selToDate + '&sel_query='+ row.security.isin +'&type=2"><i class="ri-eye-fill"></i></a>'
						) + '</td>' +
						'<td>' + (row.total_quick_nav == 0 ? '-' : (row.total_quick_nav / row.total_quick_clubbed)) + '</td>' +  // NAV / Clubbed
						'<td>' + (row.total_quick_amt == 0 ? '-' : row.total_quick_amt) + '</td>' +
						'<td>' + (row.total_ticket_basket_no == 0 ? '-' : row.total_ticket_basket_no) + '</td>' +
						'<td>' + (row.total_quick_basket_no - row.total_ticket_basket_no) + '</td>' +
						'<td>' + (row.total_ticket_actual_amt == 0 ? '-' : row.total_ticket_actual_amt) + '</td>' +                                
						'<td>' + (row.total_quick_units + row.total_ticket_units) + '</td>' +
						'<td>' + (row.total_ticket_basket_no == 0 
							? '-' 
							: '<a class="text-info" href="/trader/tickets?sel_from_date=' + selFromDate + '&sel_to_date=' + selToDate + '&sel_query='+ row.security.isin +'&type=2"><i class="ri-eye-fill"></i></a>'
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
</script>
@endsection