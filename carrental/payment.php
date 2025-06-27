<?php 
session_start();
include('includes/config.php');
error_reporting(0);
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
.panel-heading h5 {
  margin: 0;
  color: #333;
  font-weight: 600;
}
.panel-body ol, .panel-body ul {
  color: #666;
  font-size: 14px;
}
.panel-body li {
  margin-bottom: 5px;
}
label {
  font-size: 14px;
  color: #555;
}
.btn-lg {
  padding: 12px 30px;
  font-size: 16px;
  font-weight: 600;
}
.alert {
  border-radius: 8px;
}
.card-body img {
  object-fit: contain;
}
</style>
</head>
<body>
<!--Header-->
<?php include('includes/header.php');?>
<!-- /Header -->

<section class="section-padding gray-bg">
  <div class="container">
    <div class="section-header text-center">
      <h2>Payment <span>Methods</span></h2>
      <p>Choose your preferred payment method for a seamless car rental experience.</p>
    </div>
    <div class="row payment-methods-list text-center" style="margin-bottom: 40px;">
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
            
            <!-- Payment Instructions -->
            <div class="panel panel-default" style="margin-bottom:20px; text-align:left;">
              <div class="panel-heading">
                <h5><i class="fa fa-info-circle"></i> Payment Instructions</h5>
              </div>
              <div class="panel-body">
                <ol style="margin-bottom:0;">
                  <li>Enter your bKash mobile number</li>
                  <li>Click "Request OTP" to receive verification code</li>
                  <li>Open bKash app and approve the payment request</li>
                  <li>Enter the OTP received on your mobile</li>
                  <li>Complete the payment process</li>
                </ol>
              </div>
            </div>
            
            <div style="margin-bottom:10px; color:#d81b60; font-weight:500;">Please enter your 11-digit bKash number (format: 01XXXXXXXXX) to proceed.</div>
            <form id="bkash-form" onsubmit="return false;">
              <div class="form-group">
                <label for="bkash-number" style="text-align:left; display:block; margin-bottom:5px; font-weight:500;">bKash Mobile Number *</label>
                <div class="input-group" style="border-radius: 30px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.07);">
                  <span class="input-group-addon" style="background: #f8f8f8; border: none; border-radius: 30px 0 0 30px; font-size: 18px;"><i class="fa fa-phone"></i></span>
                  <input type="tel" pattern="01[0-9]{9}" class="form-control" id="bkash-number" placeholder="01XXXXXXXXX" maxlength="11" required
                  style="border: none; border-radius: 0 30px 30px 0; font-size: 18px; padding: 16px 18px; box-shadow: none;" value="<?php echo isset($_SESSION['bkash_number']) ? htmlentities($_SESSION['bkash_number']) : ''; ?>">
                </div>
                <small class="text-muted">Enter your registered bKash mobile number</small>
              </div>
              
              <div class="form-group">
                <label for="bkash-pin" style="text-align:left; display:block; margin-bottom:5px; font-weight:500;">bKash PIN *</label>
                <div class="input-group" style="border-radius: 30px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.07);">
                  <span class="input-group-addon" style="background: #f8f8f8; border: none; border-radius: 30px 0 0 30px; font-size: 18px;"><i class="fa fa-lock"></i></span>
                  <input type="password" class="form-control" id="bkash-pin" placeholder="Enter your bKash PIN" maxlength="5" required
                  style="border: none; border-radius: 0 30px 30px 0; font-size: 18px; padding: 16px 18px; box-shadow: none;">
                </div>
                <small class="text-muted">Your 5-digit bKash PIN</small>
              </div>
              
              <button type="button" class="btn btn-primary btn-lg" id="request-otp" style="width: 200px;">
                <i class="fa fa-mobile"></i> Send Payment Request
              </button>
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
            <?php if(isset($_SESSION['booking_amount']) && isset($_SESSION['booking_number'])): ?>
            <div class="alert alert-info" style="margin-bottom:15px;">
              <strong>Booking #:</strong> <?php echo htmlentities($_SESSION['booking_number']); ?> &nbsp; | &nbsp;
              <strong>Amount:</strong> ৳<?php echo htmlentities($_SESSION['booking_amount']); ?>
            </div>
            <?php endif; ?>
            
            <!-- Payment Instructions -->
            <div class="panel panel-default" style="margin-bottom:20px; text-align:left;">
              <div class="panel-heading">
                <h5><i class="fa fa-info-circle"></i> Payment Instructions</h5>
              </div>
              <div class="panel-body">
                <ol style="margin-bottom:0;">
                  <li>Enter your Nagad mobile number</li>
                  <li>Click "Request OTP" to receive verification code</li>
                  <li>Open Nagad app and confirm the payment</li>
                  <li>Enter your Nagad PIN when prompted</li>
                  <li>Complete the transaction</li>
                </ol>
              </div>
            </div>
            
            <div style="margin-bottom:10px; color:#ee6002; font-weight:500;">Enter your Nagad number to proceed with payment.</div>
            <form id="nagad-form" onsubmit="return false;">
              <div class="form-group">
                <label for="nagad-number" style="text-align:left; display:block; margin-bottom:5px; font-weight:500;">Nagad Mobile Number *</label>
                <div class="input-group" style="border-radius: 30px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.07);">
                  <span class="input-group-addon" style="background: #f8f8f8; border: none; border-radius: 30px 0 0 30px; font-size: 18px;"><i class="fa fa-phone"></i></span>
                  <input type="tel" pattern="01[0-9]{9}" class="form-control" id="nagad-number" placeholder="01XXXXXXXXX" maxlength="11" required
                  style="border: none; border-radius: 0 30px 30px 0; font-size: 18px; padding: 16px 18px; box-shadow: none;">
                </div>
                <small class="text-muted">Enter your registered Nagad mobile number</small>
              </div>
              
              <div class="form-group">
                <label for="nagad-pin" style="text-align:left; display:block; margin-bottom:5px; font-weight:500;">Nagad PIN *</label>
                <div class="input-group" style="border-radius: 30px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.07);">
                  <span class="input-group-addon" style="background: #f8f8f8; border: none; border-radius: 30px 0 0 30px; font-size: 18px;"><i class="fa fa-lock"></i></span>
                  <input type="password" class="form-control" id="nagad-pin" placeholder="Enter your Nagad PIN" maxlength="4" required
                  style="border: none; border-radius: 0 30px 30px 0; font-size: 18px; padding: 16px 18px; box-shadow: none;">
                </div>
                <small class="text-muted">Your 4-digit Nagad PIN</small>
              </div>
              
              <button type="button" class="btn btn-warning btn-lg" id="request-otp-nagad" style="width: 200px;">
                <i class="fa fa-mobile"></i> Send Payment Request
              </button>
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
            <?php if(isset($_SESSION['booking_amount']) && isset($_SESSION['booking_number'])): ?>
            <div class="alert alert-info" style="margin-bottom:15px;">
              <strong>Booking #:</strong> <?php echo htmlentities($_SESSION['booking_number']); ?> &nbsp; | &nbsp;
              <strong>Amount:</strong> ৳<?php echo htmlentities($_SESSION['booking_amount']); ?>
            </div>
            <?php endif; ?>
            
            <!-- Payment Instructions -->
            <div class="panel panel-default" style="margin-bottom:20px; text-align:left;">
              <div class="panel-heading">
                <h5><i class="fa fa-info-circle"></i> Payment Instructions</h5>
              </div>
              <div class="panel-body">
                <ol style="margin-bottom:0;">
                  <li>Enter your Rocket mobile number</li>
                  <li>Click "Request OTP" to receive verification code</li>
                  <li>Dial *322# from your registered mobile</li>
                  <li>Follow the prompts to complete payment</li>
                  <li>Enter your Rocket PIN to confirm</li>
                </ol>
              </div>
            </div>
            
            <div style="margin-bottom:10px; color:#8e44ad; font-weight:500;">Enter your Rocket number to proceed with payment.</div>
            <form id="rocket-form" onsubmit="return false;">
              <div class="form-group">
                <label for="rocket-number" style="text-align:left; display:block; margin-bottom:5px; font-weight:500;">Rocket Mobile Number *</label>
                <div class="input-group" style="border-radius: 30px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.07);">
                  <span class="input-group-addon" style="background: #f8f8f8; border: none; border-radius: 30px 0 0 30px; font-size: 18px;"><i class="fa fa-phone"></i></span>
                  <input type="tel" pattern="01[0-9]{9}" class="form-control" id="rocket-number" placeholder="01XXXXXXXXX" maxlength="11" required
                  style="border: none; border-radius: 0 30px 30px 0; font-size: 18px; padding: 16px 18px; box-shadow: none;">
                </div>
                <small class="text-muted">Enter your registered Rocket mobile number</small>
              </div>
              
              <div class="form-group">
                <label for="rocket-pin" style="text-align:left; display:block; margin-bottom:5px; font-weight:500;">Rocket PIN *</label>
                <div class="input-group" style="border-radius: 30px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.07);">
                  <span class="input-group-addon" style="background: #f8f8f8; border: none; border-radius: 30px 0 0 30px; font-size: 18px;"><i class="fa fa-lock"></i></span>
                  <input type="password" class="form-control" id="rocket-pin" placeholder="Enter your Rocket PIN" maxlength="5" required
                  style="border: none; border-radius: 0 30px 30px 0; font-size: 18px; padding: 16px 18px; box-shadow: none;">
                </div>
                <small class="text-muted">Your 5-digit Rocket PIN</small>
              </div>
              
              <button type="button" class="btn btn-info btn-lg" id="request-otp-rocket" style="width: 200px;">
                <i class="fa fa-mobile"></i> Send Payment Request
              </button>
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
            <?php if(isset($_SESSION['booking_amount']) && isset($_SESSION['booking_number'])): ?>
            <div class="alert alert-info" style="margin-bottom:15px;">
              <strong>Booking #:</strong> <?php echo htmlentities($_SESSION['booking_number']); ?> &nbsp; | &nbsp;
              <strong>Amount:</strong> ৳<?php echo htmlentities($_SESSION['booking_amount']); ?>
            </div>
            <?php endif; ?>
            
            <!-- Security Notice -->
            <div class="alert alert-success" style="margin-bottom:20px;">
              <i class="fa fa-shield"></i> <strong>Secure Payment:</strong> All card information is encrypted and secure. We accept Visa and MasterCard.
            </div>
            
            <!-- Merchant Information -->
            <div class="panel panel-info" style="margin-bottom:20px; text-align:left;">
              <div class="panel-heading">
                <h5><i class="fa fa-info-circle"></i> Merchant Information</h5>
              </div>
              <div class="panel-body" style="font-size:13px;">
                <div class="row">
                  <div class="col-xs-6"><strong>Merchant:</strong> Car Rental Portal</div>
                  <div class="col-xs-6"><strong>Currency:</strong> BDT (৳)</div>
                </div>
                <div class="row" style="margin-top:5px;">
                  <div class="col-xs-6"><strong>Transaction ID:</strong> TXN<?php echo date('YmdHis') . rand(100,999); ?></div>
                  <div class="col-xs-6"><strong>Gateway:</strong> SSL Commerz</div>
                </div>
              </div>
            </div>
            
            <!-- Payment Instructions -->
            <div class="panel panel-default" style="margin-bottom:20px; text-align:left;">
              <div class="panel-heading">
                <h5><i class="fa fa-credit-card"></i> Payment Instructions</h5>
              </div>
              <div class="panel-body">
                <ul style="margin-bottom:0;">
                  <li>Enter your 16-digit card number</li>
                  <li>Provide the cardholder's full name</li>
                  <li>Enter expiry date (MM/YY format)</li>
                  <li>Enter 3-digit CVV from back of card</li>
                  <li>Click "Pay Now" to process payment</li>
                </ul>
              </div>
            </div>
            <form id="card-form" onsubmit="return false;">
              <div class="form-group">
                <label for="card-number" style="text-align:left; display:block; margin-bottom:5px; font-weight:500;">Card Number *</label>
                <div class="input-group" style="border-radius: 30px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.07);">
                  <span class="input-group-addon" style="background: #f8f8f8; border: none; border-radius: 30px 0 0 30px; font-size: 18px;"><i class="fa fa-credit-card"></i></span>
                  <input type="tel" id="card-number" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19" required style="border: none; border-radius: 0 30px 30px 0; font-size: 18px; padding: 16px 18px; box-shadow: none;">
                </div>
              </div>
              <div class="form-group">
                <label for="cardholder-name" style="text-align:left; display:block; margin-bottom:5px; font-weight:500;">Cardholder Name *</label>
                <div class="input-group" style="border-radius: 30px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.07);">
                  <span class="input-group-addon" style="background: #f8f8f8; border: none; border-radius: 30px 0 0 30px; font-size: 18px;"><i class="fa fa-user"></i></span>
                  <input type="text" id="cardholder-name" class="form-control" placeholder="John Doe" required style="border: none; border-radius: 0 30px 30px 0; font-size: 18px; padding: 16px 18px; box-shadow: none; text-transform: uppercase;">
                </div>
              </div>
              <div class="form-group row">
                <div class="col-xs-6">
                  <label for="expiry-date" style="text-align:left; display:block; margin-bottom:5px; font-weight:500;">Expiry Date *</label>
                  <div class="input-group" style="border-radius: 30px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.07);">
                    <span class="input-group-addon" style="background: #f8f8f8; border: none; border-radius: 30px 0 0 30px; font-size: 18px;"><i class="fa fa-calendar"></i></span>
                    <input type="tel" id="expiry-date" class="form-control" placeholder="MM/YY" maxlength="5" required style="border: none; border-radius: 0 30px 30px 0; font-size: 18px; padding: 16px 18px; box-shadow: none;">
                  </div>
                </div>
                <div class="col-xs-6">
                  <label for="cvv" style="text-align:left; display:block; margin-bottom:5px; font-weight:500;">CVV *</label>
                  <div class="input-group" style="border-radius: 30px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.07);">
                    <span class="input-group-addon" style="background: #f8f8f8; border: none; border-radius: 30px 0 0 30px; font-size: 18px;"><i class="fa fa-lock"></i></span>
                    <input type="tel" id="cvv" class="form-control" placeholder="123" maxlength="4" required style="border: none; border-radius: 0 30px 30px 0; font-size: 18px; padding: 16px 18px; box-shadow: none;">
                  </div>
                </div>
              </div>
              
              <!-- Billing Information -->
              <div style="margin: 20px 0; padding: 15px; background: #f9f9f9; border-radius: 8px; text-align: left;">
                <h5 style="margin-bottom: 15px; color: #333;"><i class="fa fa-map-marker"></i> Billing Information</h5>
                <div class="row">
                  <div class="col-xs-12" style="margin-bottom: 10px;">
                    <input type="text" id="billing-address" class="form-control" placeholder="Billing Address" required style="border-radius: 20px; padding: 12px 15px;">
                  </div>
                  <div class="col-xs-6" style="margin-bottom: 10px;">
                    <input type="text" id="billing-city" class="form-control" placeholder="City" required style="border-radius: 20px; padding: 12px 15px;">
                  </div>
                  <div class="col-xs-6" style="margin-bottom: 10px;">
                    <input type="text" id="billing-postal" class="form-control" placeholder="Postal Code" required style="border-radius: 20px; padding: 12px 15px;">
                  </div>
                </div>
              </div>
              <div style="margin: 15px 0; font-size: 12px; color: #666;">
                <i class="fa fa-lock"></i> Your payment information is secured with 256-bit SSL encryption
              </div>
              <button type="submit" class="btn btn-primary btn-lg" style="width: 200px;">
                <i class="fa fa-credit-card"></i> Pay Now
              </button>
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
            <?php if(isset($_SESSION['booking_amount']) && isset($_SESSION['booking_number'])): ?>
            <div class="alert alert-info" style="margin-bottom:15px;">
              <strong>Booking #:</strong> <?php echo htmlentities($_SESSION['booking_number']); ?> &nbsp; | &nbsp;
              <strong>Amount:</strong> ৳<?php echo htmlentities($_SESSION['booking_amount']); ?>
            </div>
            <?php endif; ?>
            
            <!-- Payment Instructions -->
            <div class="panel panel-default" style="margin-bottom:20px; text-align:left;">
              <div class="panel-heading">
                <h5><i class="fa fa-money"></i> Cash Payment Instructions</h5>
              </div>
              <div class="panel-body">
                <ul style="margin-bottom:0;">
                  <li>Pay in cash when you collect the vehicle</li>
                  <li>Bring exact amount or change will be provided</li>
                  <li>Payment must be made before vehicle handover</li>
                  <li>Receipt will be provided upon payment</li>
                  <li>Valid ID required for verification</li>
                </ul>
              </div>
            </div>
            
            <div class="alert alert-warning" style="margin-bottom:20px;">
              <i class="fa fa-exclamation-triangle"></i> <strong>Note:</strong> Cash payment is due at the time of vehicle pickup. Please ensure you have the exact amount ready.
            </div>
            
            <button class="btn btn-success btn-lg" id="confirm-cod" style="width: 250px;">
              <i class="fa fa-check"></i> Confirm Cash Payment
            </button>
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

  // Debug: Check if elements are loaded
  console.log('Payment page loaded');
  console.log('Payment method cards found:', $('.payment-methods-list .card').length);

  // Mobile number input: auto-format and validate
  function setupMobileNumberInput(selector) {
    $(selector).on('input', function() {
      let val = $(this).val();
      val = val.replace(/[^0-9]/g, '');
      if(val.length > 11) val = val.slice(0, 11);
      
      // Ensure it starts with '01'
      if(val.length > 0 && !val.startsWith('01')) {
        if(val.startsWith('1')) {
          val = '0' + val;
        } else if(!val.startsWith('0')) {
          val = '01' + val;
        }
      }
      
      $(this).val(val);
    });
  }
  setupMobileNumberInput('#bkash-number');
  setupMobileNumberInput('#nagad-number');
  setupMobileNumberInput('#rocket-number');

  // Card number formatting
  $('#card-number').on('input', function() {
    let val = $(this).val().replace(/\s/g, '').replace(/[^0-9]/g, '');
    let formattedVal = val.replace(/(.{4})/g, '$1 ').trim();
    if(formattedVal.length > 19) formattedVal = formattedVal.slice(0, 19);
    $(this).val(formattedVal);
  });

  // Expiry date formatting
  $('#expiry-date').on('input', function() {
    let val = $(this).val().replace(/[^0-9]/g, '');
    if(val.length >= 2) {
      val = val.substring(0,2) + '/' + val.substring(2,4);
    }
    $(this).val(val);
  });

  // CVV validation
  $('#cvv').on('input', function() {
    let val = $(this).val().replace(/[^0-9]/g, '');
    $(this).val(val);
  });

  // Cardholder name formatting
  $('#cardholder-name').on('input', function() {
    let val = $(this).val().replace(/[^a-zA-Z\s]/g, '').toUpperCase();
    $(this).val(val);
  });

  // PIN field validations
  $('#bkash-pin, #nagad-pin, #rocket-pin').on('input', function() {
    let val = $(this).val().replace(/[^0-9]/g, '');
    $(this).val(val);
  });

  // Show only the selected payment section (dynamic)
  function showSection(method) {
    console.log('Showing section for method:', method);
    
    // Hide all payment sections
    $('[id$="-section"]').stop(true, true).hide();
    
    // Show the selected section
    var sectionId = '#' + method + '-section';
    console.log('Section ID:', sectionId);
    
    if($(sectionId).length > 0) {
      $(sectionId).fadeIn(250);
      console.log('Section shown successfully');
    } else {
      console.log('Section not found:', sectionId);
    }
    
    // Update card selection state
    $('.payment-methods-list .card').removeClass('selected');
    $('.payment-methods-list .card[data-method="' + method + '"]').addClass('selected');
    selectedMethod = method;
  }

  // Handle click for all payment method cards (dynamic)
  $('.payment-methods-list').on('click', '.card', function() {
    var method = $(this).data('method');
    console.log('Payment method clicked:', method);
    showSection(method);
  });

  // Use event delegation for close button logic (dynamic)
  $(document).on('click', '.close-method-section', function() {
    console.log('Close button clicked');
    $(this).closest('[id$="-section"]').fadeOut(200);
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
  $(document).on('click', '#request-otp', function() {
    var number = $('#bkash-number').val();
    var pin = $('#bkash-pin').val();
    
    console.log('bKash payment request:', number, pin.length);
    
    if(number.length !== 11 || !/^01[0-9]{9}$/.test(number)) {
      $('#bkash-feedback').html('<span style="color:red;">Please enter a valid 11-digit bKash number (format: 01XXXXXXXXX).</span>');
      return;
    }
    if(pin.length !== 5) {
      $('#bkash-feedback').html('<span style="color:red;">Please enter your 5-digit bKash PIN.</span>');
      return;
    }
    
    $('#bkash-feedback').html('<span style="color:blue;">Sending payment request...</span>');
    $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
    
    setTimeout(() => {
      $(this).prop('disabled', false).html('<i class="fa fa-mobile"></i> Send Payment Request');
      openOtpModal('bKash', number, function(success) {
        if(success) {
          $('#bkash-feedback').html('<span style="color:green;">Payment successful! Redirecting...</span>');
        }
      });
    }, 1500);
  });
  
  // Nagad OTP
  $(document).on('click', '#request-otp-nagad', function() {
    var number = $('#nagad-number').val();
    var pin = $('#nagad-pin').val();
    
    console.log('Nagad payment request:', number, pin.length);
    
    if(number.length !== 11 || !/^01[0-9]{9}$/.test(number)) {
      $('#nagad-feedback').html('<span style="color:red;">Please enter a valid 11-digit Nagad number (format: 01XXXXXXXXX).</span>');
      return;
    }
    if(pin.length !== 4) {
      $('#nagad-feedback').html('<span style="color:red;">Please enter your 4-digit Nagad PIN.</span>');
      return;
    }
    
    $('#nagad-feedback').html('<span style="color:blue;">Sending payment request...</span>');
    $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
    
    setTimeout(() => {
      $(this).prop('disabled', false).html('<i class="fa fa-mobile"></i> Send Payment Request');
      openOtpModal('Nagad', number, function(success) {
        if(success) {
          $('#nagad-feedback').html('<span style="color:green;">Payment successful! Redirecting...</span>');
        }
      });
    }, 1500);
  });
  
  // Rocket OTP
  $(document).on('click', '#request-otp-rocket', function() {
    var number = $('#rocket-number').val();
    var pin = $('#rocket-pin').val();
    
    console.log('Rocket payment request:', number, pin.length);
    
    if(number.length !== 11 || !/^01[0-9]{9}$/.test(number)) {
      $('#rocket-feedback').html('<span style="color:red;">Please enter a valid 11-digit Rocket number (format: 01XXXXXXXXX).</span>');
      return;
    }
    if(pin.length !== 5) {
      $('#rocket-feedback').html('<span style="color:red;">Please enter your 5-digit Rocket PIN.</span>');
      return;
    }
    
    $('#rocket-feedback').html('<span style="color:blue;">Sending payment request...</span>');
    $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
    
    setTimeout(() => {
      $(this).prop('disabled', false).html('<i class="fa fa-mobile"></i> Send Payment Request');
      openOtpModal('Rocket', number, function(success) {
        if(success) {
          $('#rocket-feedback').html('<span style="color:green;">Payment successful! Redirecting...</span>');
        }
      });
    }, 1500);
  });

  // In the card form submit handler:
  $(document).on('submit', '#card-form', function(e) {
    e.preventDefault();
    console.log('Card form submitted');
    
    // Validate card form
    var cardNumber = $('#card-number').val().replace(/\s/g, '');
    var cardholderName = $('#cardholder-name').val().trim();
    var expiryDate = $('#expiry-date').val();
    var cvv = $('#cvv').val();
    var billingAddress = $('#billing-address').val().trim();
    var billingCity = $('#billing-city').val().trim();
    var billingPostal = $('#billing-postal').val().trim();
    
    // Basic validation
    if(cardNumber.length < 16) {
      alert('Please enter a valid 16-digit card number');
      $('#card-number').focus();
      return;
    }
    if(cardholderName.length < 2) {
      alert('Please enter cardholder name');
      $('#cardholder-name').focus();
      return;
    }
    if(!/^\d{2}\/\d{2}$/.test(expiryDate)) {
      alert('Please enter expiry date in MM/YY format');
      $('#expiry-date').focus();
      return;
    }
    if(cvv.length < 3) {
      alert('Please enter a valid CVV');
      $('#cvv').focus();
      return;
    }
    if(billingAddress.length < 5) {
      alert('Please enter billing address');
      $('#billing-address').focus();
      return;
    }
    if(billingCity.length < 2) {
      alert('Please enter billing city');
      $('#billing-city').focus();
      return;
    }
    if(billingPostal.length < 3) {
      alert('Please enter postal code');
      $('#billing-postal').focus();
      return;
    }
    
    // Show processing message
    $(this).find('button[type="submit"]').html('<i class="fa fa-spinner fa-spin"></i> Processing Payment...').prop('disabled', true);
    
    // Simulate payment processing
    setTimeout(function() {
      alert('Payment successful! Your card has been charged ৳' + '<?php echo isset($_SESSION["booking_amount"]) ? $_SESSION["booking_amount"] : "0"; ?>' + '. Redirecting to your bookings...');
      window.location = 'my-booking.php';
    }, 3000);
  });

  // For Cash on Delivery button:
  $(document).on('click', '#confirm-cod', function() {
    if(confirm('Confirm cash payment? You will need to pay when collecting the vehicle.')) {
      window.location = 'my-booking.php';
    }
  });

  // By default, do not show any payment method details
  $('[id$="-section"]').hide();
});
</script>
</body>
</html> 