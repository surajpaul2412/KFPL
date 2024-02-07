<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Storage;
use App\Services\FormService;

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
    public function edit(string $id) {

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
                'total_amt' => 'required|numeric',
                'utr_no' => 'required|string',
                'screenshot' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
            ]);

            if ($ticket->total_amt == $request->get('total_amt')) {
                // Screenshot Wrokings
                if ($request->hasFile('screenshot') && $ticket->screenshot) {
                    Storage::delete($ticket->screenshot);
                }
                if ($request->hasFile('screenshot')) {
                    $imagePath = $request->file('screenshot')->store('screenshot', 'public');
                    $ticket->screenshot = $imagePath;
                }

                $ticket->utr_no = $request->get('utr_no');
                if ($ticket->type == 1 && $ticket->payment_type == 1) {
                    $ticket->status_id = 6;
                }

                $ticket->update($request->except('screenshot'));

                // Pdf Workings :: START
                FormService::GenerateDocument($ticket);
                // Pdf Workings :: END

            } else {
                return redirect()->back()->with('error', 'Please verify your entered amount.');
            }
        } elseif ($ticket->status_id == 11) {
            if ($request->get('verification') == 1) {
                $request->validate([
                    'expected_refund' => 'required|numeric',
                    'dispute' => 'nullable|string',
                ]);

                if ( $ticket->refund - $request->get('expected_refund') > 500) {
                    return redirect()->back()->with('error', 'Your entered amount diff. is more than 500');
                }

                // expected_refund


                if ($ticket->type == 1) {
                    $ticket->status_id = 13;
                } else {
                    $ticket->status_id = 12;
                }
                $ticket->dispute = $request->get('dispute');
            } else {
                $ticket->dispute = $request->get('dispute');
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
