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

// SSL Commerz IPN (Instant Payment Notification) Handler
// This file is called by SSL Commerz when payment status changes

include('includes/config.php');
include('includes/sslcommerz_config.php');
include('includes/SSLCommerz.php');

// Log IPN requests for debugging (optional)
error_log("SSL Commerz IPN received: " . file_get_contents('php://input'));

// Get the POST data from SSL Commerz
$post_data = $_POST;

// Validate required fields
if(!isset($post_data['val_id']) || !isset($post_data['tran_id']) || !isset($post_data['status'])) {
    http_response_code(400);
    echo "Invalid IPN data";
    exit();
}

$val_id = $post_data['val_id'];
$tran_id = $post_data['tran_id'];
$status = $post_data['status'];
$amount = isset($post_data['amount']) ? $post_data['amount'] : 0;
$card_type = isset($post_data['card_type']) ? $post_data['card_type'] : '';
$store_amount = isset($post_data['store_amount']) ? $post_data['store_amount'] : 0;
$bank_tran_id = isset($post_data['bank_tran_id']) ? $post_data['bank_tran_id'] : '';

try {
    // Check if this transaction has already been processed
    $checkSql = "SELECT id, payment_status FROM tblbooking WHERE transaction_id = :tran_id";
    $checkQuery = $dbh->prepare($checkSql);
    $checkQuery->bindParam(':tran_id', $tran_id, PDO::PARAM_STR);
    $checkQuery->execute();
    $existingBooking = $checkQuery->fetch(PDO::FETCH_OBJ);
    
    if(!$existingBooking) {
        error_log("SSL Commerz IPN: Transaction not found - " . $tran_id);
        http_response_code(404);
        echo "Transaction not found";
        exit();
    }
    
    // If already processed as PAID, don't process again
    if($existingBooking->payment_status == 'PAID') {
        echo "OK"; // Acknowledge to SSL Commerz
        exit();
    }
    
    // Validate the transaction with SSL Commerz
    $sslcommerz = new SSLCommerz(STORE_ID, STORE_PASSWORD);
    $validation = $sslcommerz->orderValidation($val_id, STORE_ID, STORE_PASSWORD, $tran_id);
    
    if($validation && ($validation['status'] == 'VALID' || $validation['status'] == 'VALIDATED')) {
        
        // Process based on payment status
        switch($status) {
            case 'VALID':
            case 'VALIDATED':
                // Payment successful
                $updateSql = "UPDATE tblbooking SET 
                              Status = 1, 
                              payment_status = 'PAID',
                              validation_id = :val_id,
                              bank_tran_id = :bank_tran_id,
                              payment_method = :payment_method,
                              paid_amount = :amount,
                              ipn_processed = 1,
                              payment_date = NOW()
                              WHERE transaction_id = :tran_id";
                
                $updateQuery = $dbh->prepare($updateSql);
                $updateQuery->bindParam(':val_id', $val_id, PDO::PARAM_STR);
                $updateQuery->bindParam(':bank_tran_id', $bank_tran_id, PDO::PARAM_STR);
                $updateQuery->bindParam(':payment_method', $card_type, PDO::PARAM_STR);
                $updateQuery->bindParam(':amount', $amount, PDO::PARAM_STR);
                $updateQuery->bindParam(':tran_id', $tran_id, PDO::PARAM_STR);
                
                if($updateQuery->execute()) {
                    error_log("SSL Commerz IPN: Payment confirmed for transaction " . $tran_id);
                    echo "OK"; // Acknowledge success to SSL Commerz
                } else {
                    error_log("SSL Commerz IPN: Database update failed for transaction " . $tran_id);
                    http_response_code(500);
                    echo "Database update failed";
                }
                break;
                
            case 'FAILED':
                // Payment failed
                $updateSql = "UPDATE tblbooking SET 
                              payment_status = 'FAILED',
                              validation_id = :val_id,
                              ipn_processed = 1
                              WHERE transaction_id = :tran_id";
                
                $updateQuery = $dbh->prepare($updateSql);
                $updateQuery->bindParam(':val_id', $val_id, PDO::PARAM_STR);
                $updateQuery->bindParam(':tran_id', $tran_id, PDO::PARAM_STR);
                
                if($updateQuery->execute()) {
                    error_log("SSL Commerz IPN: Payment failed for transaction " . $tran_id);
                    echo "OK";
                } else {
                    error_log("SSL Commerz IPN: Database update failed for failed payment " . $tran_id);
                    http_response_code(500);
                    echo "Database update failed";
                }
                break;
                
            case 'CANCELLED':
                // Payment cancelled
                $updateSql = "UPDATE tblbooking SET 
                              payment_status = 'CANCELLED',
                              validation_id = :val_id,
                              ipn_processed = 1
                              WHERE transaction_id = :tran_id";
                
                $updateQuery = $dbh->prepare($updateSql);
                $updateQuery->bindParam(':val_id', $val_id, PDO::PARAM_STR);
                $updateQuery->bindParam(':tran_id', $tran_id, PDO::PARAM_STR);
                
                if($updateQuery->execute()) {
                    error_log("SSL Commerz IPN: Payment cancelled for transaction " . $tran_id);
                    echo "OK";
                } else {
                    error_log("SSL Commerz IPN: Database update failed for cancelled payment " . $tran_id);
                    http_response_code(500);
                    echo "Database update failed";
                }
                break;
                
            default:
                error_log("SSL Commerz IPN: Unknown status received - " . $status . " for transaction " . $tran_id);
                http_response_code(400);
                echo "Unknown status";
        }
        
    } else {
        // Validation failed
        error_log("SSL Commerz IPN: Validation failed for transaction " . $tran_id);
        
        $updateSql = "UPDATE tblbooking SET 
                      payment_status = 'INVALID',
                      validation_id = :val_id,
                      ipn_processed = 1
                      WHERE transaction_id = :tran_id";
        
        $updateQuery = $dbh->prepare($updateSql);
        $updateQuery->bindParam(':val_id', $val_id, PDO::PARAM_STR);
        $updateQuery->bindParam(':tran_id', $tran_id, PDO::PARAM_STR);
        $updateQuery->execute();
        
        http_response_code(400);
        echo "Validation failed";
    }
    
} catch(Exception $e) {
    error_log("SSL Commerz IPN Error: " . $e->getMessage());
    http_response_code(500);
    echo "Internal server error";
}
?>
