-- Migration: Add option_parts table for option-part relationships
-- This allows product options to have multiple parts with OR relationships

-- Create option_parts table
CREATE TABLE IF NOT EXISTS option_parts (
    option_id INTEGER NOT NULL,
    part_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL DEFAULT 1,
    alternative_group INTEGER,
    priority INTEGER NOT NULL DEFAULT 0,
    notes VARCHAR(500),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (option_id, part_id),
    FOREIGN KEY (option_id) REFERENCES product_options(id) ON DELETE CASCADE,
    FOREIGN KEY (part_id) REFERENCES parts(id) ON DELETE CASCADE
);

-- Create indexes for better query performance
CREATE INDEX IF NOT EXISTS idx_option_parts_alternative_group ON option_parts(alternative_group);

-- Remove old part_id column from product_options (if exists)
-- Note: SQLite doesn't support DROP COLUMN directly, so we'll create a new table
-- and copy data if the column exists

-- Check if part_id column exists and migrate data
-- This is a conditional migration that handles both new and existing databases
CREATE TABLE IF NOT EXISTS product_options_new (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL,
    name VARCHAR(255) NOT NULL,
    option_group VARCHAR(100) NOT NULL,
    price_modifier DECIMAL(10, 2) NOT NULL DEFAULT 0,
    sort_order INTEGER NOT NULL DEFAULT 0,
    is_active BOOLEAN NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Copy data from old table to new (if exists)
INSERT OR IGNORE INTO product_options_new 
SELECT id, product_id, name, option_group, price_modifier, sort_order, is_active, created_at, updated_at
FROM product_options;

-- Migrate existing part_id relationships to option_parts table
-- Only if part_id column exists and has data
INSERT OR IGNORE INTO option_parts (option_id, part_id, quantity, alternative_group, priority)
SELECT id, part_id, 1, NULL, 0
FROM product_options
WHERE part_id IS NOT NULL;

-- Drop old table and rename new one
DROP TABLE IF EXISTS product_options;
ALTER TABLE product_options_new RENAME TO product_options;

-- Recreate indexes
CREATE INDEX IF NOT EXISTS idx_product_options_product_id ON product_options(product_id);
CREATE INDEX IF NOT EXISTS idx_product_options_option_group ON product_options(option_group);
CREATE INDEX IF NOT EXISTS idx_product_options_is_active ON product_options(is_active);
