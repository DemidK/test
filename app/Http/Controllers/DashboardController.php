<?php

namespace App\Http\Controllers;

use App\Models\NavLink;
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
        // Fetch links ordered by their position
        $navLinks = NavLink::orderBy('position')->get();
        // Example data to pass to the view
        $data = [
            'navLinks' => $navLinks,
            'totalUsers' => 1234,
            'totalOrders' => 567,
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