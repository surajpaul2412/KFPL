<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Security;
use Exception;
use Validator;
use Auth;
use Storage;
use App\Services\FormService;
use App\Mail\MailToAMC;
use App\Mail\MailScreenshotToAMC;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Database\Eloquent\Builder;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Listing
    public function index(Request $request)
    {
        // SEARCH PArameters
        $sel_status_id = isset($request["sel_status_id"])
            ? $request["sel_status_id"]
            : "";
        $sel_from_date = isset($request["sel_from_date"])
            ? $request["sel_from_date"]
            : "";
        $sel_to_date = isset($request["sel_to_date"])
            ? $request["sel_to_date"]
            : "";
        $sel_role_id = isset($request["sel_role_id"])
            ? $request["sel_role_id"]
            : "";
        $sel_query = isset($request["sel_query"]) ? $request["sel_query"] : "";

        $type = isset($request["type"]) ? $request["type"] : "";

        // GET ALL ROLES
        $roles = Role::where("id", "<>", 1)->get();

        DB::enableQueryLog();

        $ticketQuery = Ticket::with("security");

        if ($sel_status_id != "") {
            $ticketQuery->where("status_id", $sel_status_id);
        }
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

        if ($type != "") {
            $ticketQuery->whereType($type);
        }

        if ($sel_role_id != "") {
            $ticketQuery->whereHas("userroles", function ($query) use (
                $sel_role_id
            ) {
                $query->whereRaw(
                    "tickets.user_id = role_user.user_id and role_user.role_id = " .
                        $sel_role_id
                );
            });
        }

        $tickets = $ticketQuery->orderBy("updated_at", "desc")->paginate(10);
        $sql = DB::getQueryLog();
        //dd($sql);
        return view(
            "admin.tickets.index",
            compact(
                "tickets",
                "roles",
                "sel_status_id",
                "sel_from_date",
                "sel_to_date",
                "sel_role_id",
                "sel_query"
            )
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $securities = Security::whereStatus(1)->orderBy("amc_id", "asc")->get();
        return view("admin.tickets.create", compact("securities"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            "security_id" => "required|exists:securities,id",
            "type" => "required|integer|in:1,2",
            "payment_type" => "required|integer|in:1,2,3",
            "basket_no" => "required|integer",
            "basket_size" => "required|integer",
            "rate" => "required|numeric",
            "security_price" => "required|numeric",
            "markup_percentage" => "required|numeric"
        ]);

        // if SELL
        if ($validatedData["type"] == 2) {
            $validatedData["markup_percentage"] = 0;
            $validatedData["rate"] = 0;
            $validatedData["security_price"] = 0;
        }

        $validatedData["user_id"] = Auth::user()->id;
        $validatedData["status_id"] = 2;

        $ticket = Ticket::create($validatedData);
        return redirect()
            ->route("admin.tickets.index")
            ->with("success", "Ticket created successfully.");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        return view("admin.tickets.show", compact("ticket"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {   
		$ticket = Ticket::findOrFail($id);
		$securities = Security::whereStatus(1)->get();
        return view("admin.tickets.edit", compact("ticket", "securities"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $data = $request->all();

            // SET STATUS as per OTHER PARAMETERS
            if ($ticket->status_id == 1) {

                $validatedData = $request->validate([
                    "security_id" => "required|exists:securities,id",
                    "type" => "required|integer|in:1,2",
                    "payment_type" => "required|integer|in:1,2,3",
                    "basket_no" => "required|integer",
                    "rate" => "required|numeric",
                    "markup_percentage" => "required|numeric",
                ]);

                $data["status_id"] = 2;

				// BUY / BASKET cases
				if($ticket->type == 1 && $ticket->payment_type == 2)
				{
					$data["status_id"] = 2;
				}

				// SELL / BASKET cases
				if($ticket->type == 2 && $ticket->payment_type == 2)
				{
					$data["status_id"] = 2;
				}

                $ticket->update($data);

            } elseif ($ticket->status_id == 2) {

                if ($ticket->type == 1) {
                    // BUY cases
                    $request->validate([
                        "verification" => "required|in:1,2",
                        "rate"   => "nullable|numeric",
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

					if ($request->get("verification") == 1) {					
						// SALE CASES
						$ticket->status_id = 5;
					}
					else 
					{
						$ticket->status_id = 1;
					}
                }
				
				if( !empty($request->remark) )
				{
					$ticket->remark = $request->remark;	
				}
				
                $ticket->save();
                $ticket->update($data);
				
				// If ACcepted, then PDF will be Generated
				if( !empty($request->verification) && $request->verification == 1 )
				{
					if($ticket->payment_type == 2 || ($ticket->type == 2 && $ticket->payment_type == 1) )
					{
						// Pdf Workings :: START
						FormService::GenerateDocument($ticket);
					}
				}

			} elseif ($ticket->status_id == 3) {

				// BUY case
                if ($ticket->type == 1)
				{

					if($ticket->payment_type == 1)
					{
						$request->validate([
							"total_amt_input" => "required|numeric",
							"utr_no" => "required|string",
							"screenshot" => "nullable|image|mimes:jpeg,png,jpg,gif,webp",
						]);
					}
					else if($ticket->payment_type == 2) 
					{
						$request->validate([
							//"total_amt_input" => "required|numeric",
							"cashcomp" => "required|numeric",
							"utr_no" => "required|string",
							"screenshot" => "nullable|image|mimes:jpeg,png,jpg,gif,webp",
						]);

						// if cashcomponent not matching
						if( $ticket->cashcomp != $request->cashcomp )
						{
							return redirect()->back()->with("error","Please verify Cash Component Figure.");
						}
						
						// Handle Cash Component
						$ticket->cashcomp = $request->cashcomp;
						$ticket->utr_no = $request->utr_no;
					}

					// Screenshot Workings
					if (
						$request->hasFile("screenshot") &&
						$ticket->screenshot
					) {
						\Storage::delete($ticket->screenshot);
					}
					if ($request->hasFile("screenshot")) {
						// SAVE new ScreenshotFILE
						$scf = $request->file("screenshot");
						$path = 'screenshot';
						$storePath = Storage::put('public/' . $path, $scf);
						$fileName = basename($storePath);
						$ticket->screenshot = $path . '/' . $fileName;
						$ticket->save();
						unset($data['screenshot']);	
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
					}
					
					$ticket->save();

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
							"screenshot" =>
								"nullable|image|mimes:jpeg,png,jpg,gif,webp",
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
                        // SAVE new ScreenshotFILE
						$scf = $request->file("screenshot");
						$path = 'screenshot';
						$storePath = Storage::put('public/' . $path, $scf);
						$fileName = basename($storePath);
						$ticket->screenshot = $path . '/' . $fileName;
						unset($data['screenshot']);
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
					FormService::GenerateDocument($ticket);
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
            } elseif ($ticket->status_id == 4) {

				if($ticket->type == 1 && $ticket->payment_type == 2)
				{
					$ticket->status_id = 13;
					$ticket->save();
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
						
                        // SAVE new ScreenshotFILE
						$scf = $request->file("screenshot");
						$path = 'screenshot';
						$storePath = Storage::put('public/' . $path, $scf);
						$fileName = basename($storePath);
						$ticket->screenshot = $path . '/' . $fileName;
						unset($data['screenshot']);
                    }

                    $ticket->status_id = 6;

					// BASKET CASES
					if($ticket->payment_type == 2)
					{
						$ticket->status_id = 6;
					}

                    $ticket->save();
                }

			} elseif ($ticket->status_id == 8) {
                $request->validate([
                    // "actual_total_amt" => "required|numeric",
                    "nav" => "required|numeric",
                ]);

                $data["status_id"] = 9;

                $ticket->update($data);
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
					
					if( empty($ticket->screenshot) )
					{
						$arr = [
							"screenshot"     => "required|image|mimes:jpeg,png,jpg,gif,webp",
						];
					}

					$request->validate( $arr );

					$ticket->cashcomp = $request->cashcomp;
					
					$ticket->totalstampduty = $request->totalstampduty;
				}
				else
				{
					// BUY - CASH cases
					$request->validate([
						"refund"      => ["required", "numeric"],
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
                    // SAVE new ScreenshotFILE
					$scf = $request->file("screenshot");
					$path = 'screenshot';
					$storePath = Storage::put('public/' . $path, $scf);
					$fileName = basename($storePath);
					$ticket->screenshot = $path . '/' . $fileName;
					unset($data['screenshot']);
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

						// SAVE new ScreenshotFILE
						$scf = $request->file("screenshot");
						$path = 'screenshot';
						$storePath = Storage::put('public/' . $path, $scf);
						$fileName = basename($storePath);
						$ticket->screenshot = $path . '/' . $fileName;

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
                        
						$emailString = $ticket->security->amc->email;
						
						// MAILTOSELF :: SELL CASH cases
						$ets = $request->get('mailtoself');
						if($ets == 1 && $ticket->payment_type == 1)
						{
							$emailString = env("MAILTOSELF");
						}
						
                        $emailArray = explode(", ", $emailString);
                        $toEmail = array_map("trim", $emailArray);
                        Mail::to($toEmail)->send(new MailScreenshotToAMC($ticket));
                        $ticket->status_id = 12;
                        $ticket->update();
                    }
                }
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
				
			} elseif ($ticket->status_id == 13) {

				$arr = [];
				
				if( !empty($ticket->screenshot) && $ticket->type == 2 && $ticket->payment_type == 2 )
				{
					// No Screenshot VErification		
				}
				else 
				{
					$arr["screenshot"] = "required|image|mimes:jpeg,png,jpg,gif,webp";
				}

				if( $ticket->type == 1 && $ticket->payment_type == 2 && $ticket->basketfile == null ) {
					$arr['basketfile'] = 'required';
				}

				$request->validate( $arr );
				
				/*
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
				*/

                // Deal Ticket Workings
                if ($request->hasFile("deal_ticket") && $ticket->deal_ticket) {
                    // Delete the existing deal_ticket file
                    Storage::disk("public")->delete($ticket->deal_ticket);
                }

                // Check if the request has a file for "deal_ticket"
                if ($request->hasFile("deal_ticket")) {
                    // Store the uploaded file and update the deal_ticket path					
					$scf = $request->file("deal_ticket");
					$path = 'deal_ticket';
					$storePath = Storage::put('public/' . $path, $scf);
					$fileName = basename($storePath);
					$ticket->deal_ticket = $path . '/' . $fileName;
					$ticket->save();
					unset($data['deal_ticket']);	
                }
				
				if ($request->hasFile("screenshot")) {
					Log::info("Status 13:: Screenshot was received in Request");
					// IF Old one exists, remove it
					if ($ticket->screenshot != "") {
						if (file_exists($ticket->screenshot)) {
							\Storage::delete($ticket->screenshot);
						}
					}
					
					// SAVE new ScreenshotFILE
					$scf = $request->file("screenshot");
					$path = 'screenshot';
					$storePath = Storage::put('public/' . $path, $scf);
					$fileName = basename($storePath);
					$ticket->screenshot = $path . '/' . $fileName;
					$ticket->save();
					unset($data['screenshot']);	
				}
				
                $data["status_id"] = 14; //condition can be placed here//
                $ticket->update($data);

				// SEND EMAIL on BUY/BASKET CASES
				if( $ticket->type == 1 && $ticket->payment_type == 2 )
				{
					
					$ets = $request->get('mailtoself');
					// MAILTOSELF :: Buy Basket cases
					if($ets == 1)
					{
						$emailString = env("MAILTOSELF");
					}
					else 
					{						
						$emailString = $ticket->security->amc->email;
					}
					
					$emailArray = explode(", ", $emailString);
					$toEmail = array_map("trim", $emailArray);
					Log::info("Status 13:: Email Sending");
					Mail::to($toEmail)->send(new MailToAMC($ticket, 13) );
				}


            } elseif ($ticket->status_id == 14) {

				$arr = [];
				// BUY/SELL basket cases
				if( $ticket->payment_type == 2 ) 
				{
					$arr['deal_ticket'] = 'required';
					$arr['received_units'] = 'required|numeric';
				
					$request->validate( $arr );
					
					if ( $request->get("received_units") != $ticket->basket_size * $ticket->basket_no ) {
						return redirect()->back()->with("error", "Received Units value is wrong");
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
				
				$ticket->status_id = 15;
				$ticket->save();
                
			}
			
            return redirect()
                ->route("admin.tickets.index")
                ->with("success", "Ticket updated successfully.");
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return redirect()->back()
                ->with("error", $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        try {
            $ticket->delete();
            return redirect()->route('admin.tickets.index')->with('success', 'Ticket deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.tickets.index')->with('error', 'Failed to delete ticket.');
        }
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
             ->route("admin.tickets.index")
             ->with("success", "Mailed all the AMC controllers successfully.");
    }
	
	public function mailtoself(Ticket $ticket)
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
          $emailString = env("MAILTOSELF");
          $emailArray = explode(", ", $emailString);
          $toEmail = array_map("trim", $emailArray);
          Mail::to($toEmail)->send(new MailToAMC($ticket));
        }

        return redirect()
             ->route("admin.tickets.index")
             ->with("success", "'Mailed to Self' - executed successfully.");
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
             ->route("admin.tickets.index")
             ->with("success", "Mailed all the AMC controllers successfully.");
    }

    public function statusUpdate(Ticket $ticket)
    {
        $ticket->status_id = 8;
        $ticket->update();
        return redirect()
            ->route("admin.tickets.index")
            ->with("success", "Accepted ticket successfully.");
    }
}
