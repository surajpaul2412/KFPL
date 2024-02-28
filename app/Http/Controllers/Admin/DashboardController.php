<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Twilio\Rest\Client;

class DashboardController extends Controller
{
    public function index()
    {
        // Logic to retrieve data for the admin dashboard
        $data = [
            'title' => 'Admin Dashboard',
            // Add other data as needed
        ];


// Instantiate the Twilio client
// $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));

// $recipients = [
//     'whatsapp:+919810785074',
//     'whatsapp:+918076043823',
// ];
// $from = 'whatsapp:+14155238886';

// foreach ($recipients as $to) {
//     $message = $twilio->messages->create(
//         $to,
//         [
//             'from' => $from,
//             'body' => 'Hello, your ticket has been tested!'
//         ]
//     );

//     // Output the message SID for each recipient
//     echo "Message SID for $to: " . $message->sid . PHP_EOL;
// }

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
