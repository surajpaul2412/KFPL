<?php

namespace App\Http\Controllers\Ops;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Mail\MailToAMC;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tickets = Ticket::whereIn('status_id', [2, 5, 6, 9, 10, 13])
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
        $ticket = Ticket::findOrFail($id);
        $data = $request->all();

        if ($ticket->status_id == 2) {
            $request->validate([
                'verification' => 'required|in:1,2',
                'rate' => 'nullable|numeric',
                'remark' => 'nullable',
            ]);

            if ($request->get('verification') == 1) {
                $data['status_id'] = 3;
            } else {
                $data['status_id'] = 1;
            }

        } elseif ($ticket->status_id == 9) {
            // $request->validate([
            //     'refund' => 'required|numeric',
            //     'deal_ticket' => 'nullable',
            // ]);

            $data['status_id'] = 11;//condition can be placed here//
        } elseif ($ticket->status_id == 13) {
            // $request->validate([
            //     'verification' => 'required|in:1,2',
            //     'received_units' => 'required|numeric',
            //     'dispute_comment' => 'nullable',
            // ]);

            $data['status_id'] = 14;//condition can be placed here//
        } else {
            
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
        $emailData = ['key' => 'value']; // Pass any data needed in the email
        $toEmail = 'suraj.paul.69@gmail.com';

        Mail::to($toEmail)->send(new MailToAMC($emailData));
        // email :: END
        $ticket->status_id = 7;
        $ticket->update();
        return redirect()->route('ops.tickets.index')->with('success', 'Mailed all the AMC controllers successfully.');
    }
}
