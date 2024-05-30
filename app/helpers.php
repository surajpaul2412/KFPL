<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Ticket;
use App\Models\QuickTicket;

function totalTicketAmt(Ticket $ticket) {
    $amt = 0;
    $amt =  ($ticket->basket_no * $ticket->basket_size * $ticket->rate) + 
            ($ticket->basket_no * $ticket->basket_size * $ticket->rate) * $ticket->markup_percentage / 100;
    
    $amt = $amt < 1000 ? $amt : round($amt, -3);
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

function sellNavValueForQuickTicket(QuickTicket $quickTicket, $actualTotalAmt) {
	$nav_value = sellConsiderationForQuickTicket($quickTicket, $actualTotalAmt)/totalUnitsForQuickTicket($quickTicket);
	return round($nav_value, 4);
}

function purchaseConsiderationForQuickTicket(QuickTicket $quickTicket, $actualTotalAmt) {
	$purchase_consideration = $actualTotalAmt + (($quickTicket->security->amc->expense_percentage)/100 * $actualTotalAmt) + ($quickTicket->security->cash_component * $quickTicket->basket_no);
	return $purchase_consideration;
}

function sellConsiderationForQuickTicket(QuickTicket $quickTicket, $actualTotalAmt) {
	$sell_consideration = $actualTotalAmt - (($quickTicket->security->amc->expense_percentage)/100 * $actualTotalAmt) + ($quickTicket->security->cash_component * $quickTicket->basket_no);
	return $sell_consideration;
}

function totalUnitsForQuickTicket(QuickTicket $quickTicket) {
	$no_of_basket = $quickTicket->basket_no;
	$basket_size = $quickTicket->basket_size;
	$total_units = $no_of_basket * $basket_size;
	return $total_units;
}

function convertToCrore($amount) {
    $crore = $amount / 10000000;
    $crore_formatted = number_format((float)$crore, 2, '.', '');
    return $crore_formatted;
}

function lastTicket() {
    $latestTicket = Ticket::orderBy('updated_at', 'desc')->first();
    return $latestTicket ? $latestTicket->updated_at->toISOString() : null;
}

function lastQuickTicket() {
    $latestTicket = QuickTicket::orderBy('updated_at', 'desc')->first();
    return $latestTicket ? $latestTicket->updated_at->toISOString() : null;
}

function opsCount($userId) {
    $ticketCount = Ticket::whereIn('status_id', [2, 5, 6, 9, 10, 13])
        // ->whereUserId($userId)
        ->count();
    return $ticketCount;
}

function accountsCount($userId) {
    $ticketCount = Ticket::whereIn('status_id', [3, 11, 12])
        // ->whereUserId($userId)
        ->count();
    return $ticketCount;
}

function dealerCount($userId) {
    $ticketCount = Ticket::whereIn('status_id', [7, 8])
        // ->whereUserId($userId)
        ->count();
    return $ticketCount;
}

?>
