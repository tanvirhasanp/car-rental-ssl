<?php
session_start();
include('includes/config.php');

// Debug page to check OTP status
if(!isset($_SESSION['temp_email'])) {
    echo "No active OTP session";
    exit();
}

$email = $_SESSION['temp_email'];
$tempToken = $_SESSION['temp_login_token'];

$sql = "SELECT * FROM tblusers WHERE EmailId = :email AND temp_login_token = :token";
$query = $dbh->prepare($sql);
$query->bindParam(':email', $email, PDO::PARAM_STR);
$query->bindParam(':token', $tempToken, PDO::PARAM_STR);
$query->execute();
$result = $query->fetch(PDO::FETCH_OBJ);

?>
<!DOCTYPE html>
<html>
<head>
    <title>OTP Debug Info</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .debug-box { background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .error { color: red; }
        .success { color: green; }
        .warning { color: orange; }
    </style>
</head>
<body>
    <h1>OTP Debug Information</h1>
    
    <div class="debug-box">
        <h3>Session Data:</h3>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
        <p><strong>Temp Token:</strong> <?php echo htmlspecialchars($tempToken); ?></p>
    </div>
    
    <?php if($result): ?>
    <div class="debug-box">
        <h3>Database Data:</h3>
        <p><strong>OTP Code:</strong> <?php echo $result->otp_code ? $result->otp_code : '<span class="error">NULL</span>'; ?></p>
        <p><strong>OTP Verified:</strong> <?php echo $result->otp_verified ? '<span class="warning">Yes (Already Used)</span>' : '<span class="success">No (Available)</span>'; ?></p>
        <p><strong>Email Verified:</strong> <?php echo $result->email_verified ? '<span class="success">Yes</span>' : '<span class="error">No</span>'; ?></p>
    </div>
    
    <div class="debug-box">
        <h3>Status Check:</h3>
        <?php if($result->otp_verified == 1): ?>
            <p class="warning">⚠️ OTP has already been used</p>
        <?php elseif(!$result->otp_code): ?>
            <p class="error">❌ No OTP code found</p>
        <?php else: ?>
            <p class="success">✅ OTP is valid and ready to use</p>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="debug-box">
        <p class="error">❌ No user found with this email and temp token</p>
    </div>
    <?php endif; ?>
    
    <div class="debug-box">
        <h3>Test OTP:</h3>
        <form method="post" action="verify-otp.php">
            <input type="text" name="otp_code" placeholder="Enter OTP to test" maxlength="6" style="padding: 8px; font-size: 16px;">
            <button type="submit" name="verify_otp" style="padding: 8px 16px;">Test OTP</button>
        </form>
    </div>
    
    <p><a href="verify-otp.php">← Back to OTP Verification</a></p>
    <p><a href="index.php">← Back to Home</a></p>
</body>
</html>
