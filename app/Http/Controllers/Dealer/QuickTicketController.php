<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Role;
use App\Models\User;
use App\Models\QuickTicket;
use App\Models\Security;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QuickTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tickets = QuickTicket::whereUserId(Auth::user()->id)->orderBy("updated_at", "desc")->paginate(10);
        return view(
            "dealer.quick_tickets.index",
            compact(
                "tickets"
            )
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $securities = Security::whereStatus(1)->get();
        $traders = User::where('status', 1)->get()->filter(function ($user) {
            return $user->isTrader();
        });
        return view("dealer.quick_tickets.create", compact("securities",'traders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'security_id' => 'required|exists:securities,id',
            "type" => "required|integer|in:1,2",
            "payment_type" => "required|integer|in:1,2,3",
            'basket_no' => 'required|integer',
            'basket_size' => 'nullable|string',
            'actual_total_amt' => 'required|numeric',
            'nav' => 'nullable|numeric',
            'trader_id' => 'required|integer',
        ]);
        $validatedData["user_id"] = Auth::user()->id;

		// Make Sure, Trader ID exists in users table
		if($request->trader_id != 0)
		{
			$user = User::where("id", $request->trader_id)->first();
			if(!$user) abort(404);
		}
		
        // Create a new QuickTicket instance
        $quickTicket = QuickTicket::create($validatedData);

        if ($quickTicket->type == 1) {
            $nav_value = purchaseNavValueForQuickTicket($quickTicket, $request->input('actual_total_amt'));
        } else {
            $nav_value = sellNavValueForQuickTicket($quickTicket, $request->input('actual_total_amt'));
        }
        
        $quickTicket->update(['nav'=> $nav_value]);
        
        return redirect()->route('dealer.quick_tickets.index')
            ->with('success', 'Quick Ticket created successfully.');
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
        $ticket = QuickTicket::findOrFail($id);
        $securities = Security::whereStatus(1)->get();
        $traders = User::where('status', 1)->get()->filter(function ($user) {
            return $user->isTrader();
        });
        return view('dealer.quick_tickets.edit', compact('ticket','securities','traders'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $quickTicket = QuickTicket::findOrFail($id);

        // Validate the request data
        $validatedData = Validator::make($request->all(), [
            'security_id' => 'required|exists:securities,id',
            "type" => "required|integer|in:1,2",
            "payment_type" => "required|integer|in:1,2,3",
            'basket_no' => 'required|integer',
            'basket_size' => 'nullable|string',
            'actual_total_amt' => 'required|numeric',
            'nav' => 'nullable|numeric',
            'trader_id' => 'required|integer',
        ])->validate();

        // Update the QuickTicket instance
        $quickTicket->update($validatedData);
		
		// Make Sure, Trader ID exists in users table
		if($request->trader_id != 0)
		{
			$user = User::where("id", $request->trader_id)->first();
			if(!$user) abort(404);
		}
		
        // Update purchaseNav if needed
        if ($request->has('actual_total_amt')) {
            if ($validatedData['type'] == 1) {
                $nav_value = purchaseNavValueForQuickTicket($quickTicket, $request->input('actual_total_amt'));
            } else {
                $nav_value = sellNavValueForQuickTicket($quickTicket, $request->input('actual_total_amt'));
            }
            
            $quickTicket->update(['nav'=> $nav_value]);
        }
        
        return redirect()->route('dealer.quick_tickets.index')
            ->with('success', 'Quick Ticket updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
