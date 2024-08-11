<?php

namespace App\Http\Controllers\Trader;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Role;
use App\Models\User;
use App\Models\QuickTicket;
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
        $sel_from_date = isset($request["sel_from_date"])
          ? $request["sel_from_date"]
          : "";
        $sel_to_date = isset($request["sel_to_date"])
          ? $request["sel_to_date"]
          : "";
        $sel_query = isset($request["sel_query"]) ? $request["sel_query"] : "";

        $type = isset($request["type"]) ? $request["type"] : "";

        // GET ALL ROLES
        $roles = Role::where('id', '<>', 1)->get();

        DB::enableQueryLog();

        // Initialize the query for QuickTicket
        $ticketQuery = QuickTicket::with('security');

        // Apply date filters
        if ($sel_from_date) {
            $ticketQuery->where('updated_at', '>=', $sel_from_date . ' 00:00:00');
        }
        if ($sel_to_date) {
            $ticketQuery->where('updated_at', '<=', $sel_to_date . ' 23:59:59');
        }

        // Apply search query filter | Chandan's requirement
        // if ($sel_query) {
        //     $ticketQuery->whereHas('security', function ($query) use ($sel_query) {
        //         $query->where('security.name', 'LIKE', "%{$sel_query}%")
        //               ->orWhere('security.symbol', 'LIKE', "%{$sel_query}%")
        //               ->orWhere('security.isin', 'LIKE', "%{$sel_query}%");
        //     });
        // }

        // Apply type filter
        if ($type) {
            $ticketQuery->where('type', $type);
        }

        // Apply user-related filters
        $tickets = $ticketQuery->where('trader_id', Auth::user()->id)
                               ->orWhere('trader_id', 0)
                               ->orderBy('updated_at', 'desc')
                               ->paginate(10);

        $sql = DB::getQueryLog();

        // Pass the data to the view
        return view('trader.quick_tickets.index', compact(
            'tickets',
            'roles',
            'sel_from_date',
            'sel_to_date',
            'sel_query',
            'type'
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
        $quickTicket = QuickTicket::findOrFail($id);

        // Create a new Ticket instance
        $ticket = new Ticket();
        $ticket->fill([
            'user_id' => ( $quickTicket->trader_id != 0 ? $quickTicket->trader_id : Auth::user()->id ),
            'security_id' => $quickTicket->security_id,
            'type' => $quickTicket->type,
            'payment_type' => $quickTicket->payment_type,
            'basket_no' => $quickTicket->basket_no,
            'basket_size' => $quickTicket->basket_size,
            'rate' => $quickTicket->security->price ?? 0,
            'security_price' => 0,
            'markup_percentage' => $quickTicket->security->markup_percentage ?? 0,
            'actual_total_amt' => $quickTicket->actual_total_amt,
            'nav' => $quickTicket->nav,
            'status_id' => 1
        ]);

        // Save the new Ticket instance
        $ticket->save();

        // Delete the QuickTicket
        $quickTicket->delete();

        return redirect()->route('trader.quick_tickets.index')->with('success', 'Quick Ticket converted successfully.');
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
