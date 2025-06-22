<?php

namespace App\Http\Controllers\Trader;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Role;
use App\Models\User;
use App\Models\Security;
use Exception;
use Validator;
use Auth;
use App\Mail\MailToAMC;
use App\Mail\MailScreenshotToAMC;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

      $tickets = $ticketQuery->whereUserId(Auth::user()->id)
                             ->where('is_active', '1')
                             ->orderBy("updated_at", "desc")
                             ->paginate(10);

      //$sql = DB::getQueryLog();
      //dd($sql);

      return view(
          "trader.tickets.index",
          compact(
              "tickets",
              "roles",
              "sel_from_date",
              "sel_to_date",
              "sel_query"
          )
      );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //$securities = Security::whereStatus(1)->orderBy("amc_id", "asc")->get();
        $securities = Security::with('amc')
                        ->where('status', 1)
                        ->whereHas('amc', function ($query) {
                            $query->where('status', 1);
                        })
                        ->orderBy('amc_id', 'asc')
                        ->get();

        return view('trader.tickets.create', compact('securities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            "security_id"    => "required|exists:securities,id",
            "type"           => "required|integer|in:1,2",
            "payment_type"   => "required|integer|in:1,2,3",
            "basket_no"      => "required|integer",
            "basket_size"    => "required|integer",
            "rate"           => "required|numeric",
            "security_price"    => "required|numeric",
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
        return redirect()->route('trader.tickets.index')->with('success', 'Ticket created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        if($ticket->is_active == 0) abort(404); // can Not EDIT Hidden Items
        return view('trader.tickets.show', compact('ticket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        if($ticket->is_active == 0) abort(404); // can Not EDIT Hidden Items
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
