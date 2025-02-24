<?php

namespace App\Http\Controllers;

use App\Models\Partner;
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
        $totalPartners = Partner::count();
        $data = [
            'navLinks' => $navLinks,
            'totalUsers' => $totalUsers,
            'totalPartners' => $totalPartners,
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