<?php
session_start();
include('includes/config.php');
include('includes/email_functions.php');
error_reporting(0);

$message = '';
$messageType = '';

if(isset($_POST['resend'])) {
    $email = $_POST['email'];
    
    // Check if user exists and is not verified
    $sql = "SELECT * FROM tblusers WHERE EmailId = :email AND email_verified = 0";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);
    
    if($result) {
        // Generate new verification token
        $verificationToken = bin2hex(random_bytes(32));
        $tokenCreatedAt = date('Y-m-d H:i:s');
        
        // Update user with new token
        $updateSql = "UPDATE tblusers SET verification_token = :token, token_created_at = :token_created 
                      WHERE EmailId = :email";
        $updateQuery = $dbh->prepare($updateSql);
        $updateQuery->bindParam(':token', $verificationToken, PDO::PARAM_STR);
        $updateQuery->bindParam(':token_created', $tokenCreatedAt, PDO::PARAM_STR);
        $updateQuery->bindParam(':email', $email, PDO::PARAM_STR);
        
        if($updateQuery->execute()) {
            // Send verification email
            if(sendVerificationEmail($email, $result->FullName, $verificationToken)) {
                $message = 'Verification email has been sent to your email address. Please check your inbox and spam folder.';
                $messageType = 'success';
            } else {
                $message = 'Error sending verification email. Please try again later.';
                $messageType = 'error';
            }
        } else {
            $message = 'Error updating verification token. Please try again.';
            $messageType = 'error';
        }
    } else {
        // Check if user exists and is already verified
        $checkSql = "SELECT * FROM tblusers WHERE EmailId = :email AND email_verified = 1";
        $checkQuery = $dbh->prepare($checkSql);
        $checkQuery->bindParam(':email', $email, PDO::PARAM_STR);
        $checkQuery->execute();
        
        if($checkQuery->rowCount() > 0) {
            $message = 'This email address is already verified. You can login to your account.';
            $messageType = 'info';
        } else {
            $message = 'No unverified account found with this email address.';
            $messageType = 'error';
        }
    }
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
<title>Resend Verification Email - Car Rental Portal</title>
<!--Bootstrap -->
<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
<!--Custom Style -->
<link rel="stylesheet" href="assets/css/style.css" type="text/css">
<!--FontAwesome Font Style -->
<link href="assets/css/font-awesome.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">
<style>
.resend-container {
    min-height: 400px;
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
        <h1>Resend Verification Email</h1>
      </div>
      <ul class="coustom-breadcrumb">
        <li><a href="index.php">Home</a></li>
        <li>Resend Verification</li>
      </ul>
    </div>
  </div>
  <div class="dark-overlay"></div>
</section>

<section class="resend-container">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <?php if(!empty($message)): ?>
                    <div class="alert alert-<?php echo $messageType; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Resend Email Verification</h4>
                    </div>
                    <div class="panel-body">
                        <p>Enter your email address to receive a new verification email.</p>
                        <form method="post">
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" class="form-control" name="email" id="email" 
                                       placeholder="Enter your email address" required>
                            </div>
                            <div class="form-group">
                                <button type="submit" name="resend" class="btn btn-primary btn-block">
                                    <i class="fa fa-envelope"></i> Resend Verification Email
                                </button>
                            </div>
                        </form>
                        <div class="text-center">
                            <p><a href="index.php">Back to Home</a></p>
                        </div>
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

<!--Login-Form -->
<?php include('includes/login.php');?>
<!--/Login-Form -->

<!--Register-Form -->
<?php include('includes/registration.php');?>
<!--/Register-Form -->

<!-- Scripts -->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
