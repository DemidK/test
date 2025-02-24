<?php

namespace App\Models\Dynamic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Qwe extends Model
{
    use SoftDeletes;

    protected $table = 'qwe';

    protected $guarded = ['id'];

    protected $casts = [
        'asd' => 'boolean',
    ];

}
