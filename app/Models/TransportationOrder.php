<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Partner;

class TransportationOrder extends BaseModel
{
    protected $fillable = [
        'carrier_name', 'reg_number', 'address',
        'is_our_vehicle', 'vehicle_number', 'vehicle_brand', 'vehicle_type', 'driver_name',
        'cargo_type', 'cargo_description', 'max_tonnage', 'ldm', 'pallet_count', 'volume_m3',
        'load_address', 'load_address_info', 'load_datetime', 'adr_level',
        'unload_address', 'unload_address_info', 'unload_datetime',
        'freight_amount', 'vat_status', 'currency', 'payment_term_days', 'penalty_amount',
        'documents_required', 'special_conditions', 'order_number', 'partner_id'
    ];

    protected $dates = [
        'load_datetime', 
        'unload_datetime'
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }
}