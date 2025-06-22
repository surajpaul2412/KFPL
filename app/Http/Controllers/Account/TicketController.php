<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Storage;
use App\Mail\MailToAMC;
use App\Mail\MailScreenshotToAMC;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Role;
use App\Models\User;
use App\Services\FormService;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

		// SEARCH PArameters
        $sel_from_date = isset($request["sel_from_date"])
            ? $request["sel_from_date"]
            : "";
        $sel_to_date = isset($request["sel_to_date"])
            ? $request["sel_to_date"]
            : "";
        $sel_query = isset($request["sel_query"]) ? $request["sel_query"] : "";

        $type = isset($request["type"]) ? $request["type"] : "";

        // GET ALL ROLES
        $roles = Role::where("id", "<>", 1)->get();

        DB::enableQueryLog();

        $ticketQuery = Ticket::with("security");

        if ($sel_from_date != "") {
            $ticketQuery->where("updated_at", ">=", $sel_from_date . " 00:00:00");
        }

        if ($sel_to_date != "") {
            $ticketQuery->where("updated_at", "<=", $sel_to_date . " 23:59:59");
        }

        if ($sel_query != "") {
            $ticketQuery->whereHas("security", function ($query) use ( $sel_query ) {
                $query
                    ->where("tickets.id", "LIKE", "%{$sel_query}%")
                    ->orWhere("securities.name", "LIKE", "%{$sel_query}%")
                    ->orWhere("securities.symbol", "LIKE", "%{$sel_query}%")
                    ->orWhere("securities.isin", "LIKE", "%{$sel_query}%");
            });
        }

        if ($type != "") {
            $ticketQuery->whereType($type);
        }

        $tickets = $ticketQuery->whereIn('status_id', [3, 11, 12])
        			 ->where('is_active', '1')
    				 ->orderBy('updated_at', 'desc')
    				 ->paginate(10);

        //$sql = DB::getQueryLog();
        //dd($sql);
        return view('accounts.tickets.index', compact(
			 "tickets",
             "roles",
             "sel_from_date",
             "sel_to_date",
             "sel_query"
		));
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
        if($ticket->is_active == 0) abort(404); // can Not EDIT Hidden Items
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

			// BUY case
			if ($ticket->type == 1)
			{

				if($ticket->payment_type == 1)
				{
					$request->validate([
						"total_amt_input" => "required|numeric",
						"utr_no" => "required|string",
						"screenshot" => "nullable|file|mimes:jpeg,png,jpg,gif,webp,pdf,doc,docx,csv,xls",
					]);
				}
				else if($ticket->payment_type == 2)
				{
					$request->validate([
						//"total_amt_input" => "required|numeric",
						"cashcomp" => "required|numeric",
						"utr_no" => "required|string",
						"screenshot" => "nullable|file|mimes:jpeg,png,jpg,gif,webp,pdf,doc,docx,csv,xls",
					]);

					// if cashcomponent not matching
					if( $ticket->cashcomp != $request->cashcomp )
					{
						return redirect()->back()->with("error","Please verify Cash Component Figure.");
					}
				}

				// Screenshot Workings
				if (
					$request->hasFile("screenshot") &&
					$ticket->screenshot
				) {
					\Storage::delete($ticket->screenshot);
				}
				if ($request->hasFile("screenshot")) {
					$imagePath = $request
						->file("screenshot")
						->store("screenshot", "public");
					$ticket->screenshot = $imagePath;
				}

				// Handle Cash Component
				if($request->cashcomp != '')
				{
					$ticket->cashcomp = $request->cashcomp;
				}

				// VALIDATION for CASH cases
				$ticket->utr_no = $request->get("utr_no");

				if ( $ticket->payment_type == 1 )
				{
					if( $ticket->total_amt == $request->get("total_amt_input"))
					{
						// CASH cases
						if ($ticket->payment_type == 1) {
							$ticket->status_id = 6;
						}

						// BASKET CASES
						if ($ticket->payment_type == 2) {
							$ticket->status_id = 13;
						}

						//Save Ticket
						$ticket->save();
						$ticket->update($request->except("screenshot"));
					} else {
						return redirect()->back()->with("error","Please verify your entered amount.");
					}
				}

				if ( $ticket->payment_type == 2 )
				{
					$ticket->status_id = 4;
					$ticket->save();
				}


			} else {
				// SELL CASE
				if( $ticket->type == 2 && $ticket->payment_type == 2 )
				{
					$request->validate([
						"totalstampduty" => "required|string",
						"utr_no" => "required|string",
					]);

					if( $ticket->totalstampduty != $request->get("totalstampduty"))
					{
						return redirect()->back()->with("error","Please verify entered Stamp Duty.");
					}

					$ticket->totalstampduty = $request->totalstampduty;
					$ticket->utr_no = $request->utr_no;

				}
				else
				{
					$request->validate([
						"screenshot" => "nullable|file|mimes:jpeg,png,jpg,gif,webp,pdf,doc,docx,csv,xls",
					]);
				}

				// Screenshot Workings
				if (
					$request->hasFile("screenshot") &&
					$ticket->screenshot
				) {
					if (file_exists($ticket->screenshot)) {
						\Storage::delete($ticket->screenshot);
					}
				}
				if ($request->hasFile("screenshot")) {
					$imagePath = $request
						->file("screenshot")
						->store("screenshot", "public");
					$ticket->screenshot = $imagePath;
				}

				// SELL + CASH CASES
				if ($ticket->payment_type == 1) {
					$ticket->status_id = 6;
				}

				// SELL + BASKET CASES
				if ($ticket->payment_type == 2) {
					$ticket->status_id = 13;
				}

				//Save Ticket
				$ticket->save();
				$ticket->update($data);
			}
			// Pdf Workings :: START
			// Prevent PDF generation in STEP 3 for BUY BASKET CASES
			if( ! ( $ticket->type == 1 && $ticket->payment_type == 2) )
			{
				if($ticket->security->amc->generate_form_pdf == 1)
				{
					FormService::GenerateDocument($ticket);
				}
			}

			// SEND EMAIL on BASKET CASES
			if( $ticket->payment_type == 2 )
			{
				
				$alreadyMailSent = 0;
				if($ticket->type == 1 || $ticket->type == 2)
				{
					$ets = $request->get('mailtoself');
					// MAILTOSELF :: Buy Basket cases
					if($ets == 1)
					{
						// MAIL Trigger
						$emailString = env("MAILTOSELF");
						$emailArray = explode(", ", $emailString);
						$toEmail = array_map("trim", $emailArray);
						Mail::to($toEmail)->send(new MailToAMC($ticket));
						$alreadyMailSent = 1;						
					}
				}
				
				if( $ticket->type == 2 && $ticket->totalstampduty == 0 )
				{
					// DO Nothing for SELL-BASKET case with STAMPDUTY 0
				}
				else 
				{
					if( $alreadyMailSent == 0 )
					{
						$emailString = $ticket->security->amc->email ?? null;
						$emailArray = explode(", ", $emailString);
						$toEmail = array_map("trim", $emailArray);
						Mail::to($toEmail)->send(new MailToAMC($ticket, 3)); // 3 is to denote SPECIAL case
					}
				}
			}
			// Pdf Workings :: END

		} elseif ($ticket->status_id == 11) {
			if ($request->get("verification") == 1) {
				$request->validate([
					"expected_refund" => "required|numeric",
					"dispute" => "nullable|string",
					"deal_ticket" => "nullable",
				]);

				if (
					$ticket->refund - $request->get("expected_refund") >
					500
				) {
					return redirect()
						->back()
						->with(
							"error",
							"Your entered amount diff. is more than 500"
						);
				}

				// expected_refund
				if ($ticket->type == 1) {
					$ticket->status_id = 13;
				} else {
					$ticket->status_id = 12;
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


				$ticket->dispute = $request->get("dispute");
			} else {
				$ticket->dispute = $request->get("dispute");
			}

			$ticket->save();

		} elseif ($ticket->status_id == 12) {
				
				//dd($request->all());
				
				if ($ticket->type == 2 && $ticket->payment_type == 1) {
					$arr = [];
					
					// ACCEPT
					if($request->verification == 1)
					{
						$arr['expected_refund'] = 'required';	
						$request->validate( $arr );
						$ticket->expected_refund = $request->expected_refund;
						$ticket->status_id = 14;
						$ticket->save();
					}
					
					// REJECT
					if($request->verification == 2)
					{
						$arr['dispute'] = 'required';	
						$request->validate( $arr );
						$ticket->dispute = $request->dispute;
						$ticket->save();
					}
					
					
				}
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
