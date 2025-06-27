<?php
session_start();
include('includes/config.php');
error_reporting(0);

// Check if user has a valid temp login token
if(!isset($_SESSION['temp_login_token']) || !isset($_SESSION['temp_email'])) {
    header('location: index.php');
    exit();
}

$message = '';
$messageType = '';

if(isset($_POST['verify_otp'])) {
    $enteredOtp = trim($_POST['otp_code']);
    $tempToken = $_SESSION['temp_login_token'];
    $email = $_SESSION['temp_email'];
    
    // Get the user data to check OTP details
    $checkSql = "SELECT * FROM tblusers WHERE EmailId = :email AND temp_login_token = :token";
    $checkQuery = $dbh->prepare($checkSql);
    $checkQuery->bindParam(':email', $email, PDO::PARAM_STR);
    $checkQuery->bindParam(':token', $tempToken, PDO::PARAM_STR);
    $checkQuery->execute();
    $userData = $checkQuery->fetch(PDO::FETCH_OBJ);
    
    if($userData) {
        // Check if OTP is already used
        if($userData->otp_verified == 1) {
            $message = 'This OTP has already been used. Please login again to receive a new code.';
            $messageType = 'error';
        }
        // Check if OTP matches
        else if($userData->otp_code !== $enteredOtp) {
            $message = 'Invalid OTP. Please check your email and try again.';
            $messageType = 'error';
        }
        // All checks passed - OTP is valid
        else {
            // OTP is correct, complete the login
            $updateSql = "UPDATE tblusers SET otp_verified = 1, otp_code = NULL, 
                          temp_login_token = NULL WHERE EmailId = :email";
            $updateQuery = $dbh->prepare($updateSql);
            $updateQuery->bindParam(':email', $email, PDO::PARAM_STR);
            
            if($updateQuery->execute()) {
                // Set session variables for successful login
                $_SESSION['login'] = $email;
                $_SESSION['fname'] = $userData->FullName;
                
                // Clear temp session variables
                unset($_SESSION['temp_login_token']);
                unset($_SESSION['temp_email']);
                
                echo "<script>alert('Login successful!');</script>";
                echo "<script type='text/javascript'> document.location = 'index.php'; </script>";
            } else {
                $message = 'Error completing login. Please try again.';
                $messageType = 'error';
            }
        }
    } else {
        $message = 'Invalid session. Please login again.';
        $messageType = 'error';
    }
}

if(isset($_POST['resend_otp'])) {
    $email = $_SESSION['temp_email'];
    $tempToken = $_SESSION['temp_login_token'];
    
    // Generate new OTP
    $newOtpCode = sprintf("%06d", random_int(100000, 999999));
    
    // Update with new OTP
    $updateSql = "UPDATE tblusers SET otp_code = :otp, otp_verified = 0 
                  WHERE EmailId = :email AND temp_login_token = :token";
    $updateQuery = $dbh->prepare($updateSql);
    $updateQuery->bindParam(':otp', $newOtpCode, PDO::PARAM_STR);
    $updateQuery->bindParam(':email', $email, PDO::PARAM_STR);
    $updateQuery->bindParam(':token', $tempToken, PDO::PARAM_STR);
    
    if($updateQuery->execute()) {
        // Get user details
        $userSql = "SELECT FullName FROM tblusers WHERE EmailId = :email";
        $userQuery = $dbh->prepare($userSql);
        $userQuery->bindParam(':email', $email, PDO::PARAM_STR);
        $userQuery->execute();
        $userResult = $userQuery->fetch(PDO::FETCH_OBJ);
        
        // Include email functions
        include('includes/email_functions.php');
        
        if(send2FAOtpEmail($email, $userResult->FullName, $newOtpCode)) {
            $message = 'A new OTP has been sent to your email address.';
            $messageType = 'success';
        } else {
            $message = 'Error sending new OTP. Please try again.';
            $messageType = 'error';
        }
    } else {
        $message = 'Error generating new OTP. Please try again.';
        $messageType = 'error';
    }
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
<title>Verify OTP - Car Rental Portal</title>
<!--Bootstrap -->
<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
<!--Custom Style -->
<link rel="stylesheet" href="assets/css/style.css" type="text/css">
<!--FontAwesome Font Style -->
<link href="assets/css/font-awesome.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">
<style>
.otp-container {
    min-height: 500px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    padding: 50px 20px;
}
.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}
.alert-error {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}
.otp-input {
    text-align: center;
    font-size: 24px;
    letter-spacing: 10px;
    font-weight: bold;
    padding: 15px;
    width: 100%;
    max-width: 300px;
    margin: 20px auto;
}
.otp-input-container {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin: 20px 0;
}
.otp-digit {
    width: 50px;
    height: 60px;
    text-align: center;
    font-size: 24px;
    font-weight: bold;
    border: 2px solid #ddd;
    border-radius: 8px;
    background: #fff;
    transition: all 0.3s ease;
    outline: none;
}
.otp-digit:focus {
    border-color: #28a745;
    box-shadow: 0 0 10px rgba(40, 167, 69, 0.3);
    background: #f8fff9;
}
.otp-digit.filled {
    border-color: #28a745;
    background: #e8f5e8;
}
.otp-digit.error {
    border-color: #dc3545;
    background: #ffe6e6;
    animation: shake 0.5s ease-in-out;
}
@keyframes shake {
    0%, 20%, 40%, 60%, 80% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
}
.otp-panel {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    padding: 40px;
    text-align: center;
    max-width: 500px;
    width: 100%;
}
.security-icon {
    font-size: 60px;
    color: #28a745;
    margin-bottom: 20px;
}
.timer {
    color: #dc3545;
    font-weight: bold;
    margin-top: 15px;
}
.btn-link {
    color:rgb(255, 255, 255) !important;
    text-decoration: none;
}
.btn-link:hover {
    color:rgb(205, 211, 216) !important;
    text-decoration: underline;
}
</style>
</head>
<body>

<!--Header-->
<?php include('includes/header.php');?>
<!-- /Header -->

<section class="page-header contactus_page">
  <div class="container">
    <div class="page-header_wrap">
      <div class="page-heading">
        <h1>Two-Factor Authentication</h1>
      </div>
      <ul class="coustom-breadcrumb">
        <li><a href="index.php">Home</a></li>
        <li>Verify OTP</li>
      </ul>
    </div>
  </div>
  <div class="dark-overlay"></div>
</section>

<section class="otp-container">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="otp-panel">
                    <div class="security-icon">
                        <i class="fa fa-shield"></i>
                    </div>
                    
                    <h2>Enter Verification Code</h2>
                    <p>We've sent a 6-digit verification code to:</p>
                    <p><strong><?php echo htmlspecialchars($_SESSION['temp_email']); ?></strong></p>
                    
                    <?php if(!empty($message)): ?>
                        <div class="alert alert-<?php echo $messageType; ?>">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" id="otpForm">
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 15px; font-weight: bold;">Enter 6-digit verification code:</label>
                            <div class="otp-input-container">
                                <input type="text" class="otp-digit" maxlength="1" data-index="0">
                                <input type="text" class="otp-digit" maxlength="1" data-index="1">
                                <input type="text" class="otp-digit" maxlength="1" data-index="2">
                                <input type="text" class="otp-digit" maxlength="1" data-index="3">
                                <input type="text" class="otp-digit" maxlength="1" data-index="4">
                                <input type="text" class="otp-digit" maxlength="1" data-index="5">
                            </div>
                            <input type="hidden" name="otp_code" id="otp_code" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="verify_otp" class="btn btn-success btn-lg" id="verifyBtn">
                                <i class="fa fa-check"></i> Verify & Login
                            </button>
                        </div>
                    </form>
                    
                    <hr>
                    
                    <form method="post" style="display: inline;">
                        <button type="submit" name="resend_otp" class="btn btn-link">
                            <i class="fa fa-refresh"></i> Resend Code
                        </button>
                    </form>
                    
                    <div style="margin-top: 20px;">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Cancel Login
                        </a>
                    </div>
                    
                    <!-- Debug link (remove in production) -->
                    <div style="margin-top: 15px; font-size: 12px;">
                        <a href="debug-otp.php" style="color: #666;">
                            <i class="fa fa-bug"></i> Debug OTP Status
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!--Footer -->
<?php include('includes/footer.php');?>
<!-- /Footer-->

<!--Back to top-->
<div id="back-top" class="back-top"> <a href="#top"><i class="fa fa-angle-up" aria-hidden="true"></i> </a> </div>
<!--/Back to top-->

<!-- Scripts -->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    // Auto-focus on first OTP input
    $('.otp-digit').first().focus();
    
    // Handle OTP input
    $('.otp-digit').on('input', function() {
        var $this = $(this);
        var value = $this.val();
        
        // Only allow numbers
        value = value.replace(/[^0-9]/g, '');
        $this.val(value);
        
        if(value.length === 1) {
            $this.addClass('filled');
            // Move to next input
            var nextIndex = parseInt($this.data('index')) + 1;
            if(nextIndex < 6) {
                $('.otp-digit[data-index="' + nextIndex + '"]').focus();
            }
        } else {
            $this.removeClass('filled');
        }
        
        // Update hidden field with complete OTP
        updateOtpCode();
        
        // Auto-submit if all 6 digits are entered
        if(getAllOtpDigits().length === 6) {
            $('#verifyBtn').removeClass('btn-success').addClass('btn-warning').html('<i class="fa fa-spinner fa-spin"></i> Verifying...');
            setTimeout(function() {
                $('#otpForm').submit();
            }, 500);
        }
    });
    
    // Handle backspace
    $('.otp-digit').on('keydown', function(e) {
        var $this = $(this);
        if(e.keyCode === 8 && $this.val() === '') { // Backspace on empty field
            var prevIndex = parseInt($this.data('index')) - 1;
            if(prevIndex >= 0) {
                var $prev = $('.otp-digit[data-index="' + prevIndex + '"]');
                $prev.focus().val('').removeClass('filled');
                updateOtpCode();
            }
        }
    });
    
    // Handle paste
    $('.otp-digit').on('paste', function(e) {
        e.preventDefault();
        var pastedData = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
        var digits = pastedData.replace(/[^0-9]/g, '').substring(0, 6);
        
        $('.otp-digit').each(function(index) {
            if(index < digits.length) {
                $(this).val(digits[index]).addClass('filled');
            }
        });
        
        updateOtpCode();
        
        if(digits.length === 6) {
            $('#verifyBtn').focus();
        }
    });
    
    function getAllOtpDigits() {
        var otp = '';
        $('.otp-digit').each(function() {
            otp += $(this).val();
        });
        return otp;
    }
    
    function updateOtpCode() {
        $('#otp_code').val(getAllOtpDigits());
    }
    
    // Show error animation if there's an error message
    <?php if(!empty($message) && $messageType === 'error'): ?>
    $('.otp-digit').addClass('error');
    setTimeout(function() {
        $('.otp-digit').removeClass('error');
    }, 1000);
    <?php endif; ?>
});
</script>

</body>
</html>
