<?php

namespace App\Http\Controllers\Ops;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Logic to retrieve data for the ops dashboard
        $data = [
            'title' => 'Ops Dashboard',
            // Add other data as needed
        ];

        // Return the ops dashboard view with the data
        return view('ops.dashboard', $data);
    }
}
