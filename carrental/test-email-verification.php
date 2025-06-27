<?php
// Test Email Verification Script
// This script helps test and debug email verification functionality

include('includes/config.php');
include('includes/email_functions.php');

echo "<h1>Email Verification Test</h1>";

// Test 1: Check database structure
echo "<h2>1. Database Structure Check</h2>";
try {
    $sql = "DESCRIBE tblusers";
    $query = $dbh->prepare($sql);
    $query->execute();
    $columns = $query->fetchAll(PDO::FETCH_ASSOC);
    
    $requiredColumns = ['email_verified', 'verification_token', 'token_created_at'];
    $foundColumns = [];
    
    foreach($columns as $column) {
        if(in_array($column['Field'], $requiredColumns)) {
            $foundColumns[] = $column['Field'];
        }
    }
    
    if(count($foundColumns) == count($requiredColumns)) {
        echo "<div style='color: green;'>✅ All required columns found: " . implode(', ', $foundColumns) . "</div>";
    } else {
        echo "<div style='color: red;'>❌ Missing columns. Found: " . implode(', ', $foundColumns) . "</div>";
        echo "<div style='color: orange;'>Please run the SQL script: SQL File/add_email_verification.sql</div>";
    }
} catch(Exception $e) {
    echo "<div style='color: red;'>❌ Database error: " . $e->getMessage() . "</div>";
}

// Test 2: Check email configuration
echo "<h2>2. Email Configuration Check</h2>";
echo "<div>SMTP Host: " . SMTP_HOST . "</div>";
echo "<div>SMTP Port: " . SMTP_PORT . "</div>";
echo "<div>SMTP Username: " . SMTP_USERNAME . "</div>";
echo "<div>SMTP From Email: " . SMTP_FROM_EMAIL . "</div>";
echo "<div>Site URL: " . SITE_URL . "</div>";

// Test 3: Test email sending
if(isset($_POST['test_email'])) {
    echo "<h2>3. Email Sending Test</h2>";
    $testEmail = $_POST['test_email'];
    $testName = 'Test User';
    $testToken = 'test123token456';
    
    if(sendVerificationEmail($testEmail, $testName, $testToken)) {
        echo "<div style='color: green;'>✅ Test verification email sent successfully!</div>";
        echo "<div>Check your email: " . htmlspecialchars($testEmail) . "</div>";
        echo "<div>Test verification link: " . SITE_URL . "verify-email.php?token=" . $testToken . "</div>";
    } else {
        echo "<div style='color: red;'>❌ Failed to send test email. Check your SMTP configuration.</div>";
    }
}

// Test 4: Check existing users
echo "<h2>4. Existing Users Check</h2>";
try {
    $sql = "SELECT EmailId, email_verified, verification_token FROM tblusers LIMIT 5";
    $query = $dbh->prepare($sql);
    $query->execute();
    $users = $query->fetchAll(PDO::FETCH_ASSOC);
    
    if(count($users) > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Email</th><th>Verified</th><th>Has Token</th></tr>";
        foreach($users as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['EmailId']) . "</td>";
            echo "<td>" . ($user['email_verified'] ? 'Yes' : 'No') . "</td>";
            echo "<td>" . ($user['verification_token'] ? 'Yes' : 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div>No users found in database.</div>";
    }
} catch(Exception $e) {
    echo "<div style='color: red;'>❌ Error checking users: " . $e->getMessage() . "</div>";
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Email Verification Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .form-group { margin-bottom: 15px; }
        input[type="email"] { padding: 8px; width: 300px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="warning">
        <strong>⚠️ Troubleshooting Tips:</strong>
        <ul>
            <li>Make sure you've run the SQL script to add email verification fields</li>
            <li>Check that your Gmail app password is correct</li>
            <li>Verify the SITE_URL matches your actual project location</li>
            <li>Check your spam folder for test emails</li>
        </ul>
    </div>
    
    <form method="post">
        <div class="form-group">
            <label for="test_email">Enter your email to test verification email:</label><br>
            <input type="email" name="test_email" id="test_email" required 
                   placeholder="your.email@example.com">
        </div>
        <button type="submit">Send Test Verification Email</button>
    </form>
    
    <hr>
    <p><a href="index.php">Back to Home</a></p>
</body>
</html> 