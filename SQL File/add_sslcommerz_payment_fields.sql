-- SSL Commerz Payment Integration Database Updates
-- Add payment-related columns to tblbooking table

-- Check if columns exist and add them if they don't
ALTER TABLE tblbooking 
ADD COLUMN IF NOT EXISTS transaction_id VARCHAR(100) NULL,
ADD COLUMN IF NOT EXISTS payment_status ENUM('PENDING', 'PAID', 'FAILED', 'CANCELLED', 'INVALID') DEFAULT 'PENDING',
ADD COLUMN IF NOT EXISTS validation_id VARCHAR(100) NULL,
ADD COLUMN IF NOT EXISTS bank_tran_id VARCHAR(100) NULL,
ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50) NULL,
ADD COLUMN IF NOT EXISTS paid_amount DECIMAL(10,2) NULL,
ADD COLUMN IF NOT EXISTS payment_date DATETIME NULL,
ADD COLUMN IF NOT EXISTS ipn_processed TINYINT(1) DEFAULT 0;

-- Add indexes for better performance
ALTER TABLE tblbooking 
ADD INDEX IF NOT EXISTS idx_transaction_id (transaction_id),
ADD INDEX IF NOT EXISTS idx_payment_status (payment_status),
ADD INDEX IF NOT EXISTS idx_validation_id (validation_id);

-- Update existing bookings to have PENDING payment status if null
UPDATE tblbooking SET payment_status = 'PENDING' WHERE payment_status IS NULL;
