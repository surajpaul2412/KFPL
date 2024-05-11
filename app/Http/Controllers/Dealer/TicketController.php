<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Role;
use App\Models\User;
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

		$tickets = $ticketQuery->whereIn('status_id', [7, 8])
					 ->orderBy('updated_at', 'desc')
					 ->paginate(10);

         return view('dealer.tickets.index', compact(
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
        return view('dealer.tickets.show', compact('ticket'));
    }

    public function statusUpdate(Ticket $ticket) {
        $ticket->status_id = 8;
        $ticket->update();
        return redirect()->route('dealer.tickets.index')->with('success', 'Accepted ticket successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        return view('dealer.tickets.edit', compact('ticket'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $data = $request->all();

        if ($ticket->status_id == 8) {
            $request->validate([
                'actual_total_amt' => 'required|numeric',
                'nav' => 'required|numeric'
            ]);

            $data['status_id'] = 9;
        }

        $ticket->update($data);
        return redirect()->route('dealer.tickets.index')->with('success', 'Ticket updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
