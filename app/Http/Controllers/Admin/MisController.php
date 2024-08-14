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
					->get();
			} else { // SELL case
				$data = Ticket::where('type', $setType)
					->whereDate('created_at', $currentDate)
					->with('security', 'security.amc')
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
					->get();
			} else { // SELL case
				$data = Ticket::where('type', $setType)
					->where(function ($query) use ($startOf48HoursAgo) {
						// Show records within the last 48 hours or status_id <= 13
						$query->where('status_id', '<', 13)
							  ->orWhereBetween('created_at', [$startOf48HoursAgo, Carbon::now()]);
					})
					->with('security', 'security.amc')
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
					->get();
			} else { // SELL case
				$previousDate = Carbon::yesterday();
				$startOfPreviousDay = $previousDate->startOfDay();
				$endOfCurrentDay = $currentDate->endOfDay();
				
				$data = Ticket::where('type', $setType)
					->whereBetween('created_at', [$startOfPreviousDay, $endOfCurrentDay])
					->with('security', 'security.amc')
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
