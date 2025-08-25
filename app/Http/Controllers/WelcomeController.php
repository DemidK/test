<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NavLink;

class WelcomeController extends CrudController
{
    /**
     * Display the welcome page.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('welcome', [
            'featuredContent' => 'Welcome to our application! Explore our features and get started.',
            'recentUpdates' => [
                'New user dashboard released!',
                'Improved performance and security updates.',
                'Added support for multiple languages.',
            ],
        ]);
    }
}