<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Security;
use Exception;
use Validator;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     // Listing
     public function index(Request $request)
     {
        $tickets = Ticket::orderBy('id')->paginate(10);
        return view('admin.tickets.index', compact('tickets'));
     }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $securities = Security::whereStatus(1)->get();
        return view('admin.tickets.create', compact('securities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'security_id' => 'required|exists:securities,id',
            'type' => 'required|integer|in:1,2',
            'payment_type' => 'required|integer|in:1,2,3',
            'basket_no' => 'required|integer',
            'rate' => 'required|numeric',
            'total_amt' => 'required|numeric',
        ]);

        $validatedData['user_id'] = Auth::user()->id;
        $validatedData['status_id'] = 2;
        $ticket = Ticket::create($validatedData);

        return redirect()->route('admin.tickets.index')->with('success', 'Ticket created successfully.');
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
        $securities = Security::whereStatus(1)->get();
        return view('admin.tickets.edit', compact('ticket', 'securities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'security_id' => 'required|exists:securities,id',
            'type' => 'required|integer|in:1,2',
            'payment_type' => 'required|integer|in:1,2,3',
            'basket_no' => 'required|integer',
            'rate' => 'required|numeric',
            'total_amt' => 'required|numeric',
        ]);

        $ticket = Ticket::findOrFail($id);
        $data = $request->all();
        $data['status_id'] = 2;
        $ticket->update($data);

        return redirect()->route('admin.tickets.index')->with('success', 'Ticket updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
