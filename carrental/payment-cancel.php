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

$message = 'Payment was cancelled by the user. Your booking is still pending.';
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
<title>Payment Cancelled - Car Rental Portal</title>
<!--Bootstrap -->
<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="assets/css/style.css" type="text/css">
<link rel="stylesheet" href="assets/css/font-awesome.min.css" type="text/css">
<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet"> 
<style>
.cancel-container {
    min-height: 600px;
    padding: 50px 0;
    background: linear-gradient(135deg, #ffd93d 0%, #ff6b35 100%);
}
.cancel-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    padding: 50px;
    text-align: center;
    margin: 20px 0;
}
.cancel-icon {
    font-size: 80px;
    color: #ffc107;
    margin-bottom: 30px;
}
.cancel-title {
    font-size: 2.5em;
    font-weight: bold;
    color: #333;
    margin-bottom: 20px;
}
.cancel-message {
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
    background: #ffc107;
    border-color: #ffc107;
    color: #212529;
    padding: 12px 30px;
    border-radius: 25px;
    font-weight: bold;
    margin: 0 10px;
    transition: all 0.3s ease;
}
.btn-retry:hover {
    background: #e0a800;
    border-color: #d39e00;
    color: #212529;
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
.btn-bookings {
    background: #17a2b8;
    border-color: #17a2b8;
    color: white;
    padding: 12px 30px;
    border-radius: 25px;
    font-weight: bold;
    margin: 0 10px;
    transition: all 0.3s ease;
}
.btn-bookings:hover {
    background: #138496;
    border-color: #117a8b;
    color: white;
    transform: translateY(-2px);
}
</style>
</head>
<body>

<?php include('includes/header.php'); ?>

<section class="cancel-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="cancel-card">
                    <div class="cancel-icon">
                        <i class="fa fa-exclamation-triangle"></i>
                    </div>
                    <h1 class="cancel-title">Payment Cancelled</h1>
                    <p class="cancel-message"><?php echo htmlentities($message); ?></p>
                    
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
                        <div class="info-row">
                            <span class="info-label">Status:</span>
                            <span class="info-value">
                                <span style="color: #ffc107; font-weight: bold;">
                                    <i class="fa fa-clock-o"></i> Pending Payment
                                </span>
                            </span>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="action-buttons">
                        <a href="payment_fixed.php" class="btn btn-retry">
                            <i class="fa fa-credit-card"></i> Complete Payment
                        </a>
                        <a href="my-booking.php" class="btn btn-bookings">
                            <i class="fa fa-list"></i> View Bookings
                        </a>
                        <a href="index.php" class="btn btn-home">
                            <i class="fa fa-home"></i> Go Home
                        </a>
                    </div>
                    
                    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
                        <p style="color: #6c757d; font-size: 0.9em;">
                            <i class="fa fa-info-circle"></i> 
                            You can complete the payment later from your bookings page.
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

</body>
</html>
