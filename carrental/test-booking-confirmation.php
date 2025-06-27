<?php
// Test Booking Confirmation and Payment Status
// This script helps test the admin confirmation functionality

include('includes/config.php');

echo "<h1>Booking Confirmation Test</h1>";

// Test 1: Check database structure
echo "<h2>1. Database Structure Check</h2>";
try {
    $sql = "DESCRIBE tblbooking";
    $query = $dbh->prepare($sql);
    $query->execute();
    $columns = $query->fetchAll(PDO::FETCH_ASSOC);
    
    $requiredColumns = ['Status', 'payment_status'];
    $foundColumns = [];
    
    foreach($columns as $column) {
        if(in_array($column['Field'], $requiredColumns)) {
            $foundColumns[] = $column['Field'];
        }
    }
    
    if(count($foundColumns) == count($requiredColumns)) {
        echo "<div style='color: green;'>✅ All required columns found: " . implode(', ', $foundColumns) . "</div>";
    } else {
        echo "<div style='color: red;'>❌ Missing columns. Found: " . implode(', ', $foundColumns) . "</div>";
    }
} catch(Exception $e) {
    echo "<div style='color: red;'>❌ Database error: " . $e->getMessage() . "</div>";
}

// Test 2: Check existing bookings
echo "<h2>2. Existing Bookings Status</h2>";
try {
    $sql = "SELECT id, BookingNumber, Status, payment_status, userEmail, 
            DATE(PostingDate) as booking_date 
            FROM tblbooking 
            ORDER BY id DESC 
            LIMIT 10";
    $query = $dbh->prepare($sql);
    $query->execute();
    $bookings = $query->fetchAll(PDO::FETCH_ASSOC);
    
    if(count($bookings) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th>ID</th><th>Booking #</th><th>Status</th><th>Payment Status</th><th>User Email</th><th>Date</th>";
        echo "</tr>";
        foreach($bookings as $booking) {
            $statusText = '';
            switch($booking['Status']) {
                case 0: $statusText = 'Pending'; break;
                case 1: $statusText = 'Confirmed'; break;
                case 2: $statusText = 'Cancelled'; break;
                default: $statusText = 'Unknown'; break;
            }
            
            $paymentText = $booking['payment_status'] ?: 'NULL';
            
            echo "<tr>";
            echo "<td>" . $booking['id'] . "</td>";
            echo "<td>" . htmlspecialchars($booking['BookingNumber']) . "</td>";
            echo "<td>" . $statusText . "</td>";
            echo "<td>" . htmlspecialchars($paymentText) . "</td>";
            echo "<td>" . htmlspecialchars($booking['userEmail']) . "</td>";
            echo "<td>" . $booking['booking_date'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div>No bookings found in database.</div>";
    }
} catch(Exception $e) {
    echo "<div style='color: red;'>❌ Error checking bookings: " . $e->getMessage() . "</div>";
}

// Test 3: Simulate admin confirmation (if booking ID provided)
if(isset($_POST['confirm_booking'])) {
    echo "<h2>3. Admin Confirmation Test</h2>";
    $bookingId = intval($_POST['booking_id']);
    
    try {
        // First check current status
        $checkSql = "SELECT Status, payment_status, BookingNumber FROM tblbooking WHERE id = :id";
        $checkQuery = $dbh->prepare($checkSql);
        $checkQuery->bindParam(':id', $bookingId, PDO::PARAM_INT);
        $checkQuery->execute();
        $booking = $checkQuery->fetch(PDO::FETCH_ASSOC);
        
        if($booking) {
            echo "<div>Current status for booking #" . $booking['BookingNumber'] . ":</div>";
            echo "<div>Status: " . $booking['Status'] . "</div>";
            echo "<div>Payment Status: " . ($booking['payment_status'] ?: 'NULL') . "</div>";
            
            // Simulate admin confirmation
            $updateSql = "UPDATE tblbooking SET Status = 1, payment_status = 'PAID' WHERE id = :id";
            $updateQuery = $dbh->prepare($updateSql);
            $updateQuery->bindParam(':id', $bookingId, PDO::PARAM_INT);
            
            if($updateQuery->execute()) {
                echo "<div style='color: green;'>✅ Booking confirmed and marked as PAID!</div>";
                
                // Check updated status
                $checkQuery->execute();
                $updatedBooking = $checkQuery->fetch(PDO::FETCH_ASSOC);
                echo "<div>Updated status:</div>";
                echo "<div>Status: " . $updatedBooking['Status'] . "</div>";
                echo "<div>Payment Status: " . $updatedBooking['payment_status'] . "</div>";
            } else {
                echo "<div style='color: red;'>❌ Failed to update booking.</div>";
            }
        } else {
            echo "<div style='color: red;'>❌ Booking not found.</div>";
        }
    } catch(Exception $e) {
        echo "<div style='color: red;'>❌ Error: " . $e->getMessage() . "</div>";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .form-group { margin-bottom: 15px; }
        input[type="number"] { padding: 8px; width: 200px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 20px 0; }
        table { margin: 20px 0; }
        th, td { padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <div class="warning">
        <strong>⚠️ Test Instructions:</strong>
        <ul>
            <li>This script shows current booking statuses</li>
            <li>You can test admin confirmation by entering a booking ID</li>
            <li>After confirmation, the booking will show Status=1 and payment_status='PAID'</li>
            <li>Users will see "Paid" instead of "Pay Now" for confirmed bookings</li>
        </ul>
    </div>
    
    <form method="post">
        <div class="form-group">
            <label for="booking_id">Enter booking ID to test admin confirmation:</label><br>
            <input type="number" name="booking_id" id="booking_id" required 
                   placeholder="Enter booking ID">
        </div>
        <button type="submit" name="confirm_booking">Test Admin Confirmation</button>
    </form>
    
    <hr>
    <p><a href="index.php">Back to Home</a> | <a href="my-booking.php">View My Bookings</a></p>
</body>
</html> 