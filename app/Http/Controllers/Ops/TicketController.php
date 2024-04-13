<?php

namespace App\Http\Controllers\Ops;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Mail\MailToAMC;
use App\Services\FormService;
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
            if ($ticket->type == 1) {
                // BUY cases
                $request->validate([
                    "verification" => "required|in:1,2",
                    "rate" => "nullable|numeric",
                    "remark" => "nullable",
                ]);

                if ($request->get("verification") == 1) {
                    $ticket->status_id = 3;
                    // Basket CASE
                    if( $ticket->payment_type == 2 )
                    {
                        $ticket->status_id = 6;
                    }
                } else {
                    $ticket->status_id = 1;
                }
            } else {

                // SALE CASES
                $ticket->status_id = 5;

                // BASKET CASES
                if($ticket->type == 2 && $ticket->payment_type == 2)
                {
                    $data["status_id"] = 5;
                }

                FormService::GenerateDocument($ticket);
            }
            $ticket->save();
            $ticket->update($data);
        } elseif ($ticket->status_id == 5) {
            // SELL Cases
            if ($ticket->type == 2) {
                $request->validate([
                    "screenshot" =>
                        "nullable|image|mimes:jpeg,png,jpg,gif,webp",
                ]);

                if ($request->hasFile("screenshot")) {
                    // IF Old one exists, remove it
                    if ($ticket->screenshot != "") {
                        if (file_exists($ticket->screenshot)) {
                            \Storage::delete($ticket->screenshot);
                        }
                    }
                    // SAVE new FILE
                    $imagePath = $request
                        ->file("screenshot")
                        ->store("screenshot", "public");
                    $ticket->screenshot = $imagePath;
                }

                $ticket->status_id = 6;

                // BASKET CASES
                if($ticket->payment_type == 2)
                {
                    $ticket->status_id = 6;
                }

                $ticket->save();
            }

        } elseif ($ticket->status_id == 9) {
            $actual_total_amt = $ticket->actual_total_amt;
            $request->validate([
                "refund" => ["required", "numeric", "lt:" . $actual_total_amt],
                "deal_ticket" => "nullable",
                "screenshot" =>
                    "nullable|image|mimes:jpeg,png,jpg,gif,webp",
            ]);

            // Check if the request has a file for "deal_ticket" and if the existing deal_ticket is not null
            if ($request->hasFile("deal_ticket") && $ticket->deal_ticket) {
                // Delete the existing deal_ticket file
                Storage::disk("public")->delete($ticket->deal_ticket);
            }

            // Check if the request has a file for "deal_ticket"
            if ($request->hasFile("deal_ticket")) {
                // Store the uploaded file and update the deal_ticket path
                $imagePath = $request->file("deal_ticket")->store("deal_ticket", "public");
                // Set the deal_ticket path without the "storage/" prefix
                $ticket->deal_ticket = $imagePath;
            }

            // screeshot
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

            if ($ticket->type == 1)  // BUY CASE
            {
                $ticket->status_id = 11;
                if($ticket->payment_type == 2)
                {
                    $ticket->status_id = 3;
                }
            }
            elseif ($ticket->type == 2) // SELL CASE
            {
                $ticket->status_id = 10;

                // SELL + BASKET CASES
                if($ticket->payment_type == 2)
                {
                    $ticket->status_id = 3;
                }
            }

            // Update Ticket with POST DAta
            $ticket->refund = $data["refund"] ? $data["refund"] : 0;
            $ticket->save();
        } elseif ($ticket->status_id == 10) {
            if ($ticket->type == 2) {
                $request->validate([
                    "screenshot" =>
                        "nullable|image|mimes:jpeg,png,jpg,gif,webp",
                    "deal_ticket" => "nullable",
                ]);

                if ($request->hasFile("screenshot")) {
                    // IF Old one exists, remove it
                    if ($ticket->screenshot != "") {
                        if (file_exists($ticket->screenshot)) {
                            \Storage::delete($ticket->screenshot);
                        }
                    }
                    // SAVE new FILE
                    $imagePath = $request
                        ->file("screenshot")
                        ->store("screenshot", "public");
                    $ticket->screenshot = $imagePath;
                }

                // Deal Ticket Workings
                if ($request->hasFile("deal_ticket") && $ticket->deal_ticket) {
                    // Delete the existing deal_ticket file
                    Storage::disk("public")->delete($ticket->deal_ticket);
                }

                // Check if the request has a file for "deal_ticket"
                if ($request->hasFile("deal_ticket")) {
                    // Store the uploaded file and update the deal_ticket path
                    $imagePath = $request->file("deal_ticket")->store("deal_ticket", "public");
                    // Set the deal_ticket path without the "storage/" prefix
                    $ticket->deal_ticket = $imagePath;
                }

                $ticket->status_id = 12; // SELL CASE
                // mailing for sell
                FormService::GenerateDocument($ticket);
                $ticket->save();

                // Trigger mail if SS uploaded
                if ($request->hasFile("screenshot")) {
                    $emailString = $ticket->security->amc->email ?? null;
                    $emailArray = explode(", ", $emailString);
                    $toEmail = array_map("trim", $emailArray);

                    Mail::to($toEmail)->send(new MailToAMC($ticket));

                    $ticket->status_id = 12;
                    $ticket->update();
                }
            }
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
                // Delete the existing deal_ticket file
                Storage::disk("public")->delete($ticket->deal_ticket);
            }

            // Check if the request has a file for "deal_ticket"
            if ($request->hasFile("deal_ticket")) {
                // Store the uploaded file and update the deal_ticket path
                $imagePath = $request->file("deal_ticket")->store("deal_ticket", "public");
                // Set the deal_ticket path without the "storage/" prefix
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

    public function mail(Ticket $ticket)
    {
        // sell case with null screenshot check
        if ($ticket->type == 2 && $ticket->screenshot == null) {
            $ticket->status_id = 7;
            $ticket->update();
        } else {
            $emailString = $ticket->security->amc->email ?? null;
            $emailArray = explode(", ", $emailString);
            $toEmail = array_map("trim", $emailArray);

            Mail::to($toEmail)->send(new MailToAMC($ticket));

            $ticket->status_id = 7;
            $ticket->update();
        }

        return redirect()
            ->route("ops.tickets.index")
            ->with("success", "Mailed all the AMC controllers successfully.");
    }
}
