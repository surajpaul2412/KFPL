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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

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

        // GET ALL ROLES
        $roles = Role::where("id", "<>", 1)->get();

        DB::enableQueryLog();

        $ticketQuery = Ticket::with("security");

        if ($sel_status_id != "") {
            $ticketQuery->where("status_id", $sel_status_id);
        }
        if ($sel_from_date != "") {
            $ticketQuery->where("updated_at", ">=", $sel_from_date);
        }
        if ($sel_to_date != "") {
            $ticketQuery->where("updated_at", "<=", $sel_to_date);
        }
        if ($sel_query != "") {
            $ticketQuery->whereHas("security", function (Builder $query) use (
                $sel_query
            ) {
                $query
                    ->where("tickets.id", "LIKE", "%{$sel_query}%")
                    ->orWhere("securities.name", "LIKE", "%{$sel_query}%")
                    ->orWhere("securities.symbol", "LIKE", "%{$sel_query}%")
                    ->orWhere("securities.isin", "LIKE", "%{$sel_query}%");
            });
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

        $tickets = $ticketQuery->orderBy("created_at", "desc")->paginate(10);
        $sql = DB::getQueryLog();
        // dd($sql);
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
        $securities = Security::whereStatus(1)->get();
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
            // FIND TICKET
            $ticket = Ticket::findOrFail($id);
            $data = $request->all();
            // $data['status_id'] = 2;

            // SET STATUS as per OTHER PARAMETERS
            if ($ticket->status_id == 1) {
                $validatedData = $request->validate([
                    "security_id" => "required|exists:securities,id",
                    "type" => "required|integer|in:1,2",
                    "payment_type" => "required|integer|in:1,2,3",
                    "basket_no" => "required|integer",
                    "rate" => "required|numeric",
                    "total_amt" => "required|numeric",
                ]);
                $data["status_id"] = 2;

                $ticket->update($data);
            } elseif ($ticket->status_id == 2) {
                if ($ticket->type == 1) {
                    // BUY cases
                    $request->validate([
                        "verification" => "required|in:1,2",
                        "rate" => "nullable|numeric",
                        "remark" => "nullable",
                    ]);

                    if ($request->get("verification") == 1) {
                        $ticket->status_id = 3;
                    } else {
                        $ticket->status_id = 1;
                    }
                } else {
                    // SALE CASES
                    $ticket->status_id = 5;
                }
                // Save Ticket
                $ticket->save();
            } elseif ($ticket->status_id == 3) {
                // BUY case
                if ($ticket->type == 1) {
                    $request->validate([
                        "total_amt" => "required|numeric",
                        "utr_no" => "required|string",
                        "screenshot" =>
                            "nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048",
                    ]);

                    if ($ticket->total_amt == $request->get("total_amt")) {
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

                        $ticket->utr_no = $request->get("utr_no");
                        if ($ticket->payment_type == 1) {
                            $ticket->status_id = 6;
                        }

                        //Save Ticket
                        $ticket->save();

                        // Update Ticket
                        $ticket->update($request->except("screenshot"));
                    } else {
                        return redirect()
                            ->back()
                            ->with(
                                "error",
                                "Please verify your entered amount."
                            );
                    }
                } else {
                    // SELL CASE
                    $request->validate([
                        "screenshot" =>
                            "nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048",
                    ]);

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

                    if ($ticket->payment_type == 1) {
                        $ticket->status_id = 6;
                    }

                    //Save Ticket
                    $ticket->save();
                }
                // Pdf Workings :: START
                FormService::GenerateDocument($ticket);
                // Pdf Workings :: END
            } elseif ($ticket->status_id == 5) {
                // SELL Cases
                if ($ticket->type == 2) {
                    $request->validate([
                        "screenshot" =>
                            "nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048",
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
                    $ticket->save();
                }
            } elseif ($ticket->status_id == 8) {
                $request->validate([
                    "actual_total_amt" => "required|numeric",
                    "nav" => "required|numeric",
                ]);

                $data["status_id"] = 9;

                $ticket->update($data);
            } elseif ($ticket->status_id == 9) {
                $request->validate([
                    "refund" => "required|numeric",
                    "deal_ticket" => "nullable",
                    "screenshot" =>
                        "nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048",
                ]);

                // Deal Ticket Workings
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
            } elseif ($ticket->status_id == 10) {
                if ($ticket->type == 2) {
                    $request->validate([
                        "screenshot" =>
                            "nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048",
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
                    if (
                        $request->hasFile("deal_ticket") &&
                        $ticket->deal_ticket
                    ) {
                        Storage::delete($ticket->deal_ticket);
                    }

                    if ($request->hasFile("deal_ticket")) {
                        $imagePath = $request
                            ->file("deal_ticket")
                            ->store("deal_ticket", "public");
                        $ticket->deal_ticket = $imagePath;
                    }

                    $ticket->status_id = 12; // SELL CASE
                    $ticket->save();
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
                    if (
                        $request->hasFile("deal_ticket") &&
                        $ticket->deal_ticket
                    ) {
                        Storage::delete($ticket->deal_ticket);
                    }

                    if ($request->hasFile("deal_ticket")) {
                        $imagePath = $request
                            ->file("deal_ticket")
                            ->store("deal_ticket", "public");
                        $ticket->deal_ticket = $imagePath;
                    }

                    $ticket->dispute = $request->get("dispute");
                } else {
                    $ticket->dispute = $request->get("dispute");
                }

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
            }

            $ticket->update($data);

            return redirect()
                ->route("admin.tickets.index")
                ->with("success", "Ticket updated successfully.");
        } catch (\Exception $e) {
            dd($e->getMessage());
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
        $emailString = $ticket->security->amc->email ?? null;
        $emailArray = explode(", ", $emailString);
        $toEmail = array_map("trim", $emailArray);

        Mail::to($toEmail)->send(new MailToAMC($ticket));

        $ticket->status_id = 7;
        $ticket->update();
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
