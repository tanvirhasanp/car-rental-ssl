# Email Verification + 2FA OTP Setup Instructions

## 1. Database Setup

Run both SQL scripts to add email verification and 2FA OTP fields:

1. Open phpMyAdmin
2. Select your `carrental` database
3. Go to SQL tab
4. **First, run the email verification script:**
   ```sql
   -- Copy and paste from SQL File/add_email_verification.sql
   ALTER TABLE `tblusers` 
   ADD COLUMN `email_verified` TINYINT(1) DEFAULT 0 AFTER `Password`,
   ADD COLUMN `verification_token` VARCHAR(255) DEFAULT NULL AFTER `email_verified`,
   ADD COLUMN `token_created_at` TIMESTAMP NULL DEFAULT NULL AFTER `verification_token`;
   
   ALTER TABLE `tblusers`
   ADD COLUMN `reset_token` VARCHAR(255) DEFAULT NULL AFTER `token_created_at`,
   ADD COLUMN `reset_token_created_at` TIMESTAMP NULL DEFAULT NULL AFTER `reset_token`;
   ```

5. **Then, run the 2FA OTP script:**
   ```sql
   -- Copy and paste from SQL File/add_2fa_otp.sql
   ALTER TABLE `tblusers` 
   ADD COLUMN `otp_code` VARCHAR(6) DEFAULT NULL AFTER `reset_token_created_at`,
   ADD COLUMN `otp_created_at` TIMESTAMP NULL DEFAULT NULL AFTER `otp_code`,
   ADD COLUMN `otp_verified` TINYINT(1) DEFAULT 0 AFTER `otp_created_at`,
   ADD COLUMN `temp_login_token` VARCHAR(255) DEFAULT NULL AFTER `otp_verified`;
   ```

## 2. Gmail App Password Setup

### Step 1: Enable 2-Factor Authentication
1. Go to your Google Account settings (myaccount.google.com)
2. Click on "Security" in the left sidebar
3. Under "Signing in to Google", click on "2-Step Verification"
4. Follow the prompts to enable 2FA if not already enabled

### Step 2: Generate App Password
1. After enabling 2FA, go back to Security settings
2. Under "Signing in to Google", click on "App passwords"
3. Select "Mail" as the app and "Other (Custom name)" as the device
4. Enter "Car Rental Portal" as the custom name
5. Click "Generate"
6. Copy the 16-character app password (save it securely)

## 3. Email Configuration

The email configuration is already set up in `includes/email_config.php`:

```php
define('SMTP_USERNAME', 'tanvir16211821@gmail.com');
define('SMTP_PASSWORD', 'jhuc ymbi rmhh eiwp'); // Your app password
define('SMTP_FROM_EMAIL', 'tanvir16211821@gmail.com');
define('SITE_URL', 'http://localhost/old_ok_projects/car-rentall/carrental/');
```

## 4. Test the System

1. **Test Email Configuration:**
   - Visit `http://localhost/old_ok_projects/car-rentall/carrental/test-email.php`
   - Test both verification email and 2FA OTP email
   - Check if emails are received (check spam folder too)

2. **Test Registration Flow:**
   - Register a new user
   - Check email for verification link
   - Verify email account

3. **Test 2FA Login Flow:**
   - Try logging in with verified account
   - Check email for 6-digit OTP
   - Enter OTP on the verification page
   - Complete login successfully

## 5. How the 2FA Login Flow Works

1. **User enters email + password** → System validates credentials
2. **If valid** → 6-digit OTP generated and sent to email
3. **User redirected to OTP page** → Enter 6-digit code from email
4. **OTP verified** → User successfully logged in
5. **OTP expires in 5 minutes** → User can request new OTP

## 6. Security Features

### Email Verification:
- ✅ Users must verify email before login
- ✅ Verification tokens expire in 24 hours
- ✅ Resend verification email functionality

### 2FA OTP:
- ✅ 6-digit random OTP for each login
- ✅ OTP expires in 5 minutes
- ✅ One-time use (can't reuse same OTP)
- ✅ Resend OTP functionality
- ✅ Auto-cleanup of expired sessions
- ✅ Secure temp session tokens

## 7. Features Added

### New Features:
- 🔐 **Two-Factor Authentication (2FA)** with email OTP
- 📧 **Professional OTP Email Template** with countdown timer
- ⏰ **Real-time countdown timer** on OTP verification page
- 🔄 **Resend OTP functionality** 
- 🧹 **Auto-cleanup of expired OTP sessions**
- 🛡️ **Session security** with temporary login tokens

### Original Features:
- ✅ Email verification during registration
- ✅ Login blocked until email is verified
- ✅ Resend verification email functionality
- ✅ Professional HTML email templates
- ✅ Token expiration (24 hours for verification)
- ✅ Duplicate email prevention

## 8. Files Added/Modified

### New Files:
- `includes/email_config.php` - Email configuration ✅
- `includes/email_functions.php` - Email sending functions (Updated with OTP)
- `includes/otp_functions.php` - 2FA OTP session management
- `verify-email.php` - Email verification page ✅
- `verify-otp.php` - **NEW: 2FA OTP verification page**
- `resend-verification.php` - Resend verification email page ✅
- `test-email.php` - Email testing tool (Updated for OTP testing)
- `SQL File/add_email_verification.sql` - Database schema updates ✅
- `SQL File/add_2fa_otp.sql` - **NEW: 2FA database fields**

### Modified Files:
- `includes/registration.php` - Added email verification ✅
- `includes/login.php` - **Updated: Added 2FA OTP flow**
- `includes/config.php` - **Updated: Added auto-cleanup**
- `logout.php` - **Updated: Clear OTP sessions on logout**

## 9. Troubleshooting

### 2FA OTP Issues:

1. **OTP not received:**
   - Check spam/junk folder
   - Verify email configuration
   - Test with `test-email.php`

2. **"Invalid OTP" error:**
   - Check if OTP has expired (5 minutes)
   - Ensure correct 6-digit code
   - Try requesting new OTP

3. **Stuck on OTP page:**
   - Clear browser sessions
   - Try logging out and logging in again

### Common Issues:

- **"Authentication failed"**: Wrong Gmail app password or 2FA not enabled
- **OTP emails in spam**: Normal for new setups, whitelist the sender
- **Database errors**: Make sure both SQL scripts are executed

## 10. Security Considerations

- 🔒 **Strong OTP Generation**: Cryptographically secure random 6-digit codes
- ⏰ **Time-based Expiration**: OTP expires in 5 minutes
- 🗑️ **Auto-cleanup**: Expired sessions automatically removed
- 🔐 **Session Protection**: Temporary tokens prevent session hijacking
- 📧 **Email Security**: Professional templates prevent phishing attempts

## 11. Production Recommendations

1. **Use environment variables** for email credentials
2. **Implement rate limiting** for OTP requests
3. **Add IP-based restrictions** for security
4. **Use dedicated email service** (SendGrid, Mailgun) for production
5. **Upgrade password hashing** from MD5 to bcrypt
6. **Add logging** for security events

## 12. Next Steps (Optional Enhancements)

- Add SMS OTP as alternative to email
- Implement "Remember this device" functionality
- Add backup codes for account recovery
- Email notifications for security events
- Admin panel for monitoring 2FA usage
