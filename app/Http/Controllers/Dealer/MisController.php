<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Carbon\Carbon;

class MisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view(
            "dealer.mis.index"
        );
    }

    public function getMisData(Request $request)
    {
        $setType = $request->input('sel_role_id');
        $currentDate = Carbon::today();

        if ($setType == 1) { // BUY case
            $data = Ticket::where('type', $setType)
                ->where('is_active', 1)
                ->whereDate('created_at', $currentDate)
                ->with('security', 'security.amc')
				->orderBy('created_at', 'desc')
                ->get();
        } else { // SELL case
            $data = Ticket::where('type', $setType)
                ->where('is_active', 1)
                ->whereDate('created_at', $currentDate)
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
