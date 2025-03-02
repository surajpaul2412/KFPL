<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\User;
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
            "admin.mis.index"
        );
    }

    public function exportOpsBuyCasesToCSV($rows)
	{
		$headers = [
				    'Ticket ID', 'Date', 'AMC Name', 'Symbol', 'ISIN', 'No Of baskets',  'Qty',
				    'Deal Accept',  'Fund Remitted', 'Appl Sent',  'Order Recd',  'Deal Recd',
				    'Amt Recd',   'Units Recd',
				];

		$callback = function() use ($rows, $headers) {
				    $file = fopen('php://output', 'w');
				    
				    // Write the headers
				    fputcsv($file, $headers);

				    // Iterate over each row and write to the CSV
				    foreach ($rows as $row) {
				        fputcsv($file, [
				            $row->id,
				            date("d-M-Y", strtotime($row->created_at)),
				            $row->security->amc->name ?? '',
				            $row->security->symbol ?? '',
				            $row->security->isin ?? '',
				            $row->basket_no,
				            $row->basket_no * $row->basket_size,
				            $row->status_id > 2 ? 'Yes' : 'No',
				            $row->utr_no ? 'Yes' : 'No',
				            $row->status_id > 6 ? 'Yes' : 'No',
				            $row->status_id > 7 ? 'Yes' : 'No',
				            $row->status_id > 9 ? 'Yes' : 'No',
				            $row->status_id > 11 ? 'Yes' : 'No',
				            $row->status_id > 13 ? 'Yes' : 'No',
				        ]);
				    }
				    fclose($file);
				};

		$responseHeaders = [
		    'Content-Type' => 'text/csv',
		    'Content-Disposition' => 'attachment; filename="opsBuyTickets.csv"',
		    'Cache-Control' => 'no-store, no-cache',
		    'Pragma' => 'no-cache',
		    'Expires' => '0',
		];

		return response()->stream($callback, 200, $responseHeaders);				

	}


    public function exportOpsSaleCasesToCSV($rows)
	{
		$headers = [
					'Ticket ID', 'Date', 'AMC Name', 'Symbol', 'ISIN', 'No Of baskets', 'Qty',
					'Units Sent', 'Appl Sent', 'Order Recd', 'Deal Recd', 'Unit Trf', 'Amt Recd'
		];

		$callback = function() use ($rows, $headers) {
				    $file = fopen('php://output', 'w');
				    
				    // Write the headers
				    fputcsv($file, $headers);

				    // Iterate over each row and write to the CSV
				    foreach ($rows as $row) {
				        fputcsv($file, [
				            $row->id,
							date("d-M-Y", strtotime($row->created_at)),
							$row->security->amc->name,
							$row->security->symbol,
							$row->security->isin,
							$row->basket_no,
							$row->basket_no * $row->basket_size,
							'',
							$row->status_id > 6 ? 'Yes' : 'No',
							$row->status_id > 7 ? 'Yes' : 'No',
							$row->status_id > 9 ? 'Yes' : 'No',                                   
							$row->status_id > 13 ? 'Yes' : 'No',
							$row->status_id > 12 ? 'Yes' : 'No' 
				        ]);
				    }
				    fclose($file);
				};

		$responseHeaders = [
		    'Content-Type' => 'text/csv',
		    'Content-Disposition' => 'attachment; filename="opsSaleTickets.csv"',
		    'Cache-Control' => 'no-store, no-cache',
		    'Pragma' => 'no-cache',
		    'Expires' => '0',
		];

		return response()->stream($callback, 200, $responseHeaders);				

	}

    public function exportTradersBuyCasesToCSV($rows)
    {
    	$headers = [
			'AMC Name', 'Symbol', 'Quick Ticket',
			'NAV', 'Quick Ticket Value', 'Ticket Raised',
			'Pending Tickets', 'Amount Sent', 'Total Units',
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

				    	$totalQuickTicket += ($row->total_quick_basket_no || 0);
						$totalQuickTicketVal += ($row->total_quick_amt || 0);
						$totalQuickTicketUnits += ($row->total_quick_units + ($row->total_ticket_units || 0) );
						$totalTicket += ($row->total_ticket_basket_no || 0);
						$amtSent += ($row->total_ticket_actual_amt || 0);

				        fputcsv($file, [
				            ( $row->security ? row->security->amc->name : '-' ),
							( $row->security ? row->security->symbol : '-' ),
							( $row->total_quick_basket_no == 0 ? '-' : row->total_quick_basket_no ),
							( $row->total_quick_nav == 0 ? '-' : (row->total_quick_nav / row->total_quick_clubbed ) ),
							( $row->total_quick_amt == 0 ? '-' : row->total_quick_amt ),
							( $row->total_ticket_basket_no == 0 ? '-' : row->total_ticket_basket_no ),
							( $row->total_quick_basket_no - row->total_ticket_basket_no ),
							( $row->total_ticket_actual_amt == 0 ? '-' : row->total_ticket_actual_amt ),
							( $row->total_quick_units + row->total_ticket_units )
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
							number_format($totalQuickTicketUnits, 2),
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

    public function exportTradersSaleCasesToCSV($rows)
    {
    	$headers = [
			'AMC Name', 'Symbol', 'Quick Ticket',
			'NAV', 'Quick Ticket Value', 'Ticket Raised',
			'Pending Tickets', 'Amount Sent', 'Total Units',
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

				    	$totalQuickTicket += ($row->total_quick_basket_no || 0);
						$totalQuickTicketVal += ($row->total_quick_amt || 0);
						$totalQuickTicketUnits += ($row->total_quick_units + ($row->total_ticket_units || 0) );
						$totalTicket += ($row->total_ticket_basket_no || 0);
						$amtSent += ($row->total_ticket_actual_amt || 0);

				        fputcsv($file, [
				            ( $row->security ? row->security->amc->name : '-' ),
							( $row->security ? row->security->symbol : '-' ),
							( $row->total_quick_basket_no == 0 ? '-' : row->total_quick_basket_no ),
							( $row->total_quick_nav == 0 ? '-' : (row->total_quick_nav / row->total_quick_clubbed ) ),
							( $row->total_quick_amt == 0 ? '-' : row->total_quick_amt ),
							( $row->total_ticket_basket_no == 0 ? '-' : row->total_ticket_basket_no ),
							( $row->total_quick_basket_no - row->total_ticket_basket_no ),
							( $row->total_ticket_actual_amt == 0 ? '-' : row->total_ticket_actual_amt ),
							( $row->total_quick_units + row->total_ticket_units )
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
							number_format($totalQuickTicketUnits, 2),
					]);

				    // Close the HANDLE
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

	// EXPORT MIS DATA to CSV
    public function exportMisDataToCSV(Request $request)
    {
		$setType  = $request->input('datamode'); // Buy / SELL 
        $userType = $request->input('usertype');   // User Type	
        $role_id = 0;
		
		if($userType == 'trader') $role_id = 2;
		if($userType == 'accounts') $role_id = 6;
		if($userType == 'ops') $role_id = 3;
		if($userType == 'dealer') $role_id = 5;
		
		DB::enableQueryLog();
		
		if($userType == 'ops')
		{
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

				return $this->exportOpsBuyCasesToCSV($data);
				
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

				return $this->exportOpsSaleCasesToCSV($data);	
			}

		}


		if($userType == 'trader')
		{
			
			$users = $users = DB::table('role_user')
							->where("role_id", $role_id)
							->get();
							
			$userArr = [];
			foreach($users as $user)
			{
				$userArr[] = $user->user_id; 
			}
			
			$currentDate = Carbon::today();
			$startOfDay = $currentDate->copy()->startOfDay();
			$endOfDay = $currentDate->copy()->endOfDay();

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
						->where(function ($query) use ($userArr) {
							$query->where('trader_id', 0)
								  ->orWhereIn('user_id', $userArr );
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
						->whereIn('user_id', $userArr )
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
						->where(function ($query) use ($userArr) {
							$query->where('trader_id', 0)
								  ->orWhereIn('user_id', $userArr );
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
						->whereIn('user_id', $userArr )
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

			$data = $combinedData->values()->all();

			if ($setType == 1) { // BUY case
				return $this->exportTradersBuyCasesToCSV($data);
			}else{
				return $this->exportTradersSaleCasesToCSV($data);
			}

		}


    }

	public function getMisData(Request $request)
    {
		$setType  = $request->input('sel_role_id'); // Buy / SELL 
        $userType = $request->input('usertype');   // User Type	
        $role_id = 0;
		
		if($userType == 'trader') $role_id = 2;
		if($userType == 'accounts') $role_id = 6;
		if($userType == 'ops') $role_id = 3;
		if($userType == 'dealer') $role_id = 5;
 		
		DB::enableQueryLog();

		if($userType == 'dealer')
		{
			$currentDate = Carbon::today();

			if ($setType == 1) { // BUY case
				$data = Ticket::where('type', $setType)
					->whereDate('created_at', $currentDate)
					->with('security', 'security.amc')
					->orderBy('created_at', 'desc')
					->get();
			} else { // SELL case
				$data = Ticket::where('type', $setType)
					->whereDate('created_at', $currentDate)
					->with('security', 'security.amc')
					->orderBy('created_at', 'desc')
					->get();
			}

			return response()->json($data);
		}
		
		if($userType == 'ops')
		{
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
		
		if($userType == 'accounts')
		{
			$currentDate = Carbon::today();
			$startOfDay = $currentDate->copy()->startOfDay();
			$endOfDay = $currentDate->copy()->endOfDay();

			if ($setType == 1) { // BUY case
				$data = Ticket::where('type', $setType)
					->whereBetween('created_at', [$startOfDay, $endOfDay])
					->with('security', 'security.amc')
					->orderBy('created_at', 'desc')
					->get();
			} else { // SELL case
				$previousDate = Carbon::yesterday();
				$startOfPreviousDay = $previousDate->startOfDay();
				$endOfCurrentDay = $currentDate->endOfDay();
				
				$data = Ticket::where('type', $setType)
					->whereBetween('created_at', [$startOfPreviousDay, $endOfCurrentDay])
					->with('security', 'security.amc')
					->orderBy('created_at', 'desc')
					->get();
			}

			return response()->json($data);
		}
		
		if($userType == 'trader')
		{
			
			$users = $users = DB::table('role_user')
							->where("role_id", $role_id)
							->get();
							
			$userArr = [];
			foreach($users as $user)
			{
				$userArr[] = $user->user_id; 
			}
			
			$currentDate = Carbon::today();
			$startOfDay = $currentDate->copy()->startOfDay();
			$endOfDay = $currentDate->copy()->endOfDay();

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
						->where(function ($query) use ($userArr) {
							$query->where('trader_id', 0)
								  ->orWhereIn('user_id', $userArr );
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
						->whereIn('user_id', $userArr )
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
						->where(function ($query) use ($userArr) {
							$query->where('trader_id', 0)
								  ->orWhereIn('user_id', $userArr );
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
						->whereIn('user_id', $userArr )
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
