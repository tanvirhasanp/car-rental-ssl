<?php
session_start();
include('includes/config.php');
error_reporting(0);

$message = '';
$messageType = '';

if(isset($_GET['token']) && !empty($_GET['token'])) {
    $token = $_GET['token'];
    
    // Verify the token and check if it's not expired (24 hours)
    $sql = "SELECT * FROM tblusers WHERE verification_token = :token AND email_verified = 0 
            AND token_created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':token', $token, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);
    
    if($result) {
        // Update user as verified
        $updateSql = "UPDATE tblusers SET email_verified = 1, verification_token = NULL, token_created_at = NULL 
                      WHERE verification_token = :token";
        $updateQuery = $dbh->prepare($updateSql);
        $updateQuery->bindParam(':token', $token, PDO::PARAM_STR);
        
        if($updateQuery->execute()) {
            $message = 'Email verified successfully! You can now login to your account.';
            $messageType = 'success';
        } else {
            $message = 'Error verifying email. Please try again or contact support.';
            $messageType = 'error';
        }
    } else {
        // Check if user is already verified
        $checkSql = "SELECT * FROM tblusers WHERE verification_token = :token AND email_verified = 1";
        $checkQuery = $dbh->prepare($checkSql);
        $checkQuery->bindParam(':token', $token, PDO::PARAM_STR);
        $checkQuery->execute();
        
        if($checkQuery->rowCount() > 0) {
            $message = 'Email is already verified. You can login to your account.';
            $messageType = 'info';
        } else {
            $message = 'Invalid or expired verification token. Please register again or contact support.';
            $messageType = 'error';
        }
    }
} else {
    $message = 'No verification token provided.';
    $messageType = 'error';
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
<title>Email Verification - Car Rental Portal</title>
<!--Bootstrap -->
<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
<!--Custom Style -->
<link rel="stylesheet" href="assets/css/style.css" type="text/css">
<!--FontAwesome Font Style -->
<link href="assets/css/font-awesome.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">
<style>
.verification-container {
    min-height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    text-align: center;
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
.alert-info {
    color: #0c5460;
    background-color: #d1ecf1;
    border-color: #bee5eb;
}
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
    font-size: 16px;
}
.verification-icon {
    font-size: 60px;
    margin-bottom: 20px;
}
.success-icon { color: #28a745; }
.error-icon { color: #dc3545; }
.info-icon { color: #17a2b8; }
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
        <h1>Email Verification</h1>
      </div>
      <ul class="coustom-breadcrumb">
        <li><a href="index.php">Home</a></li>
        <li>Email Verification</li>
      </ul>
    </div>
  </div>
  <div class="dark-overlay"></div>
</section>

<section class="verification-container">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <?php if($messageType == 'success'): ?>
                    <div class="verification-icon success-icon">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="alert alert-success">
                        <?php echo $message; ?>
                    </div>
                    <a href="#loginform" class="btn btn-primary" data-toggle="modal">Go to Login</a>
                    
                <?php elseif($messageType == 'info'): ?>
                    <div class="verification-icon info-icon">
                        <i class="fa fa-info-circle"></i>
                    </div>
                    <div class="alert alert-info">
                        <?php echo $message; ?>
                    </div>
                    <a href="#loginform" class="btn btn-primary" data-toggle="modal">Go to Login</a>
                    
                <?php else: ?>
                    <div class="verification-icon error-icon">
                        <i class="fa fa-times-circle"></i>
                    </div>
                    <div class="alert alert-error">
                        <?php echo $message; ?>
                    </div>
                    <a href="index.php" class="btn btn-primary">Back to Home</a>
                <?php endif; ?>
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

<!--Login-Form -->
<?php include('includes/login.php');?>
<!--/Login-Form -->

<!--Register-Form -->
<?php include('includes/registration.php');?>
<!--/Register-Form -->

<!-- Scripts -->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    // Improve the login modal experience
    $('a[href="#loginform"]').on('click', function(e) {
        e.preventDefault();
        // Close any existing modals first
        $('.modal').modal('hide');
        
        // Wait a moment then show the login modal
        setTimeout(function() {
            $('#loginform').modal({
                backdrop: 'static', // Prevent closing when clicking outside
                keyboard: true,     // Allow closing with escape key
                show: true
            });
        }, 200);
    });
    
    // Focus on email field when login modal opens
    $('#loginform').on('shown.bs.modal', function () {
        $(this).find('input[name="email"]').focus();
    });
    
    // Prevent the modal from closing accidentally
    $('#loginform').on('hide.bs.modal', function (e) {
        // Only allow closing if the user clicked the close button or pressed escape
        if (!$(e.target).hasClass('close') && e.originalEvent && e.originalEvent.type !== 'keydown') {
            return false;
        }
    });
});
</script>

</body>
</html>
