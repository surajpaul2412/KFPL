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

class QuickTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tickets = QuickTicket::whereUserId(Auth::user()->id)->paginate(10);
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
            'trader_id' => 'required|exists:users,id',
        ]);
        $validatedData["user_id"] = Auth::user()->id;

        // Create a new QuickTicket instance
        $quickTicket = QuickTicket::create($validatedData);
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
