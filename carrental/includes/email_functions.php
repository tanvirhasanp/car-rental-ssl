<?php
// Include PHPMailer classes
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';
require_once 'PHPMailer/src/Exception.php';
require_once 'email_config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendVerificationEmail($email, $fullName, $verificationToken) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_ENCRYPTION;
        $mail->Port       = SMTP_PORT;

        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($email, $fullName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification - ' . SITE_NAME;
        
        $verificationLink = SITE_URL . 'verify-email.php?token=' . $verificationToken;
        
        $mailBody = '
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #007bff; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .button { display: inline-block; padding: 12px 24px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 15px 0; }
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>' . SITE_NAME . '</h1>
                </div>
                <div class="content">
                    <h2>Welcome ' . htmlspecialchars($fullName) . '!</h2>
                    <p>Thank you for registering with ' . SITE_NAME . '. To complete your registration, please verify your email address by clicking the button below:</p>
                    
                    <p style="text-align: center;">
    <a href="' . $verificationLink . '" class="button" style="color: white;">Verify Email Address</a>
</p>

                    
                    <p>If the button above doesn\'t work, you can copy and paste the following link into your browser:</p>
                    <p style="word-break: break-all; color: #007bff;">' . $verificationLink . '</p>
                    
                    <p><strong>Important:</strong> This verification link will expire in 24 hours for security reasons.</p>
                    
                    <p>If you didn\'t create an account with us, please ignore this email.</p>
                </div>
                <div class="footer">
                    <p>&copy; ' . date('Y') . ' ' . SITE_NAME . '. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';

        $mail->Body = $mailBody;
        $mail->AltBody = 'Welcome ' . $fullName . '! Please verify your email address by visiting: ' . $verificationLink;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

function sendPasswordResetEmail($email, $fullName, $resetToken) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_ENCRYPTION;
        $mail->Port       = SMTP_PORT;

        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($email, $fullName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset - ' . SITE_NAME;
        
        $resetLink = SITE_URL . 'reset-password.php?token=' . $resetToken;
        
        $mailBody = '
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #dc3545; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .button { display: inline-block; padding: 12px 24px; background-color: #dc3545; color: white; text-decoration: none; border-radius: 5px; margin: 15px 0; }
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>' . SITE_NAME . '</h1>
                </div>
                <div class="content">
                    <h2>Password Reset Request</h2>
                    <p>Hello ' . htmlspecialchars($fullName) . ',</p>
                    <p>We received a request to reset your password. Click the button below to create a new password:</p>
                    
                    <p style="text-align: center;">
                        <a href="' . $resetLink . '" class="button">Reset Password</a>
                    </p>
                    
                    <p>If the button above doesn\'t work, you can copy and paste the following link into your browser:</p>
                    <p style="word-break: break-all; color: #dc3545;">' . $resetLink . '</p>
                    
                    <p><strong>Important:</strong> This password reset link will expire in 1 hour for security reasons.</p>
                    
                    <p>If you didn\'t request a password reset, please ignore this email and your password will remain unchanged.</p>
                </div>
                <div class="footer">
                    <p>&copy; ' . date('Y') . ' ' . SITE_NAME . '. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';

        $mail->Body = $mailBody;
        $mail->AltBody = 'Password Reset Request. Please visit: ' . $resetLink;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

function send2FAOtpEmail($email, $fullName, $otpCode) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_ENCRYPTION;
        $mail->Port       = SMTP_PORT;

        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($email, $fullName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Login Verification Code - ' . SITE_NAME;
        
        $mailBody = '
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #28a745; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; text-align: center; }
                .otp-code { 
                    font-size: 32px; 
                    font-weight: bold; 
                    color: #28a745; 
                    letter-spacing: 8px; 
                    background: #fff; 
                    border: 2px dashed #28a745; 
                    padding: 20px; 
                    margin: 20px 0; 
                    border-radius: 10px;
                }
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
                .warning { color: #dc3545; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🔐 ' . SITE_NAME . '</h1>
                </div>
                <div class="content">
                    <h2>Login Verification Code</h2>
                    <p>Hello ' . htmlspecialchars($fullName) . ',</p>
                    <p>Your login verification code is:</p>
                    
                    <div class="otp-code">' . $otpCode . '</div>
                    
                    <p><strong>Enter this code on the website to complete your login.</strong></p>
                    
                    <p class="warning">⚠️ This code will expire in 5 minutes for security reasons.</p>
                    
                    <p><strong>Security Note:</strong> If you did not attempt to login, please ignore this email and consider changing your password.</p>
                </div>
                <div class="footer">
                    <p>&copy; ' . date('Y') . ' ' . SITE_NAME . '. All rights reserved.</p>
                    <p>This is an automated message, please do not reply.</p>
                </div>
            </div>
        </body>
        </html>';

        $mail->Body = $mailBody;
        $mail->AltBody = 'Your login verification code is: ' . $otpCode . '. This code will expire in 5 minutes.';

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("OTP email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>
