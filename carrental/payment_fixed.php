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

        // Attach selected payment method and extra info
        $selected_method = isset($_POST['selected_method']) ? $_POST['selected_method'] : 'card';
        $post_data['selected_method'] = $selected_method;
        if ($selected_method === 'card') {
            $post_data['card_number'] = isset($_POST['card_number']) ? $_POST['card_number'] : '';
            $post_data['cardholder_name'] = isset($_POST['cardholder_name']) ? $_POST['cardholder_name'] : '';
            $post_data['expiry'] = isset($_POST['expiry']) ? $_POST['expiry'] : '';
            $post_data['cvv'] = isset($_POST['cvv']) ? $_POST['cvv'] : '';
        } elseif ($selected_method === 'bkash') {
            $post_data['bkash_number'] = isset($_POST['bkash_number']) ? $_POST['bkash_number'] : '';
        } elseif ($selected_method === 'nagad') {
            $post_data['nagad_number'] = isset($_POST['nagad_number']) ? $_POST['nagad_number'] : '';
        } elseif ($selected_method === 'rocket') {
            $post_data['rocket_number'] = isset($_POST['rocket_number']) ? $_POST['rocket_number'] : '';
        }
        // You may want to log or store these details for audit, but do not send sensitive card data to SSLCommerz in production!
        
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
.payment-method {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    margin: 15px 0;
    transition: all 0.3s ease;
    cursor: pointer;
}
.payment-method:hover {
    border-color: #007bff;
    box-shadow: 0 2px 8px rgba(0,123,255,0.2);
}
.payment-method.selected {
    border-color: #007bff;
    background: #f0f8ff;
}
.ssl-logo {
    max-height: 60px;
    margin: 10px 0;
}
.security-info {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    border-radius: 8px;
    padding: 15px;
    margin: 20px 0;
}
/* Make payment-method-card clickable and highlight on hover/selected */
.payment-method-card {
    cursor: pointer;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    margin-bottom: 15px;
    transition: border-color 0.3s, box-shadow 0.3s, background 0.3s;
}
.payment-method-card:hover {
    border-color: #007bff;
    box-shadow: 0 2px 8px rgba(0,123,255,0.15);
    background: #f8fbff;
}
.payment-method-card.selected {
    border-color: #007bff;
    background: #f0f8ff;
    box-shadow: 0 2px 8px rgba(0,123,255,0.18);
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
                <div class="payment-card">
                    <h3 class="text-center">
                        <i class="fa fa-credit-card"></i> Secure Payment
                    </h3>
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Payment Method Selection -->
                    <div class="row payment-methods-list text-center" style="margin-bottom: 30px;">
                        <div class="col-md-3 col-xs-6">
                            <div class="card payment-method-card" data-method="card">
                                <div class="card-body">
                                    <img src="https://www.sslcommerz.com/wp-content/uploads/2019/11/payment_gateways_new-01.png" style="max-width: 100%; height: auto;" alt="Visa/Mastercard">
                                    <div>Visa/Mastercard</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <div class="card payment-method-card" data-method="bkash">
                                <div class="card-body">
                                    <img src="https://seeklogo.com/images/B/bkash-logo-835789094D-seeklogo.com.png" style="max-width: 100%; height: auto; max-height: 40px;" alt="bKash">
                                    <div>bKash</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <div class="card payment-method-card" data-method="nagad">
                                <div class="card-body">
                                    <img src="https://download.logo.wine/logo/Nagad/Nagad-Logo.wine.png" style="max-width: 100%; height: auto; max-height: 40px;" alt="Nagad">
                                    <div>Nagad</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <div class="card payment-method-card" data-method="rocket">
                                <div class="card-body">
                                    <img src="https://seeklogo.com/images/R/rocket-logo-6B41BBACAB-seeklogo.com.png" style="max-width: 100%; height: auto; max-height: 40px;" alt="Rocket">
                                    <div>Rocket</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Forms -->
                    <form method="post" id="payment-form">
                        <input type="hidden" name="selected_method" id="selected_method" value="">
                        <!-- Card Payment Form -->
                        <div id="card-section" class="method-section" style="display:none;">
                            <div class="form-group">
                                <input type="text" class="form-control" name="card_number" placeholder="Card Number (16 digits)" maxlength="16" pattern="[0-9]{16}" required>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" name="cardholder_name" placeholder="Cardholder Name" required>
                            </div>
                            <div class="form-group row">
                                <div class="col-xs-6">
                                    <input type="text" class="form-control" name="expiry" placeholder="MM/YY" maxlength="5" pattern="(0[1-9]|1[0-2])\/([0-9]{2})" required>
                                </div>
                                <div class="col-xs-6">
                                    <input type="text" class="form-control" name="cvv" placeholder="CVV" maxlength="4" pattern="[0-9]{3,4}" required>
                                </div>
                            </div>
                        </div>
                        <!-- bKash Payment Form -->
                        <div id="bkash-section" class="method-section" style="display:none;">
                            <div class="form-group">
                                <input type="tel" class="form-control" name="bkash_number" id="bkash-number" placeholder="bKash Number (01XXXXXXXXX)" maxlength="11" pattern="01[0-9]{9}">
                            </div>
                        </div>
                        <!-- Nagad Payment Form -->
                        <div id="nagad-section" class="method-section" style="display:none;">
                            <div class="form-group">
                                <input type="tel" class="form-control" name="nagad_number" id="nagad-number" placeholder="Nagad Number (01XXXXXXXXX)" maxlength="11" pattern="01[0-9]{9}">
                            </div>
                        </div>
                        <!-- Rocket Payment Form -->
                        <div id="rocket-section" class="method-section" style="display:none;">
                            <div class="form-group">
                                <input type="tel" class="form-control" name="rocket_number" id="rocket-number" placeholder="Rocket Number (01XXXXXXXXX)" maxlength="11" pattern="01[0-9]{9}">
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" name="pay_now" class="btn btn-success btn-lg" style="padding: 15px 50px; font-size: 18px;">
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
    // Hide all method sections and remove selection on load
    $('.method-section').hide();
    $('.payment-method-card').removeClass('selected');
    $('#selected_method').val('');
    // Payment method selection logic
    $('.payment-method-card').on('click', function() {
        var method = $(this).data('method');
        $('.payment-method-card').removeClass('selected');
        $(this).addClass('selected');
        $('.method-section').hide();
        $('#' + method + '-section').show();
        $('#selected_method').val(method);
        // Set required fields dynamically
        $('input[name="card_number"], input[name="cardholder_name"], input[name="expiry"], input[name="cvv"]').prop('required', method === 'card');
        $('input[name="bkash_number"]').prop('required', method === 'bkash');
        $('input[name="nagad_number"]').prop('required', method === 'nagad');
        $('input[name="rocket_number"]').prop('required', method === 'rocket');
    });
    // Mobile number input: auto-format and validate
    function setupMobileNumberInput(selector) {
        $(selector).on('input', function() {
            let val = $(this).val();
            val = val.replace(/[^0-9]/g, '');
            if(val.length > 11) val = val.slice(0, 11);
            $(this).val(val);
        });
    }
    setupMobileNumberInput('#bkash-number');
    setupMobileNumberInput('#nagad-number');
    setupMobileNumberInput('#rocket-number');
    // Animate amount on page load
    $('.amount-highlight').hide().fadeIn(1000);
});
</script>

</body>
</html>
