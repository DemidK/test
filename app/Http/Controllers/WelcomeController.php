<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NavLink;

class WelcomeController extends Controller
{
    /**
     * Display the welcome page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch links ordered by their position
        $navLinks = NavLink::orderBy('position')->get();

        return view('welcome', [
            'navLinks' => $navLinks,
            'featuredContent' => 'Welcome to our application! Explore our features and get started.',
            'recentUpdates' => [
                'New user dashboard released!',
                'Improved performance and security updates.',
                'Added support for multiple languages.',
            ],
        ]);
    }
}