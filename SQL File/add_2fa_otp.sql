-- Add OTP fields to tblusers table for 2FA
ALTER TABLE `tblusers` 
ADD COLUMN `otp_code` VARCHAR(6) DEFAULT NULL AFTER `reset_token_created_at`,
ADD COLUMN `otp_created_at` TIMESTAMP NULL DEFAULT NULL AFTER `otp_code`,
ADD COLUMN `otp_verified` TINYINT(1) DEFAULT 0 AFTER `otp_created_at`,
ADD COLUMN `temp_login_token` VARCHAR(255) DEFAULT NULL AFTER `otp_verified`;
