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
        'customer_id',
        'customer_vat',
        'customer_post_address',
        'items',
        'total_amount',
        'total_vat',
        'total_wo_vat',
        'updater'
    ];
    
    protected $casts = [
        'items' => 'array',
        'total_vat' => 'float',
        'total_wo_vat' => 'float',
        'customer_id' => 'integer'
    ];
}