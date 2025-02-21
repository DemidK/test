<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\NavLink;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $navLinks = NavLink::orderBy('position')->get();
        $totalUsers = User::count();
        $totalClients = Client::count();
        $data = [
            'navLinks' => $navLinks,
            'totalUsers' => $totalUsers,
            'totalClients' => $totalClients,
            'totalRevenue' => 12345,
            'recentActivities' => [
                'User "John Doe" placed an order.',
                'User "Jane Smith" registered.',
                'Order #123 was completed.',
            ],
        ];

        // Return the dashboard view with the data
        return view('dashboard', $data);
    }
}