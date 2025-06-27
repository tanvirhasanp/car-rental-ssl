<?php
session_start();
include('includes/config.php');

// This is a test page to simulate the session state for OTP debugging
// In real usage, these session variables are set during the login process

// Set test session variables
$_SESSION['temp_email'] = 'tanvir16211821@gmail.com';
$_SESSION['temp_login_token'] = 'test_token_' . time();

// Check if user exists and add test OTP data
$email = $_SESSION['temp_email'];
$checkSql = "SELECT id FROM tblusers WHERE EmailId = :email";
$checkQuery = $dbh->prepare($checkSql);
$checkQuery->bindParam(':email', $email, PDO::PARAM_STR);
$checkQuery->execute();
$userExists = $checkQuery->fetch(PDO::FETCH_OBJ);

if($userExists) {
    // Update with test OTP data
    $testOtp = '123456';
    $tempToken = $_SESSION['temp_login_token'];
    
    $updateSql = "UPDATE tblusers SET 
                  otp_code = :otp, 
                  otp_verified = 0, 
                  temp_login_token = :token 
                  WHERE EmailId = :email";
    $updateQuery = $dbh->prepare($updateSql);
    $updateQuery->bindParam(':otp', $testOtp, PDO::PARAM_STR);
    $updateQuery->bindParam(':token', $tempToken, PDO::PARAM_STR);
    $updateQuery->bindParam(':email', $email, PDO::PARAM_STR);
    
    if($updateQuery->execute()) {
        echo "<h3>Test Session Setup Complete!</h3>";
        echo "<p>Email: " . $email . "</p>";
        echo "<p>Temp Token: " . $tempToken . "</p>";
        echo "<p>Test OTP: " . $testOtp . "</p>";
        echo "<p><a href='debug-otp.php'>View OTP Debug Info →</a></p>";
        echo "<p><a href='verify-otp.php'>Test OTP Verification Page →</a></p>";
    } else {
        echo "<p>Error setting up test data.</p>";
    }
} else {
    echo "<p>User with email $email not found. Please register first.</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 40px; }
h3 { color: #28a745; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
