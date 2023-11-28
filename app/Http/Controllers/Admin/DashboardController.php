<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Logic to retrieve data for the admin dashboard
        $data = [
            'title' => 'Admin Dashboard',
            // Add other data as needed
        ];

        // Return the admin dashboard view with the data
        return view('admin.dashboard', $data);
    }
}
