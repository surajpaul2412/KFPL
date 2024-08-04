<?php

namespace App\Http\Controllers\Trader;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\QuickTicket;
use Carbon\Carbon;
use Auth;

class MisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view(
            "trader.mis.index"
        );
    }

public function getMisData(Request $request)
{
    $setType = $request->input('sel_role_id');
    $currentDate = Carbon::today();
    $startOfDay = $currentDate->copy()->startOfDay();
    $endOfDay = $currentDate->copy()->endOfDay();
    $userId = Auth::user()->id; // Get the authenticated user's ID

    if ($setType == 1) { // BUY case
        // Fetch data from QuickTicket
        $quickTicketData = QuickTicket::selectRaw('
                    security_id, 
                    COUNT(security_id) as total_clubbed,
                    SUM(basket_no) as total_basket_no, 
                    SUM(nav) as total_nav, 
                    SUM(actual_total_amt) as total_amt, 
                    SUM(basket_no * basket_size) as total_units
                ')
                ->where('type', $setType)
                ->where(function ($query) use ($userId) {
                    $query->where('trader_id', $userId)
                          ->orWhere('trader_id', 0);
                })
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->groupBy('security_id')
                ->with('security', 'security.amc') // Load relationships
                ->get()
                ->map(function ($item) {
                    $item->source = 'quick_ticket';
                    return $item;
                });

        $ticketData = Ticket::selectRaw('
                    security_id, 
                    COUNT(security_id) as total_clubbed,
                    SUM(basket_no) as total_basket_no, 
                    SUM(nav) as total_nav, 
                    SUM(actual_total_amt) as total_actual_amt, 
                    SUM(basket_no * basket_size) as total_units
                ')
                ->whereUserId($userId)
                ->where('type', $setType)
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->groupBy('security_id')
                ->with('security', 'security.amc') // Load relationships
                ->get()
                ->map(function ($item) {
                    $item->source = 'ticket';
                    return $item;
                });

        // Combine the data from QuickTicket and Ticket
        $data = $quickTicketData->concat($ticketData);

    } else { // SELL case
        // Fetch data from QuickTicket
        $quickTicketData = QuickTicket::selectRaw('
                    security_id, 
                    COUNT(security_id) as total_clubbed,
                    SUM(basket_no) as total_basket_no, 
                    SUM(nav) as total_nav, 
                    SUM(actual_total_amt) as total_amt, 
                    SUM(basket_no * basket_size) as total_units
                ')
                ->where('type', $setType)
                ->where(function ($query) use ($userId) {
                    $query->where('trader_id', $userId)
                          ->orWhere('trader_id', 0);
                })
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->groupBy('security_id')
                ->with('security', 'security.amc') // Load relationships
                ->get()
                ->map(function ($item) {
                    $item->source = 'quick_ticket';
                    return $item;
                });

        $ticketData = Ticket::selectRaw('
                    security_id, 
                    COUNT(security_id) as total_clubbed,
                    SUM(basket_no) as total_basket_no, 
                    SUM(nav) as total_nav, 
                    SUM(actual_total_amt) as total_actual_amt, 
                    SUM(basket_no * basket_size) as total_units
                ')
                ->where('user_id', $userId)
                ->where('type', $setType)
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->groupBy('security_id')
                ->with('security', 'security.amc') // Load relationships
                ->get()
                ->map(function ($item) {
                    $item->source = 'ticket';
                    return $item;
                });

        // Combine the data from QuickTicket and Ticket
        $data = $quickTicketData->concat($ticketData);
    }

    return response()->json($data);
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
