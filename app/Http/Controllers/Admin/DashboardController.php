<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Twilio\Rest\Client;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $buyExecuted = Ticket::where('status_id', '>', 8)
                    ->where('type', 1)
                    ->where('payment_type', 1)
                    ->sum('actual_total_amt');

        $sellExecuted = Ticket::where('status_id', '>', 8)
                    ->where('type', 2)
                    ->where('payment_type', 1)
                    ->sum('actual_total_amt');


        $data = [
            'buyExecuted' => $buyExecuted,
            'sellExecuted' => $sellExecuted            
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
