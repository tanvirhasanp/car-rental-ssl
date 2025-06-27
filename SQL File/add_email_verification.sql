-- Add email verification fields to tblusers table
ALTER TABLE `tblusers` 
ADD COLUMN `email_verified` TINYINT(1) DEFAULT 0 AFTER `Password`,
ADD COLUMN `verification_token` VARCHAR(255) DEFAULT NULL AFTER `email_verified`,
ADD COLUMN `token_created_at` TIMESTAMP NULL DEFAULT NULL AFTER `verification_token`;

-- Add password reset fields
ALTER TABLE `tblusers`
ADD COLUMN `reset_token` VARCHAR(255) DEFAULT NULL AFTER `token_created_at`,
ADD COLUMN `reset_token_created_at` TIMESTAMP NULL DEFAULT NULL AFTER `reset_token`;

-- Update existing users to be verified (optional - for existing test data)
UPDATE `tblusers` SET `email_verified` = 1 WHERE `id` > 0;
