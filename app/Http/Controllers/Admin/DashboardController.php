<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;

class DashboardController extends Controller
{
    public function index()
    {
        // Logic to retrieve data for the admin dashboard
        $data = [
            'title' => 'Admin Dashboard',
            // Add other data as needed
        ];

        // Return the admin dashboard view with the data
        return view('admin.dashboard', $data);
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
