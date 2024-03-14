<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;

class DashboardController extends Controller
{
    public function index()
    {
        // Logic to retrieve data for the dealer dashboard
        $data = [
            'title' => 'Dealer Dashboard',
            // Add other data as needed
        ];

        // Return the dealer dashboard view with the data
        return view('dealer.dashboard', $data);
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
