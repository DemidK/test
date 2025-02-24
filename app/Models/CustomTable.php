<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class CustomTable extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'fields',
        'validation_rules',
        'is_active'
    ];

    protected $casts = [
        'fields' => 'array',
        'validation_rules' => 'array',
        'is_active' => 'boolean'
    ];

    public function getModel()
    {
        $self = $this;
        
        return new class([], $self) extends Model {
            protected $guarded = ['id'];
            protected $casts = [];
            protected $customTable;
            
            public function __construct(array $attributes = [], CustomTable $customTable = null)
            {
                parent::__construct($attributes);
                
                if ($customTable) {
                    $this->customTable = $customTable;
                    $this->table = $customTable->name;
                    
                    // Set up casts based on field types
                    foreach ($customTable->fields as $fieldName => $field) {
                        $this->casts[$fieldName] = match($field['type']) {
                            'integer' => 'integer',
                            'float' => 'decimal:2',
                            'json' => 'array',
                            'boolean' => 'boolean',
                            'date' => 'date',
                            'datetime' => 'datetime',
                            default => 'string'
                        };
                    }
                }
            }
        };
    }

    public static function query()
    {
        return (new static)->newQuery();
    }

    public function getQueryBuilder()
    {
        return DB::table($this->name);
    }
}