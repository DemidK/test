<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        if (auth()->check() && auth()->user()->schema_name) {
            return auth()->user()->schema_name . '.' . $this->table;
        }
    }
}