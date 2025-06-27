# SSL Commerz Payment Integration Documentation

## Overview
This document provides comprehensive documentation for the SSL Commerz payment gateway integration in the Car Rental Portal. The integration includes a complete payment flow from booking to payment confirmation.

## Features Implemented
- ✅ SSL Commerz Sandbox Integration
- ✅ Payment Initiation from Booking
- ✅ Payment Success Handling
- ✅ Payment Error Handling
- ✅ Payment Cancellation Handling
- ✅ IPN (Instant Payment Notification) Support
- ✅ Database Schema Updates
- ✅ Payment Status Tracking
- ✅ User-Friendly Payment Pages

## Files Added/Modified

### New Files Created:
1. `carrental/includes/sslcommerz_config.php` - SSL Commerz configuration
2. `carrental/includes/SSLCommerz.php` - SSL Commerz API wrapper class
3. `carrental/payment_fixed.php` - Main payment initiation page
4. `carrental/payment-success.php` - Payment success handler
5. `carrental/payment-error.php` - Payment error handler
6. `carrental/payment-cancel.php` - Payment cancellation handler
7. `carrental/payment-ipn.php` - IPN (webhook) handler
8. `SQL File/add_sslcommerz_payment_fields.sql` - Database schema updates

### Modified Files:
1. `carrental/vehical-details.php` - Updated to redirect to new payment page
2. `carrental/my-booking.php` - Added payment status display and Pay Now buttons

## Database Schema Changes

### New Columns Added to `tblbooking`:
- `transaction_id` VARCHAR(100) - Unique transaction identifier
- `payment_status` ENUM('PENDING', 'PAID', 'FAILED', 'CANCELLED', 'INVALID') - Payment status
- `validation_id` VARCHAR(100) - SSL Commerz validation ID
- `bank_tran_id` VARCHAR(100) - Bank transaction ID
- `payment_method` VARCHAR(50) - Payment method used (card type)
- `paid_amount` DECIMAL(10,2) - Amount paid
- `payment_date` DATETIME - Payment completion date
- `ipn_processed` TINYINT(1) - IPN processing flag

## Configuration

### SSL Commerz Settings (includes/sslcommerz_config.php):
```php
define('STORE_ID', 'testbox');           // Sandbox store ID
define('STORE_PASSWORD', 'qwerty');      // Sandbox store password
define('CURRENCY', 'BDT');               // Currency
define('SSLCZ_ENABLED', true);           // Enable/disable payments
```

### URLs Configuration:
- Success URL: `payment-success.php`
- Fail URL: `payment-error.php`
- Cancel URL: `payment-cancel.php`
- IPN URL: `payment-ipn.php`

## Payment Flow

### 1. Booking Creation
- User selects vehicle and dates in `vehical-details.php`
- Booking is created with `PENDING` payment status
- User is redirected to `payment_fixed.php`

### 2. Payment Initiation (`payment_fixed.php`)
- Fetches latest booking for logged-in user
- Calculates total amount (days × price per day)
- Displays booking summary and payment form
- When user clicks "Pay Now":
  - Creates unique transaction ID
  - Prepares SSL Commerz payment data
  - Updates booking with transaction ID
  - Redirects to SSL Commerz gateway

### 3. Payment Processing
- User completes payment on SSL Commerz gateway
- SSL Commerz redirects to appropriate handler based on result

### 4. Payment Response Handling

#### Success (`payment-success.php`):
- Validates payment with SSL Commerz API
- Updates booking status to confirmed (Status = 1)
- Updates payment status to 'PAID'
- Stores payment details
- Displays success message and booking details

#### Error (`payment-error.php`):
- Displays error message
- Shows booking details
- Provides retry option

#### Cancel (`payment-cancel.php`):
- Displays cancellation message
- Shows booking details with pending status
- Provides options to retry or view bookings

#### IPN Handler (`payment-ipn.php`):
- Receives SSL Commerz notifications
- Validates transactions
- Updates payment status asynchronously
- Handles VALID, FAILED, CANCELLED statuses
- Prevents duplicate processing

## Security Features

1. **Transaction Validation**: Every payment is validated with SSL Commerz API
2. **Session Management**: Secure session handling for user authentication
3. **SQL Injection Prevention**: All database queries use prepared statements
4. **XSS Protection**: HTML entity encoding for all output
5. **IPN Verification**: SSL Commerz validation for webhook authenticity

## Payment Status Management

### Status Flow:
```
PENDING → User initiates payment
    ↓
Payment Gateway Processing
    ↓
PAID / FAILED / CANCELLED / INVALID
```

### User Interface:
- **My Bookings Page**: Shows payment status with appropriate buttons
- **Pay Now**: Available for PENDING/FAILED payments
- **Paid**: Green indicator for successful payments
- **Payment Failed - Retry**: Red indicator for failed payments

## Testing

### Sandbox Credentials:
- Store ID: `testbox`
- Store Password: `qwerty`
- Gateway URL: `https://sandbox.sslcommerz.com/`

### Test Cards:
SSL Commerz provides test cards for different scenarios:
- Success: Use any valid card format
- Failure: Specific test cards for failure scenarios
- Cancellation: User can cancel during payment process

## Error Handling

### Common Scenarios:
1. **Payment Gateway Disabled**: Shows appropriate message
2. **Network Issues**: Graceful fallback with retry options
3. **Invalid Transactions**: Proper validation and error messages
4. **Database Errors**: Transaction rollback and error logging
5. **Session Timeouts**: Redirect to login with proper messaging

## Maintenance

### Monitoring:
- Check IPN logs for payment processing issues
- Monitor database for orphaned transactions
- Review payment success/failure rates

### Regular Tasks:
1. Clean up old transaction records
2. Update SSL Commerz credentials when moving to production
3. Monitor payment gateway status
4. Review error logs

## Production Deployment

### Steps to Go Live:
1. **Update Configuration**:
   ```php
   define('STORE_ID', 'your_live_store_id');
   define('STORE_PASSWORD', 'your_live_store_password');
   ```

2. **Update URLs**:
   - Change localhost URLs to your domain
   - Update SSL certificate (HTTPS required)

3. **SSL Commerz API URLs**:
   ```php
   define('SSLCZ_SESSION_API', 'https://securepay.sslcommerz.com/gwprocess/v4/api.php');
   define('SSLCZ_VALIDATION_API', 'https://securepay.sslcommerz.com/validator/api/validationserverAPI.php');
   ```

4. **Testing**:
   - Test all payment scenarios
   - Verify IPN functionality
   - Check SSL certificate validity

## API Reference

### SSLCommerz Class Methods:

#### `makePayment($post_data)`
- Initiates payment session
- Returns payment URL and session key

#### `orderValidation($val_id, $store_id, $store_passwd, $order_id)`
- Validates completed payment
- Returns validation status and details

### Database Helper Functions:
- Booking creation and updates
- Payment status management
- Transaction logging

## Support

### Common Issues:
1. **"Payment gateway disabled"**: Check `SSLCZ_ENABLED` setting
2. **"Invalid transaction"**: Verify SSL Commerz credentials
3. **"Booking not found"**: Ensure user is logged in and has active booking
4. **IPN not working**: Check URL accessibility and SSL certificate

### Contact:
- SSL Commerz Support: For gateway-related issues
- Development Team: For integration-related issues

## Changelog

### Version 1.0 (Current)
- Initial SSL Commerz integration
- Complete payment flow implementation
- IPN handling
- Database schema updates
- User interface improvements

---

*Last Updated: June 28, 2025*
