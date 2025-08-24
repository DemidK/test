<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\Json;

class Invoice extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'partner_name',
        'partner_email',
        'partner_address',
        'partner_id',
        'partner_vat',
        'partner_post_address',
        'items',
        'total_amount',
        'total_vat',
        'total_wo_vat',
        'updater'
    ];
    
    protected $casts = [
        'items' => Json::class,
        'total_vat' => 'float',
        'total_wo_vat' => 'float',
        'partner_id' => 'integer'
    ];
}