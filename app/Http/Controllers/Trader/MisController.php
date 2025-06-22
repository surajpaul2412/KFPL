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

    // EXPORT TRADER BUY Tickets
    public function exportTradersBuyCasesToCSV($rows)
    {
        $headers = [
            'AMC Name', 'Symbol', 'Quick Ticket', 'NAV', 'Quick Ticket Value',
            'Ticket Raised', 'Pending Tickets',  'Amount Sent', 'Total Units',
        ];

        $callback = function() use ($rows, $headers) {
                    $file = fopen('php://output', 'w');
                    
                    // Write the headers
                    fputcsv($file, $headers);

                    // Declare VAriables 
                    $totalQuickTicket = 0;
                    $totalQuickTicketVal = 0;
                    $totalQuickTicketUnits = 0;
                    $totalTicket = 0;
                    $amtSent = 0;

                    // Iterate over each row and write to the CSV
                    foreach ($rows as $row) {

                        $totalQuickTicket += doubleval($row->total_quick_basket_no);
                        $totalQuickTicketVal += doubleval($row->total_quick_amt);
                        $totalQuickTicketUnits += doubleval($row->total_quick_units + $row->total_ticket_units);
                        $totalTicket += doubleval($row->total_ticket_basket_no);
                        $amtSent += doubleval($row->total_ticket_actual_amt);

                        fputcsv($file, [
                            ($row->security ? $row->security->amc->name : '-'),
                            ($row->security ? $row->security->symbol : '-'),
                            ($row->total_quick_basket_no == 0 ? '-' : $row->total_quick_basket_no),
                            ($row->total_quick_nav == 0 ? '-' : ($row->total_quick_nav / $row->total_quick_clubbed)),
                            ($row->total_quick_amt == 0 ? '-' : $row->total_quick_amt),
                            ($row->total_ticket_basket_no == 0 ? '-' : $row->total_ticket_basket_no),
                            ($row->total_quick_basket_no - $row->total_ticket_basket_no),
                            ($row->total_ticket_actual_amt == 0 ? '-' : $row->total_ticket_actual_amt) ,
                            ($row->total_quick_units + $row->total_ticket_units),
                        ]);
                    }

                    // Write the bottom ROW
                    fputcsv($file, [
                            'Total',
                            '',
                            $totalQuickTicket,
                            '',
                            number_format($totalQuickTicketVal, 2),
                            $totalTicket,
                            '',
                            number_format($amtSent, 2),
                            number_format($totalQuickTicketUnits, 2)
                    ]);

                    fclose($file);
                };

        $responseHeaders = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="tradersBuyTickets.csv"',
            'Cache-Control' => 'no-store, no-cache',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        return response()->stream($callback, 200, $responseHeaders);    
    }


    // EXPORT TRADER BUY Tickets
    public function exportTradersSaleCasesToCSV($rows)
    {
        $headers = [
            'AMC Name', 'Symbol', 'Quick Ticket', 'NAV', 'Quick Ticket Value', 
            'Ticket Raised', 'Pending Tickets', 'Amount Sent', 'Total Units',
        ];

        $callback = function() use ($rows, $headers) {
                    $file = fopen('php://output', 'w');
                    
                    // Write the headers
                    fputcsv($file, $headers);

                    // Declare VAriables 
                    $totalQuickTicket = 0;
                    $totalQuickTicketVal = 0;
                    $totalQuickTicketUnits = 0;
                    $totalTicket = 0;
                    $amtSent = 0;

                    // Iterate over each row and write to the CSV
                    foreach ($rows as $row) {

                        $totalQuickTicket += doubleval($row->total_quick_basket_no);
                        $totalQuickTicketVal += doubleval($row->total_quick_amt);
                        $totalQuickTicketUnits += doubleval($row->total_quick_units + $row->total_ticket_units);
                        $totalTicket += doubleval($row->total_ticket_basket_no);
                        $amtSent += doubleval($row->total_ticket_actual_amt);

                        fputcsv($file, [
                            ($row->security ? $row->security->amc->name : '-'),
                            ($row->security ? $row->security->symbol : '-'),
                            ($row->total_quick_basket_no == 0 ? '-' : $row->total_quick_basket_no),
                            ($row->total_quick_nav == 0 ? '-' : ($row->total_quick_nav / $row->total_quick_clubbed)),
                            ($row->total_quick_amt == 0 ? '-' : $row->total_quick_amt),
                            ($row->total_ticket_basket_no == 0 ? '-' : $row->total_ticket_basket_no),
                            ($row->total_quick_basket_no - $row->total_ticket_basket_no),
                            ($row->total_ticket_actual_amt == 0 ? '-' : $row->total_ticket_actual_amt) ,
                            ($row->total_quick_units + $row->total_ticket_units),
                        ]);
                    }

                    // Write the bottom ROW
                    fputcsv($file, [
                            'Total',
                            '',
                            $totalQuickTicket,
                            '',
                            number_format($totalQuickTicketVal, 2),
                            $totalTicket,
                            '',
                            number_format($amtSent, 2),
                            number_format($totalQuickTicketUnits, 2)
                    ]);

                    fclose($file);
                };

        $responseHeaders = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="tradersSaleTickets.csv"',
            'Cache-Control' => 'no-store, no-cache',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        return response()->stream($callback, 200, $responseHeaders);    
    }
    // Export MIS Data to CSV
    public function exportMisDataToCSV(Request $request)
    {
        
        $setType = $request->input('datamode');
        $currentDate = Carbon::today();
        $startOfDay = $currentDate->copy()->startOfDay();
        $endOfDay = $currentDate->copy()->endOfDay();
        $userId = Auth::user()->id; // Get the authenticated user's ID

        if ($setType == 1) { // BUY case
            // Fetch data from QuickTicket
            $quickTicketData = QuickTicket::selectRaw('
                        security_id, 
                        trader_id as user_id, 
                        COUNT(security_id) as total_quick_clubbed,
                        SUM(basket_no) as total_quick_basket_no, 
                        SUM(nav) as total_quick_nav, 
                        SUM(actual_total_amt) as total_quick_amt, 
                        SUM(basket_no * basket_size) as total_quick_units
                    ')
                    ->where('type', $setType)
                    // ->where(function ($query) use ($userId) {
                    //     $query->where('trader_id', $userId)
                    //           ->orWhere('trader_id', 0);
                    // })
                    ->whereBetween('created_at', [$startOfDay, $endOfDay])
                    ->groupBy('security_id','trader_id')
                    ->with('security', 'security.amc') // Load relationships
                    ->get()
                    ->map(function ($item) {
                        $item->source = 'quick_ticket';
                        return $item;
                    });

            // Fetch data from Ticket
            $ticketData = Ticket::selectRaw('
                        security_id, 
                        user_id, 
                        COUNT(security_id) as total_ticket_clubbed,
                        SUM(basket_no) as total_ticket_basket_no, 
                        SUM(nav) as total_ticket_nav, 
                        SUM(actual_total_amt) as total_ticket_actual_amt, 
                        SUM(basket_no * basket_size) as total_ticket_units
                    ')
                    // ->whereUserId($userId)
                    ->where('type', $setType)
                    ->where('is_active', 1)
                    ->whereBetween('created_at', [$startOfDay, $endOfDay])
                    ->groupBy('security_id','user_id')
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
                        trader_id as user_id, 
                        COUNT(security_id) as total_quick_clubbed,
                        SUM(basket_no) as total_quick_basket_no, 
                        SUM(nav) as total_quick_nav, 
                        SUM(actual_total_amt) as total_quick_amt, 
                        SUM(basket_no * basket_size) as total_quick_units
                    ')
                    ->where('type', $setType)
                    // ->where(function ($query) use ($userId) {
                    //     $query->where('trader_id', $userId)
                    //           ->orWhere('trader_id', 0);
                    // })
                    ->whereBetween('created_at', [$startOfDay, $endOfDay])
                    ->groupBy('security_id','trader_id')
                    ->with('security', 'security.amc') // Load relationships
                    ->get()
                    ->map(function ($item) {
                        $item->source = 'quick_ticket';
                        return $item;
                    });

            // Fetch data from Ticket
            $ticketData = Ticket::selectRaw('
                        security_id, 
                        user_id, 
                        COUNT(security_id) as total_ticket_clubbed,
                        SUM(basket_no) as total_ticket_basket_no, 
                        SUM(nav) as total_ticket_nav, 
                        SUM(actual_total_amt) as total_ticket_actual_amt, 
                        SUM(basket_no * basket_size) as total_ticket_units
                    ')
                    // ->where('user_id', $userId)
                    ->where('type', $setType)
                    ->where('is_active', 1)
                    ->whereBetween('created_at', [$startOfDay, $endOfDay])
                    ->groupBy('security_id','user_id')
                    ->with('security', 'security.amc') // Load relationships
                    ->get()
                    ->map(function ($item) {
                        $item->source = 'ticket';
                        return $item;
                    });
        }

        // Combine the data from QuickTicket and Ticket
        $combinedData = $quickTicketData->concat($ticketData)->groupBy('security_id','user_id')->map(function ($group) {
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
        
        if ($setType == 1) { // BUY case
            return $this->exportTradersBuyCasesToCSV($combinedData);
        }else{
            return $this->exportTradersSaleCasesToCSV($combinedData);
        }

        //return response()->json($combinedData->values()->all());
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
                        trader_id as user_id, 
                        COUNT(security_id) as total_quick_clubbed,
                        SUM(basket_no) as total_quick_basket_no, 
                        SUM(nav) as total_quick_nav, 
                        SUM(actual_total_amt) as total_quick_amt, 
                        SUM(basket_no * basket_size) as total_quick_units
                    ')
                    ->where('type', $setType)
                    // ->where(function ($query) use ($userId) {
                    //     $query->where('trader_id', $userId)
                    //           ->orWhere('trader_id', 0);
                    // })
                    ->whereBetween('created_at', [$startOfDay, $endOfDay])
                    ->groupBy('security_id','trader_id')
                    ->with('security', 'security.amc') // Load relationships
                    ->get()
                    ->map(function ($item) {
                        $item->source = 'quick_ticket';
                        return $item;
                    });

            // Fetch data from Ticket
            $ticketData = Ticket::selectRaw('
                        security_id, 
                        user_id, 
                        COUNT(security_id) as total_ticket_clubbed,
                        SUM(basket_no) as total_ticket_basket_no, 
                        SUM(nav) as total_ticket_nav, 
                        SUM(actual_total_amt) as total_ticket_actual_amt, 
                        SUM(basket_no * basket_size) as total_ticket_units
                    ')
                    // ->whereUserId($userId)
                    ->where('type', $setType)
                    ->where('is_active', 1)
                    ->whereBetween('created_at', [$startOfDay, $endOfDay])
                    ->groupBy('security_id','user_id')
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
                        trader_id as user_id, 
                        COUNT(security_id) as total_quick_clubbed,
                        SUM(basket_no) as total_quick_basket_no, 
                        SUM(nav) as total_quick_nav, 
                        SUM(actual_total_amt) as total_quick_amt, 
                        SUM(basket_no * basket_size) as total_quick_units
                    ')
                    ->where('type', $setType)
                    // ->where(function ($query) use ($userId) {
                    //     $query->where('trader_id', $userId)
                    //           ->orWhere('trader_id', 0);
                    // })
                    ->whereBetween('created_at', [$startOfDay, $endOfDay])
                    ->groupBy('security_id','trader_id')
                    ->with('security', 'security.amc') // Load relationships
                    ->get()
                    ->map(function ($item) {
                        $item->source = 'quick_ticket';
                        return $item;
                    });

            // Fetch data from Ticket
            $ticketData = Ticket::selectRaw('
                        security_id, 
                        user_id, 
                        COUNT(security_id) as total_ticket_clubbed,
                        SUM(basket_no) as total_ticket_basket_no, 
                        SUM(nav) as total_ticket_nav, 
                        SUM(actual_total_amt) as total_ticket_actual_amt, 
                        SUM(basket_no * basket_size) as total_ticket_units
                    ')
                    // ->where('user_id', $userId)
                    ->where('type', $setType)
                    ->where('is_active', 1)
                    ->whereBetween('created_at', [$startOfDay, $endOfDay])
                    ->groupBy('security_id','user_id')
                    ->with('security', 'security.amc') // Load relationships
                    ->get()
                    ->map(function ($item) {
                        $item->source = 'ticket';
                        return $item;
                    });
        }

        // Combine the data from QuickTicket and Ticket
        $combinedData = $quickTicketData->concat($ticketData)->groupBy('security_id','user_id')->map(function ($group) {
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
