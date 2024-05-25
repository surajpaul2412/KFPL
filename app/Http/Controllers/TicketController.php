<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\QuickTicket;
use Auth;

class TicketController extends Controller
{
    public function checkNewTicket() {
        $latestTicket = Ticket::orderBy('updated_at', 'desc')->first()->updated_at;
        return response()->json([
            'updated_at' => $latestTicket
        ]);
    }

    public function checkNewQuickTicket() {
        $latestTicket = QuickTicket::orderBy('updated_at', 'desc')->first()->updated_at;
        return response()->json([
            'updated_at' => $latestTicket
        ]);
    }

    public function checkOpsTicket()
    {
        $userId = Auth::user()->id;
        
        $ticketCount = Ticket::whereIn('status_id', [2, 5, 6, 9, 10, 13])
            // ->where('user_id', $userId)
            ->count();
        
        return response()->json(['ticket_count' => $ticketCount]);
    }

    public function checkAccountsTicket()
    {
        $userId = Auth::user()->id;
        
        $ticketCount = Ticket::whereIn('status_id', [3,11,12])
            // ->where('user_id', $userId)
            ->count();
        
        return response()->json(['ticket_count' => $ticketCount]);
    }

    public function checkDealerTicket() {
        $userId = Auth::user()->id;
        
        $ticketCount = Ticket::whereIn('status_id', [7,8])
            // ->where('user_id', $userId)
            ->count();
        
        return response()->json(['ticket_count' => $ticketCount]);
    }
}
