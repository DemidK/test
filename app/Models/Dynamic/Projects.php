<?php

namespace App\Models\Dynamic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Projects extends Model
{
    use SoftDeletes;

    protected $table = 'projects';

    protected $fillable = [
        'name',
        'budget',
        'status',
        'end_date',
        'is_active',
        'start_date',
        'description',
        'team_members',
    ];
}
