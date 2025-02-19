<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NavLink;

class NavLinkSeeder extends Seeder
{
    public function run(): void
    {
        $links = [
            ['name' => 'Home', 'url' => '/', 'position' => 1],
            ['name' => 'About', 'url' => '/about', 'position' => 2],
            ['name' => 'Services', 'url' => '/services', 'position' => 3],
            ['name' => 'Blog', 'url' => '/blog', 'position' => 4],
            ['name' => 'Contact', 'url' => '/contact', 'position' => 5],
            ['name' => 'FAQ', 'url' => '/faq', 'position' => 6],
            ['name' => 'Pricing', 'url' => '/pricing', 'position' => 7],
        ];

        foreach ($links as $link) {
            NavLink::create($link);
        }
    }
}