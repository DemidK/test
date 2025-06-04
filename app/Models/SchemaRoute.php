<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchemaRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'schema_name',
        'route_name',
        'user_id',
        'is_active'
    ];

    /**
     * Get the user that owns the schema route.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}