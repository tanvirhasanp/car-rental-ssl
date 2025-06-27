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

// Check if user is logged in
if(!isset($_SESSION['login']) || empty($_SESSION['login'])) {
    // Try to restore session from transaction ID
    $transactionId = isset($_POST['tran_id']) ? $_POST['tran_id'] : (isset($_GET['tran_id']) ? $_GET['tran_id'] : '');
    if($transactionId) {
        // Get user email from transaction
        $sql = "SELECT userEmail FROM tblbooking WHERE transaction_id = :tran_id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':tran_id', $transactionId, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
        if($result) {
            $_SESSION['login'] = $result->userEmail;
        }
    }
}

$message = 'Payment failed or was declined by the bank. Please try again with a different payment method.';
$transactionId = isset($_POST['tran_id']) ? $_POST['tran_id'] : (isset($_GET['tran_id']) ? $_GET['tran_id'] : '');

// Get booking details if transaction ID is available
$bookingDetails = null;
if($transactionId) {
    $sql = "SELECT tblbooking.*, tblvehicles.VehiclesTitle, tblbrands.BrandName, 
            tblusers.FullName FROM tblbooking 
            JOIN tblvehicles ON tblbooking.VehicleId = tblvehicles.id 
            JOIN tblbrands ON tblvehicles.VehiclesBrand = tblbrands.id
            JOIN tblusers ON tblbooking.userEmail = tblusers.EmailId
            WHERE tblbooking.transaction_id = :tran_id";
    
    $query = $dbh->prepare($sql);
    $query->bindParam(':tran_id', $transactionId, PDO::PARAM_STR);
    $query->execute();
    $bookingDetails = $query->fetch(PDO::FETCH_OBJ);
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
<title>Payment Failed - Car Rental Portal</title>
<!--Bootstrap -->
<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="assets/css/style.css" type="text/css">
<link rel="stylesheet" href="assets/css/font-awesome.min.css" type="text/css">
<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet"> 
<style>
.error-container {
    min-height: 600px;
    padding: 50px 0;
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
}
.error-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    padding: 50px;
    text-align: center;
    margin: 20px 0;
}
.error-icon {
    font-size: 80px;
    color: #dc3545;
    margin-bottom: 30px;
}
.error-title {
    font-size: 2.5em;
    font-weight: bold;
    color: #333;
    margin-bottom: 20px;
}
.error-message {
    font-size: 1.2em;
    color: #666;
    margin-bottom: 30px;
    line-height: 1.6;
}
.booking-info {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 25px;
    margin: 30px 0;
    text-align: left;
}
.info-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}
.info-row:last-child {
    border-bottom: none;
}
.info-label {
    font-weight: bold;
    color: #495057;
}
.info-value {
    color: #6c757d;
}
.action-buttons {
    margin-top: 40px;
}
.btn-retry {
    background: #dc3545;
    border-color: #dc3545;
    color: white;
    padding: 12px 30px;
    border-radius: 25px;
    font-weight: bold;
    margin: 0 10px;
    transition: all 0.3s ease;
}
.btn-retry:hover {
    background: #c82333;
    border-color: #bd2130;
    color: white;
    transform: translateY(-2px);
}
.btn-home {
    background: #6c757d;
    border-color: #6c757d;
    color: white;
    padding: 12px 30px;
    border-radius: 25px;
    font-weight: bold;
    margin: 0 10px;
    transition: all 0.3s ease;
}
.btn-home:hover {
    background: #5a6268;
    border-color: #545b62;
    color: white;
    transform: translateY(-2px);
}
</style>
</head>
<body>

<?php include('includes/header.php'); ?>

<section class="error-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="error-card">
                    <!-- Close Button -->
                    <button type="button" class="close-popup" onclick="closePaymentPopup()" style="position: absolute; top: 20px; right: 20px; background: none; border: none; font-size: 24px; color: #666; cursor: pointer; z-index: 1000;">
                        <i class="fa fa-times"></i>
                    </button>
                    
                    <div class="error-icon">
                        <i class="fa fa-times-circle"></i>
                    </div>
                    <h1 class="error-title">Payment Failed</h1>
                    <p class="error-message"><?php echo htmlentities($message); ?></p>
                    
                    <?php if($bookingDetails): ?>
                    <div class="booking-info">
                        <h4 style="margin-bottom: 20px; color: #495057;">
                            <i class="fa fa-info-circle"></i> Booking Information
                        </h4>
                        <div class="info-row">
                            <span class="info-label">Booking Number:</span>
                            <span class="info-value"><?php echo htmlentities($bookingDetails->BookingNumber); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Vehicle:</span>
                            <span class="info-value"><?php echo htmlentities($bookingDetails->BrandName . ' ' . $bookingDetails->VehiclesTitle); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Customer:</span>
                            <span class="info-value"><?php echo htmlentities($bookingDetails->FullName); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">From Date:</span>
                            <span class="info-value"><?php echo htmlentities(date('d M Y', strtotime($bookingDetails->FromDate))); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">To Date:</span>
                            <span class="info-value"><?php echo htmlentities(date('d M Y', strtotime($bookingDetails->ToDate))); ?></span>
                        </div>
                        <?php if($transactionId): ?>
                        <div class="info-row">
                            <span class="info-label">Transaction ID:</span>
                            <span class="info-value"><?php echo htmlentities($transactionId); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="action-buttons">
                        <a href="payment_fixed.php" class="btn btn-retry">
                            <i class="fa fa-refresh"></i> Try Again
                        </a>
                        <a href="index.php" class="btn btn-home">
                            <i class="fa fa-home"></i> Go Home
                        </a>
                    </div>
                    
                    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
                        <p style="color: #6c757d; font-size: 0.9em;">
                            <i class="fa fa-info-circle"></i> 
                            If you continue to experience issues, please contact our support team.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>

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
    $('.error-container').on('click', function(e) {
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
