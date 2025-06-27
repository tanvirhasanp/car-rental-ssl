<?php
// OTP Session Management Functions

// Prevent redeclaration if file is included multiple times
if (!function_exists('clearExpiredOtpSessions')) {

function clearExpiredOtpSessions() {
    // This function can be called periodically to clean up old OTP sessions from database
    global $dbh;
    
    $cleanupSql = "UPDATE tblusers SET otp_code = NULL, otp_verified = 0, temp_login_token = NULL 
                   WHERE otp_verified = 1";
    $cleanupQuery = $dbh->prepare($cleanupSql);
    $cleanupQuery->execute();
}

function validateOtpSession() {
    // Validate if current OTP session is still valid
    if(!isset($_SESSION['temp_login_token']) || !isset($_SESSION['temp_email'])) {
        return false;
    }
    
    global $dbh;
    
    $tempToken = $_SESSION['temp_login_token'];
    $email = $_SESSION['temp_email'];
    
    $sql = "SELECT * FROM tblusers WHERE EmailId = :email AND temp_login_token = :token";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':token', $tempToken, PDO::PARAM_STR);
    $query->execute();
    
    return $query->rowCount() > 0;
}

function clearCurrentOtpSession() {
    // Clear current user's OTP session
    if(isset($_SESSION['temp_email'])) {
        global $dbh;
        
        $email = $_SESSION['temp_email'];
        $clearSql = "UPDATE tblusers SET otp_code = NULL, otp_verified = 0, temp_login_token = NULL 
                     WHERE EmailId = :email";
        $clearQuery = $dbh->prepare($clearSql);
        $clearQuery->bindParam(':email', $email, PDO::PARAM_STR);
        $clearQuery->execute();
    }
    
    unset($_SESSION['temp_login_token']);
    unset($_SESSION['temp_email']);
}

// Auto-cleanup used OTP sessions (call this in config.php or any frequently accessed file)
function autoCleanupOtpSessions() {
    // Only run cleanup occasionally to avoid performance impact
    if(!isset($_SESSION['last_otp_cleanup']) || 
       (time() - $_SESSION['last_otp_cleanup']) > 300) { // 5 minutes
        clearExpiredOtpSessions();
        $_SESSION['last_otp_cleanup'] = time();
    }
}

} // End of function existence check
?>
