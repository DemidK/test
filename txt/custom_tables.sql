-- Create enum type for field data types
DO 
$$BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'field_type') THEN
        CREATE TYPE field_type AS ENUM (
            'string',
            'text',
            'integer',
            'float',
            'decimal',
            'boolean',
            'date',
            'datetime',
            'json',
            'select',
            'email',
            'password',
            'file'
        );
    END IF;
END$$;

-- Create custom_tables table
CREATE TABLE IF NOT EXISTS custom_tables (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    display_name VARCHAR(255) NOT NULL,
    description TEXT,
    fields JSONB NOT NULL,
    validation_rules JSONB,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP WITH TIME ZONE
);

-- Add unique constraint to ensure table names are unique
CREATE UNIQUE INDEX idx_custom_tables_name ON custom_tables (name) WHERE deleted_at IS NULL;

-- Add index on deleted_at to improve performance with soft deletes
CREATE INDEX idx_custom_tables_deleted_at ON custom_tables (deleted_at);

-- Create a function to automatically update the updated_at timestamp
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Create a trigger to call the function when a row is updated
CREATE TRIGGER set_updated_at
BEFORE UPDATE ON custom_tables
FOR EACH ROW
EXECUTE FUNCTION update_updated_at_column();

-- Add comment to explain the purpose of the table
COMMENT ON TABLE custom_tables IS 'Stores metadata for dynamically created custom tables in the application';

-- Add comments to explain columns
COMMENT ON COLUMN custom_tables.name IS 'The table name (must start with lowercase letter and only contain lowercase letters, numbers, and underscores)';
COMMENT ON COLUMN custom_tables.display_name IS 'The human-readable name shown in the UI';
COMMENT ON COLUMN custom_tables.description IS 'Optional description of the table purpose';
COMMENT ON COLUMN custom_tables.fields IS 'JSON structure defining the fields and their properties';
COMMENT ON COLUMN custom_tables.validation_rules IS 'JSON structure defining validation rules for fields';
COMMENT ON COLUMN custom_tables.is_active IS 'Flag to enable or disable the table';
COMMENT ON COLUMN custom_tables.deleted_at IS 'Timestamp for soft deletion';