<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\QuickTicket;
use Carbon\Carbon;
use Auth;

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
                            ->where('user_id', Auth::user()->id)
                            ->sum('actual_total_amt');

        $sellQuickTicket = QuickTicket::where(function($query) use ($today) {
                                $query->whereDate('updated_at', $today)
                                    ->orWhereDate('created_at', $today);
                            })
                            ->where('type', 2)
                            ->where('user_id', Auth::user()->id)
                            ->sum('actual_total_amt');

        $buyQuickTicketCount = QuickTicket::whereUserId(Auth::user()->id)->whereType(1)->count();
        $sellQuickTicketCount = QuickTicket::whereUserId(Auth::user()->id)->whereType(2)->count();

        $data = [
            'buyExecuted' => $buyExecuted,
            'sellExecuted' => $sellExecuted,
            'buyQuickTicket' => $buyQuickTicket,
            'sellQuickTicket' => $sellQuickTicket,
            'buyQuickTicketCount' => $buyQuickTicketCount,
            'sellQuickTicketCount' => $sellQuickTicketCount
        ];
        return view('dealer.dashboard', compact('data'));
    }

    public function calculatePurchaseNav(Request $request) {
        $ticket = Ticket::findOrFail($request->get('ticket_id'));
        $nav_value = purchaseNavValue($ticket, $request->input('actual_total_amt'));
        return response()->json(['navValue' => $nav_value]);
    }

    public function calculatePurchaseNavByRequest(Request $request) {
        dd($request->all());
        $actual_total_amt = $request->input('actual_total_amt');

        
        $nav_value = purchaseNavValue($ticket, $request->input('actual_total_amt'));
        return response()->json(['navValue' => $nav_value]);
    }
}
