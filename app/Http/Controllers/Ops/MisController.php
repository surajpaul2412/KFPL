<?php

namespace App\Http\Controllers\Ops;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Carbon\Carbon;
use DB;

class MisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view(
            "ops.mis.index"
        );
    }

    public function sendBuyDataToCSV($rows)
    {
        $headers = [
                    'Ticket ID', 'Date', 'AMC Name', 'Symbol', 'ISIN', 'No Of baskets', 'Qty',
                    'Deal Accept', 'Fund Remitted', 'Appl Sent', 'Order Recd', 'Deal Recd', 
                    'Amt Recd', 'Units Recd'
                ];

        $callback = function() use ($rows, $headers) 
                    {
                    
                        $file = fopen('php://output', 'w');
                                    
                        // Write the headers
                        fputcsv($file, $headers);

                        // Define Variables 
                        $totalBasket = 0;
                        $totalQty = 0;

                        // Iterate over each row and write to the CSV
                        foreach ($rows as $row) {

                            $totalBasket += doubleval($row->basket_no);
                            $totalQty += doubleval($row->basket_no * $row->basket_size);

                            fputcsv($file, [
                                $row->id, 
                                date("d-M-Y", strtotime($row->created_at)),
                                $row->security->amc->name,
                                $row->security->symbol,
                                $row->security->isin,
                                $row->basket_no ,
                                $row->basket_no * $row->basket_size ,
                                ($row->status_id > 2 ? 'Yes' : 'No'), 
                                ($row->utr_no ? 'Yes' : 'No'),
                                ($row->status_id > 6 ? 'Yes' : 'No'),  
                                ($row->status_id > 7 ? 'Yes' : 'No'), 
                                ($row->status_id > 9 ? 'Yes' : 'No'),
                                ($row->status_id > 11 ? 'Yes' : 'No'),
                                ($row->status_id > 13 ? 'Yes' : 'No')
                            ]);
                        }

                        fputcsv($file, [
                                'Sub Total',
                                '',
                                '',
                                '',
                                '',
                                number_format($totalBasket, 2),
                                number_format($totalQty, 2),
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                ''
                        ]);    

                        fclose($file);
                    };

        $responseHeaders = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="opsTickets.csv"',
            'Cache-Control' => 'no-store, no-cache',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        return response()->stream($callback, 200, $responseHeaders);  
    }

    public function sendSaleDataToCSV($rows)
    {

        $headers = [
            'Ticket ID', 'Date', 'AMC Name', 'Symbol',
            'ISIN', 'No Of baskets',  'Qty',
            'Units Sent', 'Appl Sent', 'Order Recd',
            'Deal Recd', 'Unit Trf', 'Amt Recd'
        ];

        $callback = function() use ($rows, $headers) 
                    {
                    
                        $file = fopen('php://output', 'w');
                                    
                        // Write the headers
                        fputcsv($file, $headers);

                        // Define Variables 
                        $totalBasket = 0;
                        $totalQty = 0;

                        // Iterate over each row and write to the CSV
                        foreach ($rows as $row) {

                            $totalBasket += doubleval($row->basket_no);
                            $totalQty += doubleval($row->basket_no * $row->basket_size);

                            fputcsv($file, [
                                $row->id,
                                date("d-M-Y", strtotime($row->created_at)),
                                $row->security->amc->name,
                                $row->security->symbol,
                                $row->security->isin,
                                $row->basket_no,
                                $row->basket_no * $row->basket_size,
                                '',
                                ($row->status_id > 6 ? 'Yes' : 'No'),
                                ($row->status_id > 7 ? 'Yes' : 'No'),
                                ($row->status_id > 9 ? 'Yes' : 'No'),                                    
                                ($row->status_id > 13 ? 'Yes' : 'No'),
                                ($row->status_id > 12 ? 'Yes' : 'No')
                            ]);
                        }

                        fputcsv($file, [
                                'Sub Total',
                                '',
                                '',
                                '',
                                '',
                                number_format($totalBasket, 2),
                                number_format($totalQty, 2),
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                ''
                        ]);    

                        fclose($file);
                    };

        $responseHeaders = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="opsTickets.csv"',
            'Cache-Control' => 'no-store, no-cache',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        return response()->stream($callback, 200, $responseHeaders);  
    }

    // EXPORT MIS DATA to CSV
    public function exportMisDataToCSV(Request $request)
    {
        
        $setType = $request->input('datamode');
        $currentDate = Carbon::now();
        $startOf48HoursAgo = $currentDate->copy()->subHours(48);

        if ($setType == 1) { // BUY case
            $data = Ticket::where('type', $setType)
                ->where(function ($query) use ($startOf48HoursAgo) {
                    // Show records within the last 48 hours or status_id <= 13
                    $query->where('status_id', '<', 13)
                          ->orWhereBetween('created_at', [$startOf48HoursAgo, Carbon::now()]);
                })
                ->with('security', 'security.amc')
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->sendBuyDataToCSV($data);    
        } else { // SELL case
            $data = Ticket::where('type', $setType)
                ->where(function ($query) use ($startOf48HoursAgo) {
                    // Show records within the last 48 hours or status_id <= 13
                    $query->where('status_id', '<', 13)
                          ->orWhereBetween('created_at', [$startOf48HoursAgo, Carbon::now()]);
                })
                ->with('security', 'security.amc')
                ->orderBy('created_at', 'desc')
                ->get();
            return $this->sendSaleDataToCSV($data);        
        }

          
    }

    // GET MIS DATA
    public function getMisData(Request $request)
    {
        $setType = $request->input('sel_role_id');
        $currentDate = Carbon::now();
        $startOf48HoursAgo = $currentDate->copy()->subHours(48);

        if ($setType == 1) { // BUY case
            $data = Ticket::where('type', $setType)
                ->where(function ($query) use ($startOf48HoursAgo) {
                    // Show records within the last 48 hours or status_id <= 13
                    $query->where('status_id', '<', 13)
                          ->orWhereBetween('created_at', [$startOf48HoursAgo, Carbon::now()]);
                })
                ->with('security', 'security.amc')
				->orderBy('created_at', 'desc')
                ->get();
        } else { // SELL case
            $data = Ticket::where('type', $setType)
                ->where(function ($query) use ($startOf48HoursAgo) {
                    // Show records within the last 48 hours or status_id <= 13
                    $query->where('status_id', '<', 13)
                          ->orWhereBetween('created_at', [$startOf48HoursAgo, Carbon::now()]);
                })
                ->with('security', 'security.amc')
				->orderBy('created_at', 'desc')
                ->get();
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
