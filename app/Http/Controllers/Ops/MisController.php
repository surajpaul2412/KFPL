<?php

namespace App\Http\Controllers\Ops;

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
            "ops.mis.index"
        );
    }

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
