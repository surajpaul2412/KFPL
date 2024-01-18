<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Ticket;

function purchaseNavValue(Ticket $ticket, $actualTotalAmt) {
	$nav_value = purchaseConsideration($ticket, $actualTotalAmt)/totalUnits($ticket);
	return round($nav_value, 4);
}

function purchaseConsideration(Ticket $ticket, $actualTotalAmt) {
	$purchase_consideration = $actualTotalAmt + (($ticket->security->amc->expense_percentage)/100 * $actualTotalAmt) + $ticket->security->cash_component;
	return $purchase_consideration;
}

function totalUnits(Ticket $ticket) {
	$no_of_basket = $ticket->basket_no;
	$basket_size = $ticket->basket_size;
	$total_units = $no_of_basket * $basket_size;
	return $total_units;
}


?>
