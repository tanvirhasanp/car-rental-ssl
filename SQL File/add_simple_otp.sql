-- Simplified OTP fields for tblusers table (without time-based expiration)
-- This replaces the previous add_2fa_otp.sql with a simpler version

ALTER TABLE `tblusers` 
ADD COLUMN `otp_code` VARCHAR(6) DEFAULT NULL AFTER `reset_token_created_at`,
ADD COLUMN `otp_verified` TINYINT(1) DEFAULT 0 AFTER `otp_code`,
ADD COLUMN `temp_login_token` VARCHAR(255) DEFAULT NULL AFTER `otp_verified`;

-- Note: otp_created_at is removed since we're not using time-based expiration
-- If you previously added otp_created_at, you can optionally remove it with:
-- ALTER TABLE `tblusers` DROP COLUMN `otp_created_at`;
