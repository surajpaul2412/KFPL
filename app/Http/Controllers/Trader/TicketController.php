<?php

namespace App\Http\Controllers\Trader;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Security;
use Exception;
use Validator;
use Auth;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tickets = Ticket::whereUserId(Auth::user()->id)
         ->orderBy('created_at', 'desc')
         ->paginate(10);

         return view('trader.tickets.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $securities = Security::whereStatus(1)->get();
        return view('trader.tickets.create', compact('securities'));
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
            'basket_size' => 'required|integer',
            'rate' => 'required|numeric',
            'security_price' => 'required|numeric',
            'markup_percentage' => 'required|numeric',
            'total_amt' => 'required|numeric',
        ]);

        $validatedData['user_id'] = Auth::user()->id;
        $validatedData['status_id'] = 2;

        $ticket = Ticket::create($validatedData);
        return redirect()->route('trader.tickets.index')->with('success', 'Ticket created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        return view('trader.tickets.show', compact('ticket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $securities = Security::whereStatus(1)->get();
        return view('trader.tickets.edit', compact('ticket', 'securities'));
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

        return redirect()->route('trader.tickets.index')->with('success', 'Ticket updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getSecurityDetails($id) {
        $security = Security::findOrFail($id);

        if (!$security) {
            return response()->json(['error' => 'Security not found'], 404);
        }

        return response()->json([
            'security' => $security
        ]);
    }
}
