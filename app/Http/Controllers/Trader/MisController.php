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
                        COUNT(security_id) as total_quick_clubbed,
                        SUM(basket_no) as total_quick_basket_no, 
                        SUM(nav) as total_quick_nav, 
                        SUM(actual_total_amt) as total_quick_amt, 
                        SUM(basket_no * basket_size) as total_quick_units
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

            // Fetch data from Ticket
            $ticketData = Ticket::selectRaw('
                        security_id, 
                        COUNT(security_id) as total_ticket_clubbed,
                        SUM(basket_no) as total_ticket_basket_no, 
                        SUM(nav) as total_ticket_nav, 
                        SUM(actual_total_amt) as total_ticket_actual_amt, 
                        SUM(basket_no * basket_size) as total_ticket_units
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

        } else { // SELL case
            // Fetch data from QuickTicket
            $quickTicketData = QuickTicket::selectRaw('
                        security_id, 
                        COUNT(security_id) as total_quick_clubbed,
                        SUM(basket_no) as total_quick_basket_no, 
                        SUM(nav) as total_quick_nav, 
                        SUM(actual_total_amt) as total_quick_amt, 
                        SUM(basket_no * basket_size) as total_quick_units
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

            // Fetch data from Ticket
            $ticketData = Ticket::selectRaw('
                        security_id, 
                        COUNT(security_id) as total_ticket_clubbed,
                        SUM(basket_no) as total_ticket_basket_no, 
                        SUM(nav) as total_ticket_nav, 
                        SUM(actual_total_amt) as total_ticket_actual_amt, 
                        SUM(basket_no * basket_size) as total_ticket_units
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
        }

        // Combine the data from QuickTicket and Ticket
        $combinedData = $quickTicketData->concat($ticketData)->groupBy('security_id')->map(function ($group) {
            $firstItem = $group->first();
            $combinedItem = $firstItem->replicate();
            $combinedItem->total_quick_clubbed = $group->sum('total_quick_clubbed');
            $combinedItem->total_quick_basket_no = $group->sum('total_quick_basket_no');
            $combinedItem->total_quick_nav = $group->sum('total_quick_nav');
            $combinedItem->total_quick_amt = $group->sum('total_quick_amt');
            $combinedItem->total_quick_units = $group->sum('total_quick_units');
            $combinedItem->total_ticket_clubbed = $group->sum('total_ticket_clubbed');
            $combinedItem->total_ticket_basket_no = $group->sum('total_ticket_basket_no');
            $combinedItem->total_ticket_nav = $group->sum('total_ticket_nav');
            $combinedItem->total_ticket_actual_amt = $group->sum('total_ticket_actual_amt');
            $combinedItem->total_ticket_units = $group->sum('total_ticket_units');
            return $combinedItem;
        });

        return response()->json($combinedData->values()->all());
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
