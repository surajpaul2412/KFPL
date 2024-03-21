<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Ticket;
use App\Models\QuickTicket;

function totalTicketAmt(Ticket $ticket) {
    $amt = 0;
    $amt = ($ticket->basket_no * $ticket->basket_size * $ticket->rate) + ($ticket->basket_no * $ticket->basket_size * $ticket->rate) * $ticket->markup_percentage / 100;

    $amt = number_format($amt, 2, '.', '');
    return $amt;
}

function purchaseNavValue(Ticket $ticket, $actualTotalAmt) {
	$nav_value = purchaseConsideration($ticket, $actualTotalAmt)/totalUnits($ticket);
	return round($nav_value, 4);
}

function saleNavValue(Ticket $ticket, $actualTotalAmt) {
	$nav_value = saleConsideration($ticket, $actualTotalAmt)/totalUnits($ticket);
	return round($nav_value, 4);
}

function purchaseConsideration(Ticket $ticket, $actualTotalAmt) {
	$purchase_consideration = $actualTotalAmt + (($ticket->security->amc->expense_percentage)/100 * $actualTotalAmt) + ($ticket->security->cash_component * $ticket->basket_no);
	return $purchase_consideration;
}

function saleConsideration(Ticket $ticket, $actualTotalAmt) {
	$sale_consideration = $actualTotalAmt - (($ticket->security->amc->expense_percentage)/100 * $actualTotalAmt) + ($ticket->security->cash_component * $ticket->basket_no);
	return $sale_consideration;
}

function totalUnits(Ticket $ticket) {
	$no_of_basket = $ticket->basket_no;
	$basket_size = $ticket->basket_size;
	$total_units = $no_of_basket * $basket_size;
	return $total_units;
}







function purchaseNavValueForQuickTicket(QuickTicket $quickTicket, $actualTotalAmt) {
	$nav_value = purchaseConsiderationForQuickTicket($quickTicket, $actualTotalAmt)/totalUnitsForQuickTicket($quickTicket);
	return round($nav_value, 4);
}
function purchaseConsiderationForQuickTicket(QuickTicket $quickTicket, $actualTotalAmt) {
	$purchase_consideration = $actualTotalAmt + (($quickTicket->security->amc->expense_percentage)/100 * $actualTotalAmt) + ($quickTicket->security->cash_component * $quickTicket->basket_no);
	return $purchase_consideration;
}
function totalUnitsForQuickTicket(QuickTicket $quickTicket) {
	$no_of_basket = $quickTicket->basket_no;
	$basket_size = $quickTicket->basket_size;
	$total_units = $no_of_basket * $basket_size;
	return $total_units;
}

?>
