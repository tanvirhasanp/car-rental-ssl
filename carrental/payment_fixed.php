<?php 
session_start();
include('includes/config.php');
include('includes/sslcommerz_config.php');
include('includes/SSLCommerz.php');
error_reporting(0);

// Check if user is logged in
if(!isset($_SESSION['login'])) {
    header('location: index.php');
    exit();
}

// Get the latest booking details for the current user
$userEmail = $_SESSION['login'];
$sql = "SELECT tblbooking.*, tblvehicles.VehiclesTitle, tblvehicles.PricePerDay, 
        tblbrands.BrandName, DATEDIFF(tblbooking.ToDate, tblbooking.FromDate) as totalDays,
        tblusers.FullName, tblusers.ContactNo, tblusers.Address
        FROM tblbooking 
        JOIN tblvehicles ON tblbooking.VehicleId = tblvehicles.id 
        JOIN tblbrands ON tblvehicles.VehiclesBrand = tblbrands.id
        JOIN tblusers ON tblbooking.userEmail = tblusers.EmailId
        WHERE tblbooking.userEmail = :email 
        ORDER BY tblbooking.id DESC LIMIT 1";

$query = $dbh->prepare($sql);
$query->bindParam(':email', $userEmail, PDO::PARAM_STR);
$query->execute();
$booking = $query->fetch(PDO::FETCH_OBJ);

if(!$booking) {
    header('location: car-listing.php');
    exit();
}

// Calculate total amount
$totalAmount = $booking->totalDays * $booking->PricePerDay;

// Store booking details in session
$_SESSION['booking_id'] = $booking->id;
$_SESSION['booking_number'] = $booking->BookingNumber;
$_SESSION['booking_amount'] = $totalAmount;

// Handle SSL Commerz payment initiation
if(isset($_POST['pay_now'])) {
    
    if(!SSLCZ_ENABLED) {
        $error = "Payment gateway is currently disabled. Please try again later.";
    } else {
        // Prepare SSL Commerz payment data
        $post_data = array();
        $post_data['total_amount'] = $totalAmount;
        $post_data['currency'] = CURRENCY;
        $post_data['tran_id'] = $booking->BookingNumber . '_' . time();
        $post_data['success_url'] = SUCCESS_URL;
        $post_data['fail_url'] = FAIL_URL;
        $post_data['cancel_url'] = CANCEL_URL;
        $post_data['ipn_url'] = IPN_URL;
        
        // Customer information
        $post_data['cus_name'] = $booking->FullName;
        $post_data['cus_email'] = $userEmail;
        $post_data['cus_add1'] = $booking->Address;
        $post_data['cus_phone'] = $booking->ContactNo;
        $post_data['cus_city'] = "Dhaka";
        $post_data['cus_country'] = "Bangladesh";
        
        // Product information
        $post_data['product_name'] = $booking->BrandName . ' ' . $booking->VehiclesTitle;
        $post_data['product_category'] = "Car Rental";
        $post_data['product_profile'] = "general";
        
        // Shipping information
        $post_data['ship_name'] = $booking->FullName;
        $post_data['ship_add1'] = $booking->Address;
        $post_data['ship_city'] = "Dhaka";
        $post_data['ship_country'] = "Bangladesh";
        $post_data['shipping_method'] = 'NO';
        $post_data['num_of_item'] = 1;
        
        // Initialize SSL Commerz
        $sslcommerz = new SSLCommerz(STORE_ID, STORE_PASSWORD);
        $response = $sslcommerz->makePayment($post_data);
        
        if($response['status'] == 'SUCCESS') {
            // Store transaction ID in session for validation
            $_SESSION['tran_id'] = $post_data['tran_id'];
            
            // Update booking with transaction ID
            $updateSql = "UPDATE tblbooking SET transaction_id = :tran_id WHERE id = :booking_id";
            $updateQuery = $dbh->prepare($updateSql);
            $updateQuery->bindParam(':tran_id', $post_data['tran_id'], PDO::PARAM_STR);
            $updateQuery->bindParam(':booking_id', $booking->id, PDO::PARAM_STR);
            $updateQuery->execute();
            
            // Redirect to SSL Commerz payment gateway
            header('Location: ' . $response['data']);
            exit();
        } else {
            $error = "Failed to initialize payment. Please try again.";
        }
    }
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
<title>Payment - Car Rental Portal</title>
<!--Bootstrap -->
<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="assets/css/style.css" type="text/css">
<link rel="stylesheet" href="assets/css/font-awesome.min.css" type="text/css">
<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet"> 
<style>
.payment-container {
    min-height: 600px;
    padding: 50px 0;
}
.payment-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    padding: 40px;
    margin: 20px 0;
}
.booking-summary {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 30px;
    margin-bottom: 30px;
}
.amount-highlight {
    font-size: 2.5em;
    font-weight: bold;
    color: #28a745;
    text-align: center;
    margin: 20px 0;
}
.security-info {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    border-radius: 8px;
    padding: 15px;
    margin: 20px 0;
}
.ssl-logo {
    max-height: 60px;
    margin: 10px 0;
}
.pay-button {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    border-radius: 50px;
    padding: 20px 60px;
    font-size: 20px;
    font-weight: bold;
    color: white;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}
.pay-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
    color: white;
    text-decoration: none;
}
.pay-button:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.3);
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
        <h1>Complete Your Payment</h1>
      </div>
      <ul class="coustom-breadcrumb">
        <li><a href="index.php">Home</a></li>
        <li><a href="my-booking.php">My Bookings</a></li>
        <li>Payment</li>
      </ul>
    </div>
  </div>
  <div class="dark-overlay"></div>
</section>

<section class="payment-container">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                
                <!-- Booking Summary -->
                <div class="booking-summary">
                    <h3 class="text-center">
                        <i class="fa fa-car"></i> Booking Summary
                    </h3>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Booking #:</strong> <?php echo htmlentities($booking->BookingNumber); ?></p>
                            <p><strong>Vehicle:</strong> <?php echo htmlentities($booking->BrandName . ' ' . $booking->VehiclesTitle); ?></p>
                            <p><strong>Customer:</strong> <?php echo htmlentities($booking->FullName); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>From Date:</strong> <?php echo htmlentities($booking->FromDate); ?></p>
                            <p><strong>To Date:</strong> <?php echo htmlentities($booking->ToDate); ?></p>
                            <p><strong>Total Days:</strong> <?php echo htmlentities($booking->totalDays); ?> days</p>
                            <p><strong>Rate per Day:</strong> <?php echo CURRENCY_SYMBOL . number_format($booking->PricePerDay, 2); ?></p>
                        </div>
                    </div>
                    
                    <div class="amount-highlight">
                        Total Amount: <?php echo CURRENCY_SYMBOL . number_format($totalAmount, 2); ?>
                    </div>
                </div>

                <!-- Payment Form -->
                <!-- <div class="payment-card">
                    <h3 class="text-center">
                        <i class="fa fa-credit-card"></i> Secure Payment
                    </h3>
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle"></i> <?php echo $error; ?>
                        </div> -->
                    <?php endif; ?>

                    <!-- SSL Commerz Logo and Security Info -->
                    <!-- <div class="text-center" style="margin: 30px 0;">
                        <img src="https://www.sslcommerz.com/wp-content/uploads/2019/11/payment_gateways_new-01.png" 
                             alt="SSL Commerz" class="ssl-logo">
                        <div class="security-info">
                            <i class="fa fa-shield"></i>
                            <strong>Secure Payment Gateway</strong><br>
                            Your payment information is protected with bank-level security
                        </div>
                    </div> -->

                    <!-- Simple Payment Form -->
                    <form method="post" id="payment-form">
                        <div class="text-center">
                            <button type="submit" name="pay_now" class="btn pay-button">
                                <i class="fa fa-lock"></i> Pay <?php echo CURRENCY_SYMBOL . number_format($totalAmount, 2); ?> Securely
                            </button>
                        </div>
                        <div class="text-center" style="margin-top: 20px;">
                            <a href="my-booking.php" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Back to My Bookings
                            </a>
                        </div>
                    </form>
                    
                    <!-- Additional Information -->
                    <div style="margin-top: 30px; text-align: center; color: #666;">
                        <small>
                            <i class="fa fa-info-circle"></i>
                            You will be redirected to SSL Commerz secure payment gateway to complete your payment.
                            After successful payment, your booking will be confirmed automatically.
                        </small>
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
    // Animate amount on page load
    $('.amount-highlight').hide().fadeIn(1000);
});
</script>

</body>
</html>
