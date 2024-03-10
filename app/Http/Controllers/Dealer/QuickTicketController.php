<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Security;
use Illuminate\Support\Facades\DB;

class QuickTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        dd("Quickly updating");
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
            "dealer.quick_tickets.index",
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
