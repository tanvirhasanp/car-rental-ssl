<?php
include('includes/config.php');

// Check if OTP fields exist in tblusers table
try {
    $sql = "DESCRIBE tblusers";
    $query = $dbh->prepare($sql);
    $query->execute();
    $columns = $query->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>tblusers Table Structure:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    $otpFields = ['otp_code', 'otp_created_at', 'otp_verified', 'temp_login_token'];
    $foundOtpFields = [];
    
    foreach($columns as $column) {
        $isOtpField = in_array($column['Field'], $otpFields);
        $style = $isOtpField ? "background-color: #d4edda;" : "";
        
        echo "<tr style='$style'>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "<td>" . $column['Extra'] . "</td>";
        echo "</tr>";
        
        if($isOtpField) {
            $foundOtpFields[] = $column['Field'];
        }
    }
    echo "</table>";
    
    echo "<h3>OTP Fields Status:</h3>";
    foreach($otpFields as $field) {
        $status = in_array($field, $foundOtpFields) ? "✅ EXISTS" : "❌ MISSING";
        echo "<p><strong>$field:</strong> $status</p>";
    }
    
    if(count($foundOtpFields) !== count($otpFields)) {
        echo "<div style='background: #f8d7da; padding: 15px; margin: 20px 0; border: 1px solid #f5c6cb;'>";
        echo "<strong>⚠️ Warning:</strong> Some OTP fields are missing. Please run the SQL script: <code>SQL File/add_2fa_otp.sql</code>";
        echo "</div>";
    }
    
} catch(PDOException $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
