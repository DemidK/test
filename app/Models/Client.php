<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\Json;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'surname',
        'identification_number',
        'json_data',
    ];

    protected $casts = [
        'json_data' => Json::class,
    ];
}