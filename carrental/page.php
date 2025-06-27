<?php
session_start();
error_reporting(0);
include('includes/config.php');
?>

<!DOCTYPE HTML>
<html lang="en">
<head>

<title>Car Rental Portal | Page details</title>
<!--Bootstrap -->
<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
<!--Custome Style -->
<link rel="stylesheet" href="assets/css/style.css" type="text/css">
<!--OWL Carousel slider-->
<link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css">
<link rel="stylesheet" href="assets/css/owl.transitions.css" type="text/css">
<!--slick-slider -->
<link href="assets/css/slick.css" rel="stylesheet">
<!--bootstrap-slider -->
<link href="assets/css/bootstrap-slider.min.css" rel="stylesheet">
<!--FontAwesome Font Style -->
<link href="assets/css/font-awesome.min.css" rel="stylesheet">

<!-- SWITCHER -->
		<link rel="stylesheet" id="switcher-css" type="text/css" href="assets/switcher/css/switcher.css" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/red.css" title="red" media="all" data-default-color="true" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/orange.css" title="orange" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/blue.css" title="blue" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/pink.css" title="pink" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/green.css" title="green" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/purple.css" title="purple" media="all" />
        
<!-- Fav and touch icons -->
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/images/favicon-icon/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/images/favicon-icon/apple-touch-icon-114-precomposed.html">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/images/favicon-icon/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed" href="assets/images/favicon-icon/apple-touch-icon-57-precomposed.png">
<link rel="shortcut icon" href="assets/images/favicon-icon/favicon.png">
<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">
</head>
<body>
<!-- Start Switcher -->
<?php include('includes/colorswitcher.php');?>
<!-- /Switcher -->  
        
<!--Header-->
<?php include('includes/header.php');?>
                      <?php 
$pagetype=$_GET['type'];
$sql = "SELECT type,detail,PageName from tblpages where type=:pagetype";
$query = $dbh -> prepare($sql);
$query->bindParam(':pagetype',$pagetype,PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{ ?>
<section class="page-header aboutus_page">
  <div class="container">
    <div class="page-header_wrap">
      <div class="page-heading">
        <h1><?php   echo htmlentities($result->PageName); ?></h1>
      </div>
      <ul class="coustom-breadcrumb">
        <li><a href="#">Home</a></li>
        <li><?php   echo htmlentities($result->PageName); ?></li>
      </ul>
    </div>
  </div>
  <!-- Dark Overlay-->
  <div class="dark-overlay"></div>
</section>
<section class="about_us section-padding">
  <div class="container">
    <div class="section-header text-center">


      <h2><?php   echo htmlentities($result->PageName); ?></h2>
      <?php if($pagetype == 'faqs'): ?>
      <div class="faq-section" style="max-width:900px;margin:0 auto;text-align:left;">
        <h3 class="text-center" style="margin-bottom:32px;">Frequently Asked Questions:</h3>
        <div class="panel-group" id="faqAccordion">
          <!-- 1. Booking and Reservations -->
          <div class="panel panel-default">
            <div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" data-parent="#faqAccordion" href="#faq1">1. Booking and Reservations</a></h4></div>
            <div id="faq1" class="panel-collapse collapse in">
              <div class="panel-body">
                <strong>Q: How do I make a car rental reservation?</strong><br>
                A: You can book directly on our website by selecting your location, rental dates, and preferred vehicle.<br><br>
                <strong>Q: Can I change or cancel my reservation?</strong><br>
                A: Yes, changes and cancellations can be made through your account or by contacting customer support. Cancellation policies may apply.<br><br>
                <strong>Q: Do I need a credit card to make a reservation?</strong><br>
                A: Most rentals require a valid credit card in the name of the main driver.
              </div>
            </div>
          </div>
          <!-- 2. Requirements and Eligibility -->
          <div class="panel panel-default">
            <div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" data-parent="#faqAccordion" href="#faq2" class="collapsed">2. Requirements and Eligibility</a></h4></div>
            <div id="faq2" class="panel-collapse collapse">
              <div class="panel-body">
                <strong>Q: What are the age requirements to rent a car?</strong><br>
                A: Renters must be at least 21 years old. Drivers under 25 may be subject to a young driver surcharge.<br><br>
                <strong>Q: What documents do I need to rent a car?</strong><br>
                A: You'll need a valid driver's license, a credit/debit card, and sometimes a secondary form of ID.<br><br>
                <strong>Q: Can I use an international driver's license?</strong><br>
                A: Yes, but it must be accompanied by your original driver's license and, in some cases, an International Driving Permit (IDP).
              </div>
            </div>
          </div>
          <!-- 3. Payment and Fees -->
          <div class="panel panel-default">
            <div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" data-parent="#faqAccordion" href="#faq3" class="collapsed">3. Payment and Fees</a></h4></div>
            <div id="faq3" class="panel-collapse collapse">
              <div class="panel-body">
                <strong>Q: What payment methods do you accept?</strong><br>
                A: We accept major credit cards, debit cards (with conditions), and some digital payment methods.<br><br>
                <strong>Q: Is a deposit required?</strong><br>
                A: Yes, a security deposit is typically required and will be held on your card during the rental period.<br><br>
                <strong>Q: Are there any hidden fees?</strong><br>
                A: No. All mandatory charges are disclosed at the time of booking. Optional services may incur extra charges.
              </div>
            </div>
          </div>
          <!-- 4. Insurance and Coverage -->
          <div class="panel panel-default">
            <div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" data-parent="#faqAccordion" href="#faq4" class="collapsed">4. Insurance and Coverage</a></h4></div>
            <div id="faq4" class="panel-collapse collapse">
              <div class="panel-body">
                <strong>Q: Does the rental include insurance?</strong><br>
                A: Basic insurance is usually included, but you can purchase additional coverage for more protection.<br><br>
                <strong>Q: Can I use my personal car insurance or credit card insurance?</strong><br>
                A: Yes, in many cases. Please check with your provider to confirm coverage.<br><br>
                <strong>Q: What should I do in case of an accident?</strong><br>
                A: Contact emergency services if needed, then notify our 24/7 customer support line immediately.
              </div>
            </div>
          </div>
          <!-- 5. Pickup and Return -->
          <div class="panel panel-default">
            <div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" data-parent="#faqAccordion" href="#faq5" class="collapsed">5. Pickup and Return</a></h4></div>
            <div id="faq5" class="panel-collapse collapse">
              <div class="panel-body">
                <strong>Q: Where can I pick up and return the car?</strong><br>
                A: You can pick up and return the car at any of our listed locations. Some locations offer after-hours drop-off.<br><br>
                <strong>Q: What happens if I return the car late?</strong><br>
                A: Late returns may incur additional charges. Check your rental agreement for details.<br><br>
                <strong>Q: Can I pick up the car in one city and drop it off in another?</strong><br>
                A: Yes, one-way rentals are available between select locations. Additional fees may apply.
              </div>
            </div>
          </div>
          <!-- 6. Vehicle Options -->
          <div class="panel panel-default">
            <div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" data-parent="#faqAccordion" href="#faq6" class="collapsed">6. Vehicle Options</a></h4></div>
            <div id="faq6" class="panel-collapse collapse">
              <div class="panel-body">
                <strong>Q: What types of vehicles are available?</strong><br>
                A: We offer a wide range, from economy cars to SUVs, luxury vehicles, and vans.<br><br>
                <strong>Q: Can I choose a specific make or model?</strong><br>
                A: We guarantee the category, not the exact make/model. However, we do our best to accommodate preferences.<br><br>
                <strong>Q: Are your vehicles pet-friendly?</strong><br>
                A: Yes, but vehicles must be returned clean and free of pet hair to avoid cleaning fees.
              </div>
            </div>
          </div>
          <!-- 7. Other Questions -->
          <div class="panel panel-default">
            <div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" data-parent="#faqAccordion" href="#faq7" class="collapsed">7. Other Questions</a></h4></div>
            <div id="faq7" class="panel-collapse collapse">
              <div class="panel-body">
                <strong>Q: Is roadside assistance included?</strong><br>
                A: Yes, basic roadside assistance is included with all rentals.<br><br>
                <strong>Q: Do you offer long-term rentals?</strong><br>
                A: Yes, we offer flexible options for long-term rentals. Contact us for custom pricing.<br><br>
                <strong>Q: Can someone else drive the rental car?</strong><br>
                A: Additional drivers can be added at the time of booking or pickup. They must meet our age and license requirements.
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php else: ?>
      <p><?php  echo $result->detail; ?> </p>
      <?php endif; ?>
    </div>
   <?php } }?>
  </div>
</section>
<!-- /About-us--> 





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

<!--Forgot-password-Form -->
<?php include('includes/forgotpassword.php');?>
<!--/Forgot-password-Form --> 

<!-- Scripts --> 
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script> 
<script src="assets/js/interface.js"></script> 
<!--Switcher-->
<script src="assets/switcher/js/switcher.js"></script>
<!--bootstrap-slider-JS--> 
<script src="assets/js/bootstrap-slider.min.js"></script> 
<!--Slider-JS--> 
<script src="assets/js/slick.min.js"></script> 
<script src="assets/js/owl.carousel.min.js"></script>

</body>

<!-- Mirrored from themes.webmasterdriver.net/carforyou/demo/about-us.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 16 Jun 2017 07:26:12 GMT -->
</html>