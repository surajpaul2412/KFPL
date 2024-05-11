<?php

namespace App\Http\Controllers\Ops;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Mail\MailToAMC;
use App\Mail\MailScreenshotToAMC;
use App\Services\FormService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Role;
use App\Models\User;
use Auth;
use Storage;
use Exception;
use Validator;
use Illuminate\Database\Eloquent\Builder;

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
            $ticketQuery->whereHas("security", function ($query) use (
                $sel_query
            ) {
                $query
                    ->where("tickets.id", "LIKE", "%{$sel_query}%")
                    ->orWhere("securities.name", "LIKE", "%{$sel_query}%")
                    ->orWhere("securities.symbol", "LIKE", "%{$sel_query}%")
                    ->orWhere("securities.isin", "LIKE", "%{$sel_query}%");
            });
        }

        $tickets = $ticketQuery->whereIn('status_id', [2, 5, 6, 9, 10, 13, 14])
                               ->orderBy('updated_at', 'desc')
                               ->paginate(10);

        //$sql = DB::getQueryLog();
        //dd($sql);
        return view('ops.tickets.index', compact(
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
        try {
            $ticket = Ticket::findOrFail($id);
            $data = $request->all();

            if ($ticket->status_id == 2)
			{
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
					if($ticket->payment_type == 2)
					{
						$data["status_id"] = 5;
					}

                }

                $ticket->save();
                $ticket->update($data);

				if($ticket->payment_type == 2 || ($ticket->type == 2 && $ticket->payment_type == 1) )
				{
					// Pdf Workings :: START
					FormService::GenerateDocument($ticket);
				}

			} elseif ($ticket->status_id == 5) {

				// SELL Cases
                if ($ticket->type == 2) {

					if($ticket->payment_type == 2)
					{
						// SELL BASKET CASES :: Screenshots are not mandatory
						$request->validate([
							"screenshot" =>
								"image|mimes:jpeg,png,jpg,gif,webp",
						]);

					}
					else
					{
						$request->validate([
							"screenshot" =>
								"nullable|image|mimes:jpeg,png,jpg,gif,webp",
						]);
					}

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

				// Buy Basket cases
				if($ticket->type == 1 && $ticket->payment_type == 2)
				{
					$request->validate([
						"cashcomp"    => ["required", "numeric"],
						"deal_ticket" => "nullable",
					]);

					// Check if the request has a file for "basketfile" and if the existing deal_ticket is not null
					if ($request->hasFile("basketfile"))
					{

						// Delete the existing basketfile file
						$f = $ticket->basketfile;
						if(  $f!= '' && Storage::disk("public")->exists($f) )
						{
							Storage::disk("public")->delete($f);
						}

						$bfPath = $request->file("basketfile")->store("basket_file", "public");
						// Set the deal_ticket path without the "storage/" prefix
						$ticket->basketfile = $bfPath;

					}

					$ticket->cashcomp = $request->cashcomp;
				}
				// SELL BASKET CASES
				elseif($ticket->type == 2 && $ticket->payment_type == 2)
				{
					$arr =
					[
						"cashcomp"    => ["required", "numeric"],
						"deal_ticket" => "nullable",
						"totalstampduty" => ["required", "numeric"],
					];

					if( $ticket->screenshot == null ) {
						$arr["screenshot"] = "required|image|mimes:jpeg,png,jpg,gif,webp";
					}

					$request->validate( $arr );

					$ticket->cashcomp = $request->cashcomp;
					$ticket->totalstampduty = $request->totalstampduty;
				}
				else
				{
					// BUY - CASH cases
					$request->validate([
						"refund"      => ["required", "numeric", "lt:" . $actual_total_amt],
						"deal_ticket" => "nullable",
						"screenshot"  => "nullable|image|mimes:jpeg,png,jpg,gif,webp",
					]);

					$ticket->refund = $request->refund;
				}

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
					// BUY + BASKET CASES
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

                $ticket->save();

            } elseif ($ticket->status_id == 10) {
                if ($ticket->type == 2) {
                    $request->validate([
                        "screenshot"  => "nullable|image|mimes:jpeg,png,jpg,gif,webp",
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
                        Mail::to($toEmail)->send(new MailScreenshotToAMC($ticket));
                        $ticket->status_id = 12;
                        $ticket->update();
                    }
                }

			} elseif ($ticket->status_id == 13) {

				$arr = [
                    // 'verification' => 'required|in:1,2',
					"screenshot"     => "required|image|mimes:jpeg,png,jpg,gif,webp",
                ];

				if( $ticket->type == 1 && $ticket->payment_type == 2 && $ticket->basketfile == null ) {
					$arr['basketfile'] = 'required';
				}

				if( $ticket->type == 1 ) {
					$arr['received_units'] = 'required|numeric';
				}

				if( $ticket->deal_ticket == null ) {
					$arr['deal_ticket'] = 'required';
				}

				$request->validate( $arr );

                if ( $request->get("received_units") == $ticket->basket_size * $ticket->basket_no ) {
                    $request->validate([
                        "dispute_comment" => "nullable|string",
                    ]);
                } else {
                    if ($data["dispute_comment"] == null) {
                        return back()->with(
                            "error",
                            "Please fill the Dispute Comment if you changed the unit"
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
					$ticket->save();
                }

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
					$ticket->save();
				}
                $data["status_id"] = 14; //condition can be placed here//
                $ticket->update($data);

				// SEND EMAIL on BUY/BASKET CASES
				if( $ticket->type == 1 && $ticket->payment_type == 2 )
				{
					$emailString = $ticket->security->amc->email ?? null;
					$emailArray = explode(", ", $emailString);
					$toEmail = array_map("trim", $emailArray);
					Log::info("Status 13:: Email Sending");
					Mail::to($toEmail)->send(new MailToAMC($ticket, 13));
				}

            } elseif ($ticket->status_id == 14) {

				$arr = [];
				// BUY basket cases
				if( $ticket->type == 1 && $ticket->payment_type == 2 ) {
					$arr['deal_ticket'] = 'required';
					$arr['received_units'] = 'required|numeric';
				}

				$request->validate( $arr );
				if ( $request->get("received_units") != $ticket->basket_size * $ticket->basket_no ) {
					return redirect()->back()->with("error", "Received Units value is wrong");
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
					$ticket->save();
                }
				
				$ticket->status_id = 15;
				$ticket->save();
                
			}
			

            // $ticket->update($data);
			return redirect()->route('ops.tickets.index')->with('success', 'Ticket updated successfully.');

        } catch (\Exception $e) {
            // dd($e->getMessage());
            return redirect()->back()
                ->with("error", $e->getMessage());
        }




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
        $sendMail = 0;
        // CASH
		if( $ticket->payment_type == 1)
		{
			if ($ticket->type == 2) {
				$sendMail = 1;
				$ticket->status_id = 7;
			} else {
				$sendMail = 1;
				$ticket->status_id = 7;
			}
		} elseif ( $ticket->payment_type == 2) { // BASKET

			// BUY + BASKET
			if($ticket->type == 1 )
			{
			    $sendMail = 1;
			    $ticket->status_id = 9;
			}

			// SELL + BASKET CASES
			if($ticket->type == 2 )
			{
				$sendMail = 1;
				$ticket->status_id = 9;
			}
		}

		// Ticket Updation
		$ticket->save();

		// MAIL Trigger
        if ($sendMail) {
          $emailString = $ticket->security->amc->email ?? null;
          $emailArray = explode(", ", $emailString);
          $toEmail = array_map("trim", $emailArray);
          Mail::to($toEmail)->send(new MailToAMC($ticket));
        }

        return redirect()
            ->route("ops.tickets.index")
            ->with("success", "Mailed all the AMC controllers successfully.");
    }

    public function skip(Ticket $ticket)
    {
        // CASH
		if( $ticket->payment_type == 1)
		{
			if ($ticket->type == 2) {
				$ticket->status_id = 7;
			} else {
				$ticket->status_id = 7;
			}
		} elseif ( $ticket->payment_type == 2) { // BASKET

			// BUY + BASKET
			if($ticket->type == 1 )
			{
			    $ticket->status_id = 9;
			}

			// SELL + BASKET CASES
			if($ticket->type == 2 )
			{
				$ticket->status_id = 9;
			}
		}

		// Ticket Updation
		$ticket->save();

        return redirect()
             ->route("ops.tickets.index")
             ->with("success", "Mailed all the AMC controllers successfully.");
    }
}
