<?php 
// Set session cookie params for consistent path and domain
$cookieParams = session_get_cookie_params();
session_set_cookie_params([
    'lifetime' => $cookieParams['lifetime'],
    'path' => '/',
    'domain' => $cookieParams['domain'],
    'secure' => false, // set to true if using HTTPS
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();
// Debug session
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('includes/config.php');
include('includes/sslcommerz_config.php');
include('includes/SSLCommerz.php');

// Check if user is logged in
if(!isset($_SESSION['login']) || empty($_SESSION['login'])) {
    // Try to restore session from transaction ID
    if(isset($_POST['tran_id'])) {
        $tran_id = $_POST['tran_id'];
        // Get user email from transaction
        $sql = "SELECT userEmail FROM tblbooking WHERE transaction_id = :tran_id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':tran_id', $tran_id, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
        if($result) {
            $_SESSION['login'] = $result->userEmail;
        }
    }
}

$message = '';
$messageType = 'error';

// Validate payment response
if(isset($_POST['status']) && $_POST['status'] == 'VALID') {
    $val_id = $_POST['val_id'];
    $tran_id = $_POST['tran_id'];
    $amount = $_POST['amount'];
    $card_type = $_POST['card_type'];
    $store_amount = $_POST['store_amount'];
    $bank_tran_id = $_POST['bank_tran_id'];
    
    // Validate transaction with SSL Commerz
    $sslcommerz = new SSLCommerz(STORE_ID, STORE_PASSWORD);
    $validation = $sslcommerz->orderValidation($val_id, STORE_ID, STORE_PASSWORD, $tran_id);
    
    if($validation && isset($validation['status']) && ($validation['status'] == 'VALID' || $validation['status'] == 'VALIDATED')) {
        // Update booking status
        $updateSql = "UPDATE tblbooking SET Status = 1, payment_status = 'PAID', 
                      validation_id = :val_id, bank_tran_id = :bank_tran_id, 
                      payment_method = :payment_method, paid_amount = :amount
                      WHERE transaction_id = :tran_id";
        
        $updateQuery = $dbh->prepare($updateSql);
        $updateQuery->bindParam(':val_id', $val_id, PDO::PARAM_STR);
        $updateQuery->bindParam(':bank_tran_id', $bank_tran_id, PDO::PARAM_STR);
        $updateQuery->bindParam(':payment_method', $card_type, PDO::PARAM_STR);
        $updateQuery->bindParam(':amount', $amount, PDO::PARAM_STR);
        $updateQuery->bindParam(':tran_id', $tran_id, PDO::PARAM_STR);
        
        if($updateQuery->execute()) {
            $message = 'Payment successful! Your booking has been confirmed.';
            $messageType = 'success';
            
            // Clear session data
            unset($_SESSION['booking_id']);
            unset($_SESSION['booking_number']);
            unset($_SESSION['booking_amount']);
            unset($_SESSION['tran_id']);
        } else {
            $message = 'Payment received but booking update failed. Please contact support.';
        }
    } else {
        $message = 'Payment validation failed or incomplete. Please contact support if money has been deducted.';
        // Log validation error for debugging
        error_log("SSLCommerz validation failed for transaction: " . $tran_id . ", Validation response: " . print_r($validation, true));
    }
} else {
    $message = 'Invalid payment response. Please try again.';
}

// Get booking details for display
$bookingDetails = null;
if(isset($tran_id)) {
    $sql = "SELECT tblbooking.*, tblvehicles.VehiclesTitle, tblbrands.BrandName, 
            tblusers.FullName FROM tblbooking 
            JOIN tblvehicles ON tblbooking.VehicleId = tblvehicles.id 
            JOIN tblbrands ON tblvehicles.VehiclesBrand = tblbrands.id
            JOIN tblusers ON tblbooking.userEmail = tblusers.EmailId
            WHERE tblbooking.transaction_id = :tran_id";
    
    $query = $dbh->prepare($sql);
    $query->bindParam(':tran_id', $tran_id, PDO::PARAM_STR);
    $query->execute();
    $bookingDetails = $query->fetch(PDO::FETCH_OBJ);
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
<title>Payment Success - Car Rental Portal</title>
<!--Bootstrap -->
<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="assets/css/style.css" type="text/css">
<link rel="stylesheet" href="assets/css/font-awesome.min.css" type="text/css">
<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet"> 
<style>
.success-container {
    min-height: 600px;
    padding: 50px 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.success-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    padding: 50px;
    text-align: center;
    margin: 20px 0;
}
.success-icon {
    font-size: 80px;
    color: #28a745;
    margin-bottom: 30px;
    animation: bounceIn 1s ease-in-out;
}
.error-icon {
    font-size: 80px;
    color: #dc3545;
    margin-bottom: 30px;
    animation: bounceIn 1s ease-in-out;
}
@keyframes bounceIn {
    0% { transform: scale(0.3); opacity: 0; }
    50% { transform: scale(1.05); }
    70% { transform: scale(0.9); }
    100% { transform: scale(1); opacity: 1; }
}
.booking-info {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 30px;
    margin: 30px 0;
    text-align: left;
}
.payment-info {
    background: #e8f5e9;
    border-radius: 10px;
    padding: 20px;
    margin: 20px 0;
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
        <h1>Payment Status</h1>
      </div>
      <ul class="coustom-breadcrumb">
        <li><a href="index.php">Home</a></li>
        <li><a href="my-booking.php">My Bookings</a></li>
        <li>Payment Status</li>
      </ul>
    </div>
  </div>
  <div class="dark-overlay"></div>
</section>

<section class="success-container">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="success-card">
                    
                    <!-- Close Button -->
                    <button type="button" class="close-popup" onclick="closePaymentPopup()" style="position: absolute; top: 20px; right: 20px; background: none; border: none; font-size: 24px; color: #666; cursor: pointer; z-index: 1000;">
                        <i class="fa fa-times"></i>
                    </button>
                    
                    <?php if($messageType == 'success'): ?>
                        <div class="success-icon">
                            <i class="fa fa-check-circle"></i>
                        </div>
                        <h2 style="color: #28a745;">Payment Successful!</h2>
                        <p style="font-size: 18px; margin: 20px 0;"><?php echo $message; ?></p>
                        
                        <?php if($bookingDetails): ?>
                        <div class="booking-info">
                            <h4><i class="fa fa-car"></i> Booking Details</h4>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Booking Number:</strong> <?php echo htmlentities($bookingDetails->BookingNumber); ?></p>
                                    <p><strong>Vehicle:</strong> <?php echo htmlentities($bookingDetails->BrandName . ' ' . $bookingDetails->VehiclesTitle); ?></p>
                                    <p><strong>Customer:</strong> <?php echo htmlentities($bookingDetails->FullName); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>From Date:</strong> <?php echo htmlentities($bookingDetails->FromDate); ?></p>
                                    <p><strong>To Date:</strong> <?php echo htmlentities($bookingDetails->ToDate); ?></p>
                                    <p><strong>Status:</strong> <span style="color: #28a745; font-weight: bold;">Confirmed</span></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="payment-info">
                            <h5><i class="fa fa-credit-card"></i> Payment Information</h5>
                            <p><strong>Transaction ID:</strong> <?php echo htmlentities($tran_id); ?></p>
                            <p><strong>Amount Paid:</strong> <?php echo CURRENCY_SYMBOL . number_format($amount, 2); ?></p>
                            <p><strong>Payment Method:</strong> <?php echo htmlentities($card_type); ?></p>
                            <p><strong>Bank Transaction ID:</strong> <?php echo htmlentities($bank_tran_id); ?></p>
                        </div>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <div class="error-icon">
                            <i class="fa fa-times-circle"></i>
                        </div>
                        <h2 style="color: #dc3545;">Payment Failed!</h2>
                        <p style="font-size: 18px; margin: 20px 0;"><?php echo $message; ?></p>
                    <?php endif; ?>
                    
                    <div style="margin-top: 40px;">
                        <a href="my-booking.php" class="btn btn-primary btn-lg" style="margin: 10px;">
                            <i class="fa fa-list"></i> View My Bookings
                        </a>
                        <a href="index.php" class="btn btn-secondary btn-lg" style="margin: 10px;">
                            <i class="fa fa-home"></i> Back to Home
                        </a>
                        <?php if($messageType != 'success'): ?>
                        <a href="payment_fixed.php" class="btn btn-warning btn-lg" style="margin: 10px;">
                            <i class="fa fa-refresh"></i> Try Again
                        </a>
                        <?php endif; ?>
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
// Function to close payment popup
function closePaymentPopup() {
    // Redirect to home page or my bookings
    window.location.href = 'my-booking.php';
}

// Close popup when clicking outside
$(document).ready(function() {
    $('.success-container').on('click', function(e) {
        if (e.target === this) {
            closePaymentPopup();
        }
    });
    
    // Close popup when pressing ESC key
    $(document).keydown(function(e) {
        if (e.keyCode === 27) { // ESC key
            closePaymentPopup();
        }
    });
});
</script>

</body>
</html>
