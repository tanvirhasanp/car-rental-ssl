<?php
// Include email functions for OTP
require_once 'email_functions.php';

if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    
    // Check if user exists and password is correct
    $sql = "SELECT EmailId, Password, FullName, email_verified FROM tblusers WHERE EmailId = :email AND Password = :password";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetch(PDO::FETCH_OBJ);
    
    if($query->rowCount() > 0) {
        // Check if email is verified
        if($results->email_verified == 1) {
            // Generate 6-digit OTP
            $otpCode = sprintf("%06d", random_int(100000, 999999));
            $tempLoginToken = bin2hex(random_bytes(32));
            
            // Save OTP to database
            $updateSql = "UPDATE tblusers SET otp_code = :otp, otp_verified = 0, 
                          temp_login_token = :temp_token WHERE EmailId = :email";
            $updateQuery = $dbh->prepare($updateSql);
            $updateQuery->bindParam(':otp', $otpCode, PDO::PARAM_STR);
            $updateQuery->bindParam(':temp_token', $tempLoginToken, PDO::PARAM_STR);
            $updateQuery->bindParam(':email', $email, PDO::PARAM_STR);
            
            if($updateQuery->execute()) {
                // Send OTP email
                if(send2FAOtpEmail($email, $results->FullName, $otpCode)) {
                    // Store temp token in session for OTP verification
                    $_SESSION['temp_login_token'] = $tempLoginToken;
                    $_SESSION['temp_email'] = $email;
                    echo "<script type='text/javascript'> document.location = 'verify-otp.php'; </script>";
                } else {
                    echo "<script>alert('Error sending OTP. Please try again.');</script>";
                }
            } else {
                echo "<script>alert('Error generating OTP. Please try again.');</script>";
            }
        } else {
            echo "<script>alert('Please verify your email address before logging in. Check your email for verification link.');</script>";
        }
    } else {
        echo "<script>alert('Invalid email or password.');</script>";
    }
}

?>

<div class="modal fade" id="loginform">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Login</h3>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="login_wrap">
            <div class="col-md-12 col-sm-6">
              <form method="post">
                <div class="form-group">
                  <input type="email" class="form-control" name="email" placeholder="Email address*">
                </div>
                <div class="form-group">
                  <input type="password" class="form-control" name="password" placeholder="Password*">
                </div>
                <div class="form-group checkbox">
                  <input type="checkbox" id="remember">
               
                </div>
                <div class="form-group">
                  <input type="submit" name="login" value="Login" class="btn btn-block">
                </div>
              </form>
            </div>
           
          </div>
        </div>
      </div>
      <div class="modal-footer text-center">
        <p>Don't have an account? <a href="#signupform" data-toggle="modal" data-dismiss="modal">Signup Here</a></p>
        <p><a href="#forgotpassword" data-toggle="modal" data-dismiss="modal">Forgot Password ?</a></p>
        <p><a href="resend-verification.php">Resend Verification Email</a></p>
        <p><a href="test-email-verification.php" target="_blank">Test Email Verification</a></p>
      </div>
    </div>
  </div>
</div>