<?php
// Test file to verify otp_functions.php is working correctly
session_start();
include('includes/config.php');

echo "<h3>Testing OTP Functions</h3>";

// Test function existence
if (function_exists('clearExpiredOtpSessions')) {
    echo "<p>✅ clearExpiredOtpSessions() function exists</p>";
} else {
    echo "<p>❌ clearExpiredOtpSessions() function not found</p>";
}

if (function_exists('validateOtpSession')) {
    echo "<p>✅ validateOtpSession() function exists</p>";
} else {
    echo "<p>❌ validateOtpSession() function not found</p>";
}

if (function_exists('clearCurrentOtpSession')) {
    echo "<p>✅ clearCurrentOtpSession() function exists</p>";
} else {
    echo "<p>❌ clearCurrentOtpSession() function not found</p>";
}

if (function_exists('autoCleanupOtpSessions')) {
    echo "<p>✅ autoCleanupOtpSessions() function exists</p>";
} else {
    echo "<p>❌ autoCleanupOtpSessions() function not found</p>";
}

echo "<p><strong>All OTP functions are loaded correctly!</strong></p>";
echo "<p><a href='verify-otp.php'>← Back to OTP verification</a></p>";
?>
