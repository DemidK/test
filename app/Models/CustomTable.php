<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

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
    
    // Available field types
    public static $availableTypes = [
        'string' => 'Text (Short)',
        'text' => 'Text (Long)',
        'integer' => 'Integer',
        'float' => 'Decimal Number',
        'decimal' => 'Currency',
        'boolean' => 'Yes/No',
        'date' => 'Date',
        'datetime' => 'Date & Time',
        'json' => 'JSON',
        'select' => 'Select',
        'email' => 'Email',
        'password' => 'Password',
        'file' => 'File'
    ];

    /**
     * Create the actual database table based on the fields defined
     * 
     * @return void
     */
    public function createDatabaseTable()
    {
        Schema::create($this->name, function (Blueprint $table) {
            // Add primary key
            $table->id();
            
            // Add columns based on field definitions
            foreach ($this->fields as $fieldName => $field) {
                $this->addColumnToTable($table, $fieldName, $field);
            }
            
            // Add standard timestamps
            $table->timestamps();
            
            // Add soft delete column if needed
            $table->softDeletes();
        });
    }
    
    /**
     * Drop the database table associated with this custom table
     * 
     * @return void
     */
    public function dropDatabaseTable()
    {
        Schema::dropIfExists($this->name);
    }
    
    /**
     * Add a column to the table based on field type
     * 
     * @param Blueprint $table
     * @param string $fieldName
     * @param array $field
     * @return void
     */
    private function addColumnToTable(Blueprint $table, $fieldName, $field)
    {
        $column = null;
        
        // Create the column based on the field type
        switch ($field['type']) {
            case 'string':
                $column = $table->string($fieldName);
                break;
                
            case 'text':
                $column = $table->text($fieldName);
                break;
                
            case 'integer':
                $column = $table->integer($fieldName);
                break;
                
            case 'float':
            case 'decimal':
                $column = $table->decimal($fieldName, 10, 2);
                break;
                
            case 'boolean':
                $column = $table->boolean($fieldName);
                break;
                
            case 'date':
                $column = $table->date($fieldName);
                break;
                
            case 'datetime':
                $column = $table->dateTime($fieldName);
                break;
                
            case 'json':
                $column = $table->json($fieldName);
                break;
                
            case 'select':
                $column = $table->string($fieldName);
                break;
                
            case 'email':
                $column = $table->string($fieldName);
                break;
                
            case 'password':
                $column = $table->string($fieldName);
                break;
                
            case 'file':
                $column = $table->string($fieldName);
                break;
                
            default:
                $column = $table->string($fieldName);
                break;
        }
        
        // Set column to nullable if not required
        if (!($field['required'] ?? false)) {
            $column->nullable();
        }
        
        // Set default value if provided
        if (isset($field['default']) && $field['default'] !== null) {
            $column->default($field['default']);
        }
    }

    /**
     * Get the dynamic model instance for this custom table
     * 
     * @return \Illuminate\Database\Eloquent\Model
     */
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
                            'decimal' => 'decimal:2',
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
    
    /**
     * Alias for getModel() to match usage in controller
     * 
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getDynamicModel()
    {
        return $this->getModel();
    }

    /**
     * Generate validation rules based on field definitions
     * 
     * @return array
     */
    public function generateValidationRules()
    {
        $rules = [];
        
        foreach ($this->fields as $fieldName => $field) {
            $fieldRules = [];
            
            // Add required rule if field is required
            if ($field['required'] ?? false) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }
            
            // Add type-specific validation rules
            switch ($field['type']) {
                case 'string':
                case 'select':
                case 'email':
                case 'password':
                case 'file':
                    $fieldRules[] = 'string';
                    break;
                    
                case 'text':
                    $fieldRules[] = 'string';
                    break;
                    
                case 'integer':
                    $fieldRules[] = 'integer';
                    break;
                    
                case 'float':
                case 'decimal':
                    $fieldRules[] = 'numeric';
                    break;
                    
                case 'boolean':
                    $fieldRules[] = 'boolean';
                    break;
                    
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                    
                case 'datetime':
                    $fieldRules[] = 'date';
                    break;
                    
                case 'json':
                    $fieldRules[] = 'array';
                    break;
            }
            
            // Add email validation for email fields
            if ($field['type'] === 'email') {
                $fieldRules[] = 'email';
            }
            
            $rules[$fieldName] = $fieldRules;
        }
        
        return $rules;
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