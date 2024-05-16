<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Role;
use App\Models\User;
use App\Models\QuickTicket;
use App\Models\Ticket;
use App\Models\Security;
use Illuminate\Support\Facades\DB;

class QuickTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tickets = QuickTicket::orderBy("updated_at", "desc")->paginate(10);
        return view(
            "admin.quick_tickets.index",
            compact(
                "tickets"
            )
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $quickTicket = QuickTicket::findOrFail($id);

        // Create a new Ticket instance
        $ticket = new Ticket();
        $ticket->fill([
            'user_id' => $quickTicket->trader_id,
            'security_id' => $quickTicket->security_id,
            'type' => $quickTicket->type,
            'payment_type' => $quickTicket->payment_type,
            'basket_no' => $quickTicket->basket_no,
            'basket_size' => $quickTicket->basket_size,
            'rate' => 0,
            'security_price' => 0,
            'markup_percentage' => 0,
            'actual_total_amt' => $quickTicket->actual_total_amt,
            'nav' => $quickTicket->nav,
            'status_id' => 1
        ]);

        // Save the new Ticket instance
        $ticket->save();

        // Delete the QuickTicket
        $quickTicket->delete();

        return redirect()->route('admin.quick_tickets.index')->with('success', 'Quick Ticket converted successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
