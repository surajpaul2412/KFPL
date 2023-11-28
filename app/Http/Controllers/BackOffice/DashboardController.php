<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Logic to retrieve data for the backoffice dashboard
        $data = [
            'title' => 'Backoffice Dashboard',
            // Add other data as needed
        ];

        // Return the backoffice dashboard view with the data
        return view('backoffice.dashboard', $data);
    }
}
