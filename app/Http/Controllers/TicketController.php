<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function checkNewTicket()
    {
        $latestTicketId = Ticket::max('id');
        return response()->json(['latest_ticket_id' => $latestTicketId]);
    }
}
