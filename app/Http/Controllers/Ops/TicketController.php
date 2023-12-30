<?php

namespace App\Http\Controllers\Ops;

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
        $tickets = Ticket::whereIn('status_id', [2, 6, 9, 10, 13])
         ->orderBy('id')
         ->paginate(10);

         return view('ops.tickets.index', compact('tickets'));
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
        return view('ops.tickets.show', compact('ticket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        return view('ops.tickets.edit', compact('ticket'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'verification' => 'required|in:1,2',
            'rate' => 'required|numeric',
            'remark' => 'nullable',
        ]);
        $ticket = Ticket::findOrFail($id);
        $data = $request->all();

        if ($request->get('verification') == 1) {
            $data['status_id'] = 3;
        } else {
            $data['status_id'] = 1;
        }

        $ticket->update($data);
        return redirect()->route('ops.tickets.index')->with('success', 'Ticket updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function mail(Ticket $ticket) {
        // Write the email sending code || under progress
        $ticket->status_id = 7;
        $ticket->update();
        return redirect()->route('ops.tickets.index')->with('success', 'Mailed all the AMC controllers successfully.');
    }
}
