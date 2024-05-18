<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\QuickTicket;
use Twilio\Rest\Client;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();

        $buyExecuted = Ticket::where('status_id', '>', 8)
                    ->where('type', 1)
                    ->where('payment_type', 1)
                    ->whereDate('updated_at', $today)
                    ->sum('actual_total_amt');

        $sellExecuted = Ticket::where('status_id', '>', 8)
                    ->where('type', 2)
                    ->where('payment_type', 1)
                    ->whereDate('updated_at', $today)
                    ->sum('actual_total_amt');

        $buyQuickTicket = QuickTicket::where(function($query) use ($today) {
                                $query->whereDate('updated_at', $today)
                                    ->orWhereDate('created_at', $today);
                            })
                            ->where('type', 1)
                            ->sum('actual_total_amt');

        $sellQuickTicket = QuickTicket::where(function($query) use ($today) {
                                $query->whereDate('updated_at', $today)
                                    ->orWhereDate('created_at', $today);
                            })
                            ->where('type', 2)
                            ->sum('actual_total_amt');

        // Units To Be Transfered
        $unitsToBeTransfered = Ticket::where('type', 2)
                    ->whereBetween('status_id', [2, 5])
                    ->whereDate('updated_at', $today)
                    ->count();

        // Units Transfered
        $unitsTransfered = Ticket::where('type', 2)
                    ->where('status_id', '>', 6)
                    ->whereDate('updated_at', $today)
                    ->count();  

        // Redemption Amount Receivable
        $redemptionAmountReceivable = Ticket::where('type', 2)
                                            ->where('payment_type', 1)
                                            ->where('status_id', '>', 9)
                                            ->whereDate('updated_at', $today)
                                            ->sum('refund');

        // Redemption Amount Received
        $redemptionAmountReceived = Ticket::where('type', 2)
                                            ->wherePaymentType(1)
                                            ->where('status_id', '>', 12)
                                            ->whereDate('updated_at', $today)
                                            ->sum('refund');

        // Refund Amount Received
        $refundAmountReceived = Ticket::where('type', 1)
                                            ->wherePaymentType(1)
                                            ->where('status_id', '>', 11)
                                            ->whereDate('updated_at', $today)
                                            ->sum('refund');

        // Graph
        $statuses = [
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14
        ];

        $arrangedBuyCounts = [];
        $arrangedSellCounts = [];
        foreach ($statuses as $status) {
            $buyCount = Ticket::whereStatusId($status)
                            ->where('type', 1)
                            ->where('payment_type', 1)
                            ->whereDate('updated_at', $today)
                            ->count();
            $arrangedBuyCounts[] = $buyCount;

            $sellCount = Ticket::whereStatusId($status)
                            ->where('type', 2)
                            ->where('payment_type', 1)
                            ->whereDate('updated_at', $today)
                            ->count();
            $arrangedSellCounts[] = $sellCount;
        }

        $data = [
            'buyExecuted' => $buyExecuted,
            'sellExecuted' => $sellExecuted,
            'buyQuickTicket' => $buyQuickTicket,
            'sellQuickTicket' => $sellQuickTicket,
            'redemptionAmountReceivable' => $redemptionAmountReceivable,
            'redemptionAmountReceived' => $redemptionAmountReceived,
            'refundAmountReceived' => $refundAmountReceived,           
            'arrangedBuyCounts' => $arrangedBuyCounts,
            'arrangedSellCounts' => $arrangedSellCounts,
            'unitsToBeTransfered' => $unitsToBeTransfered,
            'unitsTransfered' => $unitsTransfered
        ];
        return view('admin.dashboard', compact('data'));
    }

    public function calculatePurchaseNav(Request $request) {
        $ticket = Ticket::findOrFail($request->get('ticket_id'));
        if ($ticket->type === 1) {
            $nav_value = purchaseNavValue($ticket, $request->input('actual_total_amt'));
        } elseif ($ticket->type === 2) {
            $nav_value = saleNavValue($ticket, $request->input('actual_total_amt'));
        }
        return response()->json(['navValue' => $nav_value]);
    }
}
