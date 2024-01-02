<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Storage;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tickets = Ticket::whereIn('status_id', [3, 11])
         ->orderBy('id')
         ->paginate(10);

         return view('accounts.tickets.index', compact('tickets'));
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        return view('accounts.tickets.edit', ['ticket' => $ticket]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $data = $request->all();

        if ($ticket->status_id == 3) {
            $request->validate([
                'utr_no' => 'required|string',
                'screenshot' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
            ]);

            if ($request->hasFile('screenshot') && $ticket->screenshot) {
                Storage::delete($ticket->screenshot);
            }
            if ($request->hasFile('screenshot')) {
                $imagePath = $request->file('screenshot')->store('screenshot', 'public');
                $ticket->screenshot = $imagePath;
            }

            $ticket->status_id = $request->get('utr_no');
            if ($ticket->type == 1 && $ticket->payment_type == 1) {
                $ticket->status_id = 6;
            }

            $ticket->update($request->except('screenshot'));
        } elseif ($ticket->status_id == 11) {
            // $request->validate([
            // ]);

            if ($ticket->type == 1) {
                $ticket->status_id = 13;
            } else {
                $ticket->status_id = 12;
            }

            $ticket->update();
        }
        
        return redirect()->route('accounts.tickets.index')->with('success', 'Ticket updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
