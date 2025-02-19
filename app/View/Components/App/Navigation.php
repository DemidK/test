<?php

namespace App\View\Components\App;

use Illuminate\View\Component;

class Navigation extends Component
{
    public $navLinks;

    public function __construct($navLinks)
    {
        $this->navLinks = $navLinks;
    }

    public function render()
    {
        return view('components.app.navigation');
    }
}