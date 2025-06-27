<?php 
session_start();
include('includes/config.php');
include('includes/payment_config.php');
error_reporting(0);

// Get booking details from session or URL parameters
$booking_amount = $_SESSION['booking_amount'] ?? ($_GET['amount'] ?? 100);
$booking_number = $_SESSION['booking_number'] ?? ($_GET['booking_id'] ?? 'BK' . time());

// Store in session for payment processing
$_SESSION['booking_amount'] = $booking_amount;
$_SESSION['booking_number'] = $booking_number;
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<title>Payment Methods - Car Rental Portal</title>
<!--Bootstrap -->
<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="assets/css/style.css" type="text/css">
<link rel="stylesheet" href="assets/css/font-awesome.min.css" type="text/css">
<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet"> 
<style>
.payment-method-card {
  margin-bottom: 30px;
  transition: box-shadow 0.3s;
}
.payment-method-card.selected {
  box-shadow: 0 0 10px #007bff;
  border: 2px solid #007bff;
}
.payment-methods-list .card {
  cursor: pointer;
  border: 2px solid transparent;
  transition: border 0.2s;
}
.payment-methods-list .card.selected {
  border: 2px solid #007bff;
}
#otpModal .modal-content {
  text-align: center;
}
#otp-timer {
  font-weight: bold;
  color: #007bff;
}
.payment-methods-list .card-body img {
  display: block;
  margin: 0 auto 8px auto;
  max-width: 60px;
  max-height: 40px;
}
.payment-methods-list .card.selected {
  border: 2px solid #007bff;
  box-shadow: 0 0 10px #007bff;
  background: #f0f8ff;
}
.payment-methods-list .card {
  transition: border 0.2s, box-shadow 0.2s, background 0.2s;
}
</style>
</head>
<body>
<!--Header-->
<?php include('includes/header.php');?>
<!-- /Header -->

<?php if (PAYMENT_TEST_MODE): ?>
<div class="alert alert-warning" style="margin: 0; border-radius: 0; text-align: center;">
    <i class="fa fa-warning"></i> 
    <strong>SANDBOX MODE:</strong> This is a test environment. No real payments will be processed.
    Use test card 4242424242424242 for Stripe and OTP 123456 for mobile payments.
</div>
<?php endif; ?>

<section class="section-padding gray-bg">
  <div class="container">
    <div class="section-header text-center">
      <h2>Payment <span>Methods</span></h2>
      <p>Choose your preferred payment method for a seamless car rental experience.</p>
      <?php if(isset($_SESSION['booking_amount']) && isset($_SESSION['booking_number'])): ?>
      <div class="alert alert-info" style="max-width: 600px; margin: 20px auto;">
        <strong>Booking Details:</strong><br>
        <strong>Booking #:</strong> <?php echo htmlentities($_SESSION['booking_number']); ?> &nbsp; | &nbsp;
        <strong>Amount:</strong> <?php echo CURRENCY_SYMBOL . number_format($_SESSION['booking_amount'], 2); ?>
      </div>
      <?php endif; ?>
    </div>
    <div class="row payment-methods-list text-center" style="margin-bottom: 40px;">
      <div class="col-md-2 col-xs-6">
        <div class="card" data-method="stripe">
          <div class="card-body">
            <img src="https://upload.wikimedia.org/wikipedia/commons/b/ba/Stripe_Logo%2C_revised_2016.svg" alt="Stripe" style="height:40px; margin-bottom:8px;">
            <div>Credit/Debit</div>
          </div>
        </div>
      </div>
      <div class="col-md-2 col-xs-6">
        <div class="card" data-method="paypal">
          <div class="card-body">
            <img src="https://upload.wikimedia.org/wikipedia/commons/a/a4/Paypal_2014_logo.png" alt="PayPal" style="height:40px; margin-bottom:8px;">
            <div>PayPal</div>
          </div>
        </div>
      </div>
      <div class="col-md-2 col-xs-6">
        <div class="card" data-method="bkash">
          <div class="card-body">
            <img src="https://banner2.cleanpng.com/lnd/20241225/fq/77508cc5e260af30c8590c31cb25ef.webp" alt="bKash" style="height:40px; margin-bottom:8px;">
            <div>bKash</div>
          </div>
        </div>
      </div>
      <div class="col-md-2 col-xs-6">
        <div class="card" data-method="nagad">
          <div class="card-body">
            <img src="https://download.logo.wine/logo/Nagad/Nagad-Logo.wine.png" alt="Nagad" style="height:40px; margin-bottom:8px;">
            <div>Nagad</div>
          </div>
        </div>
      </div>
      <div class="col-md-2 col-xs-6">
        <div class="card" data-method="rocket">
          <div class="card-body">
            <img src="https://upload.wikimedia.org/wikipedia/commons/e/e9/Rocket_ddbl.png" alt="Rocket" style="height:40px; margin-bottom:8px;">
            <div>Rocket</div>
          </div>
        </div>
      </div>
      <div class="col-md-2 col-xs-6">
        <div class="card" data-method="cod">
          <div class="card-body">
            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Cash" style="height:40px; margin-bottom:8px;">
            <div>Cash on Delivery</div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <!-- Payment method sections will be shown here dynamically -->
      </div>
      <div class="col-md-2 col-xs-6">
        <div class="card" data-method="nagad">
          <div class="card-body">
            <img src="https://download.logo.wine/logo/Nagad/Nagad-Logo.wine.png" alt="Nagad" style="height:40px; margin-bottom:8px;">
            <div>Nagad</div>
          </div>
        </div>
      </div>
      <div class="col-md-2 col-xs-6">
        <div class="card" data-method="rocket">
          <div class="card-body">
            <img src="https://upload.wikimedia.org/wikipedia/commons/e/e9/Rocket_ddbl.png" alt="Rocket" style="height:40px; margin-bottom:8px;">
            <div>Rocket</div>
          </div>
        </div>
      </div>
      <div class="col-md-2 col-xs-6">
        <div class="card" data-method="card">
          <div class="card-body">
            <img src="https://upload.wikimedia.org/wikipedia/commons/4/41/Visa_Logo.png" alt="Visa" style="height:28px; margin-bottom:2px; margin-right:4px;">
            <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Mastercard-logo.png" alt="MasterCard" style="height:28px; margin-bottom:2px; margin-right:4px;">
            <!-- <img src="https://upload.wikimedia.org/wikipedia/commons/3/30/American_Express_logo_%282018%29.svg" alt="Amex" style="height:28px; margin-bottom:2px;"> -->
            <div>Card</div>
          </div>
        </div>
      </div>
      <div class="col-md-2 col-xs-6">
        <div class="card" data-method="cod">
          <div class="card-body">
            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Cash" style="height:40px; margin-bottom:8px;">
            <div>Cash</div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <!-- Stripe Payment -->
      <div class="col-md-8 col-md-offset-2" id="stripe-section" style="display:none; position:relative;">
        <div class="card payment-method-card">
          <div class="card-body text-center">
            <button type="button" class="close close-method-section" aria-label="Close" style="position:absolute;right:20px;top:20px;font-size:28px;z-index:2;">
              <span aria-hidden="true">&times;</span>
            </button>
            <img src="https://upload.wikimedia.org/wikipedia/commons/b/ba/Stripe_Logo%2C_revised_2016.svg" alt="Stripe" style="height:48px; margin-bottom:12px;">
            <h4 class="margin-top-20">Credit/Debit Card Payment</h4>
            <?php if(isset($_SESSION['booking_amount']) && isset($_SESSION['booking_number'])): ?>
            <div class="alert alert-info" style="margin-bottom:15px;">
              <strong>Booking #:</strong> <?php echo htmlentities($_SESSION['booking_number']); ?> &nbsp; | &nbsp;
              <strong>Amount:</strong> <?php echo CURRENCY_SYMBOL . number_format($_SESSION['booking_amount'], 2); ?>
            </div>
            <?php endif; ?>
            <?php if(PAYMENT_TEST_MODE): ?>
            <div class="alert alert-warning" style="margin-bottom:15px; font-size:12px;">
              <strong>Test Mode:</strong> Use card number <code>4242424242424242</code> with any future expiry and any 3-digit CVC.
            </div>
            <?php endif; ?>
            <form id="stripe-form">
              <div class="form-group">
                <div class="input-group" style="border-radius: 30px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.07);">
                  <span class="input-group-addon" style="background: #f8f8f8; border: none; border-radius: 30px 0 0 30px; font-size: 18px;"><i class="fa fa-credit-card"></i></span>
                  <input type="tel" class="form-control" id="stripe-card-number" placeholder="Card Number" maxlength="19" required 
                  style="border: none; border-radius: 0 30px 30px 0; font-size: 18px; padding: 16px 18px; box-shadow: none;">
                </div>
              </div>
              <div class="form-group">
                <div class="input-group" style="border-radius: 30px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.07);">
                  <span class="input-group-addon" style="background: #f8f8f8; border: none; border-radius: 30px 0 0 30px; font-size: 18px;"><i class="fa fa-user"></i></span>
                  <input type="text" class="form-control" id="stripe-cardholder-name" placeholder="Cardholder Name" required 
                  style="border: none; border-radius: 0 30px 30px 0; font-size: 18px; padding: 16px 18px; box-shadow: none;">
                </div>
              </div>
              <div class="form-group row">
                <div class="col-xs-6">
                  <div class="input-group" style="border-radius: 30px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.07);">
                    <span class="input-group-addon" style="background: #f8f8f8; border: none; border-radius: 30px 0 0 30px; font-size: 18px;"><i class="fa fa-calendar"></i></span>
                    <input type="tel" class="form-control" id="stripe-expiry" placeholder="MM/YY" maxlength="5" required 
                    style="border: none; border-radius: 0 30px 30px 0; font-size: 18px; padding: 16px 18px; box-shadow: none;">
                  </div>
                </div>
                <div class="col-xs-6">
                  <div class="input-group" style="border-radius: 30px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.07);">
                    <span class="input-group-addon" style="background: #f8f8f8; border: none; border-radius: 30px 0 0 30px; font-size: 18px;"><i class="fa fa-lock"></i></span>
                    <input type="tel" class="form-control" id="stripe-cvc" placeholder="CVC" maxlength="4" required 
                    style="border: none; border-radius: 0 30px 30px 0; font-size: 18px; padding: 16px 18px; box-shadow: none;">
                  </div>
                </div>
              </div>
              <button type="submit" class="btn btn-primary" id="stripe-pay-btn">
                <i class="fa fa-credit-card"></i> Pay <?php echo CURRENCY_SYMBOL . number_format($_SESSION['booking_amount'] ?? 0, 2); ?>
              </button>
              <div id="stripe-feedback" style="margin-top:10px;"></div>
            </form>
          </div>
        </div>
      </div>
      <!-- bKash -->
      <div class="col-md-8 col-md-offset-2" id="bkash-section" style="display:none; position:relative;">
        <div class="card payment-method-card">
          <div class="card-body text-center">
            <button type="button" class="close close-method-section" aria-label="Close" style="position:absolute;right:20px;top:20px;font-size:28px;z-index:2;">
              <span aria-hidden="true">&times;</span>
            </button>
            <img src="https://banner2.cleanpng.com/lnd/20241225/fq/77508cc5e260af30c8590c31cb25ef.webp" alt="bKash" style="height:48px; margin-bottom:12px;">
            <h4 class="margin-top-20">bKash Payment</h4>
            <?php if(isset($_SESSION['booking_amount']) && isset($_SESSION['booking_number'])): ?>
            <div class="alert alert-info" style="margin-bottom:15px;">
              <strong>Booking #:</strong> <?php echo htmlentities($_SESSION['booking_number']); ?> &nbsp; | &nbsp;
              <strong>Amount:</strong> ৳<?php echo htmlentities($_SESSION['booking_amount']); ?>
            </div>
            <?php endif; ?>
            <div style="margin-bottom:10px; color:#d81b60; font-weight:500;">Please enter your 11-digit bKash number (format: 01XXXXXXXXX) to proceed. You will receive an OTP for verification.</div>
            <form id="bkash-form" onsubmit="return false;">
              <div class="form-group">
                <div class="input-group" style="border-radius: 30px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.07);">
                  <span class="input-group-addon" style="background: #f8f8f8; border: none; border-radius: 30px 0 0 30px; font-size: 18px;"><i class="fa fa-phone"></i></span>
                  <input type="tel" pattern="01[0-9]{9}" class="form-control" id="bkash-number" placeholder="01XXXXXXXXX" maxlength="11" required
                  style="border: none; border-radius: 0 30px 30px 0; font-size: 18px; padding: 16px 18px; box-shadow: none;" value="<?php echo isset($_SESSION['bkash_number']) ? htmlentities($_SESSION['bkash_number']) : ''; ?>">
                </div>
              </div>
              <button type="button" class="btn btn-primary" id="request-otp">Request OTP</button>
              <div id="bkash-feedback" style="margin-top:10px;"></div>
            </form>
          </div>
        </div>
      </div>
      <!-- Nagad -->
      <div class="col-md-8 col-md-offset-2" id="nagad-section" style="display:none; position:relative;">
        <div class="card payment-method-card">
          <div class="card-body text-center">
            <button type="button" class="close close-method-section" aria-label="Close" style="position:absolute;right:20px;top:20px;font-size:28px;z-index:2;">
              <span aria-hidden="true">&times;</span>
            </button>
            <img src="https://download.logo.wine/logo/Nagad/Nagad-Logo.wine.png" alt="Nagad" style="height:48px; margin-bottom:12px;">
            <h4 class="margin-top-20">Nagad Payment</h4>
            <p>Make payments easily through Nagad, another popular mobile financial service in Bangladesh.</p>
            <form id="nagad-form" onsubmit="return false;">
              <div class="form-group">
                <div class="input-group" style="border-radius: 30px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.07);">
                  <span class="input-group-addon" style="background: #f8f8f8; border: none; border-radius: 30px 0 0 30px; font-size: 18px;"><i class="fa fa-phone"></i></span>
                  <input type="tel" pattern="01[0-9]{9}" class="form-control" id="nagad-number" placeholder="01XXXXXXXXX" maxlength="11" required
                  style="border: none; border-radius: 0 30px 30px 0; font-size: 18px; padding: 16px 18px; box-shadow: none;">
                </div>
              </div>
              <button type="button" class="btn btn-warning" id="request-otp-nagad">Request OTP</button>
              <div id="nagad-feedback" style="margin-top:10px;"></div>
            </form>
          </div>
        </div>
      </div>
      <!-- Rocket -->
      <div class="col-md-8 col-md-offset-2" id="rocket-section" style="display:none; position:relative;">
        <div class="card payment-method-card">
          <div class="card-body text-center">
            <button type="button" class="close close-method-section" aria-label="Close" style="position:absolute;right:20px;top:20px;font-size:28px;z-index:2;">
              <span aria-hidden="true">&times;</span>
            </button>
            <img src="https://upload.wikimedia.org/wikipedia/commons/e/e9/Rocket_ddbl.png" alt="Rocket" style="height:48px; margin-bottom:12px;">
            <h4 class="margin-top-20">Rocket Payment</h4>
            <p>Pay via Rocket, the mobile banking service from Dutch-Bangla Bank.</p>
            <form id="rocket-form" onsubmit="return false;">
              <div class="form-group">
                <div class="input-group" style="border-radius: 30px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.07);">
                  <span class="input-group-addon" style="background: #f8f8f8; border: none; border-radius: 30px 0 0 30px; font-size: 18px;"><i class="fa fa-phone"></i></span>
                  <input type="tel" pattern="01[0-9]{9}" class="form-control" id="rocket-number" placeholder="01XXXXXXXXX" maxlength="11" required
                  style="border: none; border-radius: 0 30px 30px 0; font-size: 18px; padding: 16px 18px; box-shadow: none;">
                </div>
              </div>
              <button type="button" class="btn btn-info" id="request-otp-rocket">Request OTP</button>
              <div id="rocket-feedback" style="margin-top:10px;"></div>
            </form>
          </div>
        </div>
      </div>
      <!-- Card Payment -->
      <div class="col-md-8 col-md-offset-2" id="card-section" style="display:none; position:relative;">
        <div class="card payment-method-card">
          <div class="card-body text-center">
            <button type="button" class="close close-method-section" aria-label="Close" style="position:absolute;right:20px;top:20px;font-size:28px;z-index:2;">
              <span aria-hidden="true">&times;</span>
            </button>
            <img src="https://upload.wikimedia.org/wikipedia/commons/4/41/Visa_Logo.png" alt="Visa" style="height:32px; margin-bottom:2px; margin-right:4px;">
            <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Mastercard-logo.png" alt="MasterCard" style="height:32px; margin-bottom:2px; margin-right:4px;">
            <img src="https://upload.wikimedia.org/wikipedia/commons/3/30/American_Express_logo_%282018%29.svg" alt="Amex" style="height:32px; margin-bottom:2px;">
            <h4 class="margin-top-20">Card Payment</h4>
            <p>We accept Visa and MasterCard Pay online with your debit or credit card.</p>
            <form id="card-form" onsubmit="return false;">
              <div class="form-group">
                <div class="input-group" style="border-radius: 30px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.07);">
                  <span class="input-group-addon" style="background: #f8f8f8; border: none; border-radius: 30px 0 0 30px; font-size: 18px;"><i class="fa fa-credit-card"></i></span>
                  <input type="tel" class="form-control" placeholder="Card Number" maxlength="16" required style="border: none; border-radius: 0 30px 30px 0; font-size: 18px; padding: 16px 18px; box-shadow: none;">
                </div>
              </div>
              <div class="form-group">
                <div class="input-group" style="border-radius: 30px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.07);">
                  <span class="input-group-addon" style="background: #f8f8f8; border: none; border-radius: 30px 0 0 30px; font-size: 18px;"><i class="fa fa-user"></i></span>
                  <input type="text" class="form-control" placeholder="Cardholder Name" required style="border: none; border-radius: 0 30px 30px 0; font-size: 18px; padding: 16px 18px; box-shadow: none;">
                </div>
              </div>
              <div class="form-group row">
                <div class="col-xs-6">
                  <div class="input-group" style="border-radius: 30px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.07);">
                    <span class="input-group-addon" style="background: #f8f8f8; border: none; border-radius: 30px 0 0 30px; font-size: 18px;"><i class="fa fa-calendar"></i></span>
                    <input type="tel" class="form-control" placeholder="MM/YY" maxlength="5" required style="border: none; border-radius: 0 30px 30px 0; font-size: 18px; padding: 16px 18px; box-shadow: none;">
                  </div>
                </div>
                <div class="col-xs-6">
                  <div class="input-group" style="border-radius: 30px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.07);">
                    <span class="input-group-addon" style="background: #f8f8f8; border: none; border-radius: 30px 0 0 30px; font-size: 18px;"><i class="fa fa-lock"></i></span>
                    <input type="tel" class="form-control" placeholder="CVV" maxlength="4" required style="border: none; border-radius: 0 30px 30px 0; font-size: 18px; padding: 16px 18px; box-shadow: none;">
                  </div>
                </div>
              </div>
              <button type="submit" class="btn btn-primary">Pay Now</button>
            </form>
          </div>
        </div>
      </div>
      <!-- Cash on Delivery -->
      <div class="col-md-8 col-md-offset-2" id="cod-section" style="display:none; position:relative;">
        <div class="card payment-method-card">
          <div class="card-body text-center">
            <button type="button" class="close close-method-section" aria-label="Close" style="position:absolute;right:20px;top:20px;font-size:28px;z-index:2;">
              <span aria-hidden="true">&times;</span>
            </button>
            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Cash" style="height:48px; margin-bottom:12px;">
            <h4 class="margin-top-20">Cash on Delivery</h4>
            <p>Prefer to pay in cash? Choose cash on delivery and pay when you receive your car.</p>
            <button class="btn btn-success">Confirm Cash on Delivery</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- OTP Modal -->
<div class="modal fade" id="otpModal" tabindex="-1" role="dialog" aria-labelledby="otpModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="otpModalLabel">Enter OTP</h4>
      </div>
      <div class="modal-body">
        <p>We have sent a 6-digit OTP to your mobile number.<br><span id="otp-timer">30</span> seconds left</p>
        <input type="text" class="form-control" id="otp-input" maxlength="6" placeholder="Enter OTP">
        <div id="otp-modal-feedback" style="margin-top:10px;"></div>
        <button type="button" class="btn btn-primary" id="verify-otp-btn">Verify OTP</button>
        <button type="button" class="btn btn-link" id="resend-otp-btn" disabled>Resend OTP</button>
      </div>
    </div>
  </div>
</div>

<!--Footer-->
<?php include('includes/footer.php');?>
<!-- /Footer -->

<!-- Scripts -->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script>
$(function() {
  var selectedMethod = null;
  var otpData = { method: '', number: '', otp: '' };
  var otpTimer = null, otpTimeLeft = 30;

  // Mobile number input: auto-format and validate
  function setupMobileNumberInput(selector) {
    $(selector).on('input', function() {
      let val = $(this).val();
      val = val.replace(/[^0-9]/g, '');
      if(val.length > 11) val = val.slice(0, 11);
      if(val.length > 0 && !val.startsWith('')) {
        val = '01'; 
      }
      $(this).val(val);
    });
  }
  setupMobileNumberInput('#bkash-number');
  setupMobileNumberInput('#nagad-number');
  setupMobileNumberInput('#rocket-number');

  // Show only the selected payment section (dynamic)
  function showSection(method) {
    $('[id$="-section"]').stop(true, true).hide(); // Hide all sections ending with -section
    $('#' + method + '-section').fadeIn(250);
    $('.payment-methods-list .card').removeClass('selected');
    $('.payment-methods-list .card[data-method="' + method + '"]').addClass('selected');
    selectedMethod = method;
  }

  // Handle click for all payment method cards (dynamic)
  $('.payment-methods-list').on('click', '.card', function() {
    var method = $(this).data('method');
    showSection(method);
  });

  // Use event delegation for close button logic (dynamic)
  $(document).on('click', '.close-method-section', function() {
    $(this).closest('.payment-method-card').fadeOut(200);
    $('.payment-methods-list .card').removeClass('selected');
    selectedMethod = null;
  });

  // OTP Modal logic (unchanged)
  function openOtpModal(method, number, callback) {
    otpData.method = method;
    otpData.number = number;
    otpData.otp = (Math.floor(100000 + Math.random() * 900000)).toString();
    $('#otp-input').val('');
    $('#otp-modal-feedback').html('');
    $('#otpModal').modal('show');
    $('#otpModalLabel').text('Enter OTP for ' + method.charAt(0).toUpperCase() + method.slice(1));
    startOtpTimer();
    // For demo, show OTP in feedback
    $('#otp-modal-feedback').html('<span style="color:blue;">Demo OTP: ' + otpData.otp + '</span>');
    $('#verify-otp-btn').off('click').on('click', function() {
      var entered = $('#otp-input').val();
      if(entered === otpData.otp) {
        $('#otp-modal-feedback').html('<span style="color:green;">OTP verified! Payment can proceed.</span>');
        setTimeout(function() {
          $('#otpModal').modal('hide');
          window.location = 'my-booking.php';
        }, 800);
        if(callback) callback(true);
      } else {
        $('#otp-modal-feedback').html('<span style="color:red;">Invalid OTP. Please try again.</span>');
      }
    });
    $('#resend-otp-btn').prop('disabled', true).off('click').on('click', function() {
      otpData.otp = (Math.floor(100000 + Math.random() * 900000)).toString();
      $('#otp-modal-feedback').html('<span style="color:blue;">New OTP sent! (Demo: ' + otpData.otp + ')</span>');
      $('#otp-input').val('');
      startOtpTimer();
    });
  }

  function startOtpTimer() {
    otpTimeLeft = 30;
    $('#otp-timer').text(otpTimeLeft);
    $('#resend-otp-btn').prop('disabled', true);
    if(otpTimer) clearInterval(otpTimer);
    otpTimer = setInterval(function() {
      otpTimeLeft--;
      $('#otp-timer').text(otpTimeLeft);
      if(otpTimeLeft <= 0) {
        clearInterval(otpTimer);
        $('#resend-otp-btn').prop('disabled', false);
        $('#otp-timer').text('0');
      }
    }, 1000);
  }

  // bKash OTP
  $('#request-otp').click(function() {
    var number = $('#bkash-number').val();
    if(number.length !== 11 || !/^01[0-9]{9}$/.test(number)) {
      $('#bkash-feedback').html('<span style="color:red;">Please enter a valid 11-digit bKash number (format: 01XXXXXXXXX).</span>');
      return;
    }
    $('#bkash-feedback').html('');
    openOtpModal('bKash', number, function(success) {
      if(success) {
        $('#bkash-feedback').html('<span style="color:green;">OTP verified! Payment can proceed.</span>');
      }
    });
  });
  // Nagad OTP
  $('#request-otp-nagad').click(function() {
    var number = $('#nagad-number').val();
    if(number.length !== 11 || !/^01[0-9]{9}$/.test(number)) {
      $('#nagad-feedback').html('<span style="color:red;">Please enter a valid 11-digit Nagad number (format: 01XXXXXXXXX).</span>');
      return;
    }
    $('#nagad-feedback').html('');
    openOtpModal('Nagad', number, function(success) {
      if(success) {
        $('#nagad-feedback').html('<span style="color:green;">OTP verified! Payment can proceed.</span>');
      }
    });
  });
  // Rocket OTP
  $('#request-otp-rocket').click(function() {
    var number = $('#rocket-number').val();
    if(number.length !== 11 || !/^01[0-9]{9}$/.test(number)) {
      $('#rocket-feedback').html('<span style="color:red;">Please enter a valid 11-digit Rocket number (format: 01XXXXXXXXX).</span>');
      return;
    }
    $('#rocket-feedback').html('');
    openOtpModal('Rocket', number, function(success) {
      if(success) {
        $('#rocket-feedback').html('<span style="color:green;">OTP verified! Payment can proceed.See details in your Bookings Sections</span>');
      }
    });
  });

  // In the card form submit handler:
  $('#card-form').on('submit', function(e) {
    e.preventDefault();
    window.location = 'my-booking.php';
  });

  // For Cash on Delivery button:
  $('#cod-section .btn-success').on('click', function() {
    window.location = 'my-booking.php';
  });

  // By default, do not show any payment method details
  $('[id$="-section"]').hide();
});
</script>
</body>
</html> 