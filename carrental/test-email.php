<?php
// Test Email Configuration Script
// Run this file to test if your email setup is working

include('includes/config.php');
include('includes/email_functions.php');

if(isset($_POST['test_email'])) {
    $testEmail = $_POST['test_email'];
    $testName = 'Test User';
    $testToken = 'test123token';
    $testOtp = '123456';
    
    echo "<h2>Testing Email Configuration...</h2>";
    
    // Test verification email
    if(isset($_POST['test_type']) && $_POST['test_type'] == 'otp') {
        echo "<h3>Testing 2FA OTP Email...</h3>";
        if(send2FAOtpEmail($testEmail, $testName, $testOtp)) {
            echo "<div style='color: green; padding: 10px; border: 1px solid green; margin: 10px 0;'>";
            echo "<strong>✅ Success!</strong> Test OTP email sent successfully to: " . htmlspecialchars($testEmail);
            echo "<br>Test OTP Code: <strong>$testOtp</strong>";
            echo "</div>";
        } else {
            echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 10px 0;'>";
            echo "<strong>❌ Failed!</strong> Could not send test OTP email. Check your configuration.";
            echo "</div>";
        }
    } else {
        echo "<h3>Testing Verification Email...</h3>";
        if(sendVerificationEmail($testEmail, $testName, $testToken)) {
            echo "<div style='color: green; padding: 10px; border: 1px solid green; margin: 10px 0;'>";
            echo "<strong>✅ Success!</strong> Test verification email sent successfully to: " . htmlspecialchars($testEmail);
            echo "</div>";
        } else {
            echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 10px 0;'>";
            echo "<strong>❌ Failed!</strong> Could not send test verification email. Check your configuration.";
            echo "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Email Configuration Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .form-group { margin-bottom: 15px; }
        input[type="email"] { padding: 8px; width: 300px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>Email Configuration Test</h1>
    
    <div class="warning">
        <strong>⚠️ Warning:</strong> Make sure you have:
        <ul>
            <li>Updated <code>includes/email_config.php</code> with your Gmail credentials</li>
            <li>Generated a Gmail App Password (not your regular password)</li>
            <li>Added the database fields using the SQL script</li>
        </ul>
    </div>
    
    <form method="post">
        <div class="form-group">
            <label for="test_email">Enter your email address to receive a test email:</label><br>
            <input type="email" name="test_email" id="test_email" required 
                   placeholder="your.email@example.com">
        </div>
        <div class="form-group">
            <label>Choose email type to test:</label><br>
            <input type="radio" name="test_type" value="verification" id="test_verification" checked>
            <label for="test_verification">Verification Email</label><br>
            <input type="radio" name="test_type" value="otp" id="test_otp">
            <label for="test_otp">2FA OTP Email</label>
        </div>
        <button type="submit">Send Test Email</button>
    </form>
    
    <hr>
    <h3>Configuration Check:</h3>
    <ul>
        <li><strong>SMTP Host:</strong> <?php echo defined('SMTP_HOST') ? SMTP_HOST : '❌ Not configured'; ?></li>
        <li><strong>SMTP Username:</strong> <?php echo defined('SMTP_USERNAME') ? SMTP_USERNAME : '❌ Not configured'; ?></li>
        <li><strong>Site URL:</strong> <?php echo defined('SITE_URL') ? SITE_URL : '❌ Not configured'; ?></li>
    </ul>
    
    <p><a href="index.php">← Back to Main Site</a></p>
</body>
</html>
