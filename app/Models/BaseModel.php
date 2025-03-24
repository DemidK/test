<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BaseModel extends Model
{
    /**
     * Models that should always use the public schema
     * @var array
     */
    protected static $publicSchemaModels = [
        'App\Models\User',
        'App\Models\SchemaRoute'
    ];

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        // First get the original table name from the model
        $tableName = parent::getTable();
        
        // If the table name already contains a schema prefix, return it as is
        if (str_contains($tableName, '.')) {
            return $tableName;
        }
        
        // Models that should always use the public schema
        if (in_array(get_class($this), self::$publicSchemaModels)) {
            return $tableName;
        }
        
        // Add schema prefix if user is authenticated and has a schema
        if (Auth::check() && Auth::user()->schema_name) {
            $schemaName = Auth::user()->schema_name;
            
            // For debugging
            Log::debug("Using schema {$schemaName} for table {$tableName}");
            
            return "{$schemaName}.{$tableName}";
        }
        
        // Fall back to the original table name
        return $tableName;
    }
}