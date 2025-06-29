-- Add currency support to donations table
-- For academic purposes - simple currency converter

ALTER TABLE donations 
ADD COLUMN original_currency VARCHAR(3) DEFAULT 'RWF' AFTER amount,
ADD COLUMN original_amount DECIMAL(10,2) DEFAULT 0.00 AFTER original_currency,
ADD COLUMN exchange_rate DECIMAL(10,4) DEFAULT 1.0000 AFTER original_amount;

-- Update existing donations to have default values
UPDATE donations 
SET original_currency = 'RWF', 
    original_amount = amount, 
    exchange_rate = 1.0000 
WHERE original_currency IS NULL;

-- Add index for better performance
ALTER TABLE donations ADD INDEX idx_currency (original_currency);

-- Show the updated table structure
DESCRIBE donations;