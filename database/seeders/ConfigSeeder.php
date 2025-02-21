<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Config;

class ConfigSeeder extends Seeder
{
    public function run()
    {
        Config::create([
            'key' => 'client_data_objects',
            'value' => json_encode([
                'initial_count' => 1,
                'max_count' => 5,
                'background_color' => 'bg-gray-50',
                'default_items' => [
                    [
                        'name' => 'Contact Information',
                        'fields' => [
                            ['key' => 'Phone', 'value' => ''],
                            ['key' => 'Email', 'value' => ''],
                            ['key' => 'Address', 'value' => '']
                        ]
                    ]
                ]
            ])
        ]);
    }
}