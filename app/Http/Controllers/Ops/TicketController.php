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
         ->orderBy('updated_at', 'desc')
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
            $request->validate([
                "refund" => "required|numeric",
                "deal_ticket" => "nullable",
                "screenshot" =>
                    "nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048",
            ]);

            // Deal Ticket Wrokings
            if ($request->hasFile("deal_ticket") && $ticket->deal_ticket) {
                Storage::delete($ticket->deal_ticket);
            }

            if ($request->hasFile("deal_ticket")) {
                $imagePath = $request
                    ->file("deal_ticket")
                    ->store("deal_ticket", "public");
                $ticket->deal_ticket = "storage/" . $imagePath;
            }

            if ($request->hasFile("screenshot")) {
                // IF Old one exists, remove it
                if ($ticket->screenshot != "") {
                    if (
                        Storage::disk("public")->exists($ticket->screenshot)
                    ) {
                        Storage::disk("public")->delete(
                            $ticket->screenshot
                        );
                    }
                }
                $imagePath = $request
                    ->file("screenshot")
                    ->store("screenshot", "public");
                $ticket->screenshot = "storage/" . $imagePath;
            }

            if ($ticket->type == 1) {
                $ticket->status_id = 11; // BUY CASE
            } elseif ($ticket->type == 2) {
                $ticket->status_id = 10; // SELL CASE
            }

            // Update Ticket with POST DAta
            $ticket->refund = $data["refund"] ? $data["refund"] : 0;
            $ticket->save();
        } elseif ($ticket->status_id == 13) {
            $request->validate([
                // 'verification' => 'required|in:1,2',
                "received_units" => "required|numeric",
                "deal_ticket" => "nullable",
            ]);

            if (
                $request->get("received_units") ==
                $ticket->basket_size * $ticket->basket_no
            ) {
                $request->validate([
                    "dispute_comment" => "nullable|string",
                ]);
            } else {
                if ($data["dispute_comment"] == null) {
                    return back()->with(
                        "error",
                        "Please fill the Dispute Comment if you changes the unit"
                    );
                }
            }
            // Deal Ticket Workings
            if ($request->hasFile("deal_ticket") && $ticket->deal_ticket) {
                Storage::delete($ticket->deal_ticket);
            }

            if ($request->hasFile("deal_ticket")) {
                $imagePath = $request
                    ->file("deal_ticket")
                    ->store("deal_ticket", "public");
                $ticket->deal_ticket = $imagePath;
            }

            $data["status_id"] = 14; //condition can be placed here//
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
        $emailString = $ticket->security->amc->email??null;
        $emailArray = explode(', ', $emailString);
        $toEmail = array_map('trim', $emailArray);

        Mail::to($toEmail)->send(new MailToAMC($ticket));

        $ticket->status_id = 7;
        $ticket->update();
        return redirect()->route('ops.tickets.index')->with('success', 'Mailed all the AMC controllers successfully.');
    }
}
