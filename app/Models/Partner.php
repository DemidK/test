<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\Json;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'identification_number',
        'json_data',
    ];

    protected $casts = [
        'json_data' => Json::class,
    ];
}