<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'customer_name',
        'customer_email',
        'customer_address',
        'items',
        'total_amount',
    ];

    // Cast the 'items' field to an array
    protected $casts = [
        'items' => 'array',
    ];
}