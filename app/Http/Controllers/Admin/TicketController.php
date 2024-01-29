<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Security;
use Exception;
use Validator;
use App\Services\FormService;

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
            'basket_size' => 'required|integer',
            'rate' => 'required|numeric',
            'security_price' => 'required|numeric',
            'markup_percentage' => 'required|numeric',
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


        // FIND TICKET
        $ticket = Ticket::findOrFail($id);
        $data = $request->all();
        $data['status_id'] = 2;

        // SET STATUS as per OTHER PARAMETERS
        if ($ticket->status_id == 1) {
            $validatedData = $request->validate([
                'security_id' => 'required|exists:securities,id',
                'type' => 'required|integer|in:1,2',
                'payment_type' => 'required|integer|in:1,2,3',
                'basket_no' => 'required|integer',
                'rate' => 'required|numeric',
                'total_amt' => 'required|numeric',
            ]);
        } else if ($ticket->status_id == 8) {
            $request->validate([
                'actual_total_amt' => 'required|numeric',
                'nav' => 'required|numeric'
            ]);

            $data['status_id'] = 9;
        } else if ($ticket->status_id == 3) {
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

                //Save Ticket
                $ticket->save();

                // Update Ticket
                $ticket->update($request->except('screenshot'));

                // Pdf Workings :: START
                FormService::GenerateDocument($ticket);
                // Pdf Workings :: END

            } else {
                return redirect()->back()->with('error', 'Please verify your entered amount.');
            }
        } else if ($ticket->status_id == 11) {
            $request->validate([
                'expected_refund' => 'required|numeric',
                'remark' => 'nullable|string',
            ]);

            if ($ticket->type == 1) {
                $ticket->status_id = 13;
            } else {
                $ticket->status_id = 12;
            }

            $ticket->save();

        } else if ($ticket->status_id == 2) {
            $request->validate([
                'verification' => 'required|in:1,2',
                'rate' => 'nullable|numeric',
                'remark' => 'nullable',
            ]);

            if ($request->get('verification') == 1) {
                  $ticket->status_id = 3;
            } else {
                  $ticket->status_id = 1;
            }

            $ticket->save();

        } elseif ($ticket->status_id == 9) {
            $request->validate([
                'refund' => 'required|numeric',
                'deal_ticket' => 'required',
            ]);

            // Deal Ticket Wrokings
            if ($request->hasFile('deal_ticket') && $ticket->deal_ticket) {
                Storage::delete($ticket->deal_ticket);
            }
            if ($request->hasFile('deal_ticket')) {
                $imagePath = $request->file('deal_ticket')->store('deal_ticket', 'public');
                $ticket->deal_ticket = $imagePath;
            }

            $ticket->status_id = 11;//condition can be placed here//
            $ticket->save();

            // Update Ticket with POST DAta
            $ticket->update($request->except('screenshot'));

        } elseif ($ticket->status_id == 13) {
            $request->validate([
                // 'verification' => 'required|in:1,2',
                'received_units' => 'required|numeric',
            ]);

            if ($request->get('received_units') == ($ticket->basket_size * $ticket->basket_no)) {
                $request->validate([
                    'remark' => 'nullable|string',
                ]);
            } else {
                if ($data['remark'] == null) {
                    return back()->with('error','Please fill the Dispute Comment if you changes the amount');
                }
            }

            $data['status_id'] = 14;//condition can be placed here//

        } else {

        }

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
