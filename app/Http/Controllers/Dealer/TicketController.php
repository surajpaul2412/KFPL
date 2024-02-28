<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tickets = Ticket::whereIn('status_id', [7, 8])
         ->orderBy('created_at', 'desc')
         ->paginate(10);

         return view('dealer.tickets.index', compact('tickets'));
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
        $ticket = Ticket::findOrFail($id);
        return view('dealer.tickets.show', compact('ticket'));
    }

    public function statusUpdate(Ticket $ticket) {
        $ticket->status_id = 8;
        $ticket->update();
        return redirect()->route('dealer.tickets.index')->with('success', 'Accepted ticket successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        return view('dealer.tickets.edit', compact('ticket'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $data = $request->all();

        if ($ticket->status_id == 8) {
            $request->validate([
                'actual_total_amt' => 'required|numeric',
                'nav' => 'required|numeric'
            ]);

            $data['status_id'] = 9;
        }        

        $ticket->update($data);
        return redirect()->route('dealer.tickets.index')->with('success', 'Ticket updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
