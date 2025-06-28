# PayPack Mobile Money Integration Guide

## Overview

This guide explains how PayPack mobile money integration works in the Dufatanye Charity Foundation's Giveback Management System (GMS). PayPack provides a unified payment gateway that supports both MTN and Airtel mobile money in Rwanda.

## Features

- **Unified Mobile Money**: Single integration for both MTN and Airtel mobile money
- **Real-time Processing**: Live payment status updates and notifications
- **Automatic Notifications**: Email and SMS confirmations after successful payments
- **Transaction Tracking**: Complete payment history and status monitoring
- **Phone Number Validation**: Automatic formatting for Rwandan mobile numbers

## How It Works

### 1. Payment Flow

1. **Donor Selection**: User selects "Mobile Money (PayPack)" as payment method
2. **Phone Input**: User enters their mobile money phone number
3. **Payment Initiation**: System calls PayPack API to initiate payment
4. **Status Monitoring**: Real-time status checking and updates
5. **Confirmation**: Email and SMS notifications sent upon completion

### 2. Phone Number Support

PayPack automatically detects and processes:

- **MTN Rwanda**: 078X, 079X numbers
- **Airtel Rwanda**: 072X, 073X numbers

### 3. Payment Statuses

- `pending`: Payment initiated, waiting for user action
- `processing`: Payment in progress, being verified
- `completed`: Payment successful, notifications sent
- `failed`: Payment failed, reason recorded

## Configuration

### Environment Variables

Add these to your `.env` file:

```env
# PayPack Mobile Money Configuration
PAYPACK_CLIENT_ID=your_client_id_here
PAYPACK_CLIENT_SECRET=your_client_secret_here
PAYPACK_API_URL=https://payments.paypack.rw/api
```

### Database Settings

Run the configuration update script to sync settings:

```bash
php update_config.php
```

This will create/update these database records:

- `paypack.client_id`
- `paypack.client_secret`
- `paypack.api_url`

## Files Structure

### Core Files

1. **`classes/PayPackHandler.php`**

   - Main PayPack integration class
   - Handles payment initiation and status checking
   - Manages notifications and database updates

2. **`payment_paypack.php`**

   - Payment processing page
   - Phone number validation and formatting
   - User interface for mobile money payments

3. **`donation_payment_status.php`**

   - Payment status tracking page
   - Real-time status updates via AJAX
   - Success/failure handling

4. **`classes/PaymentProcessor.php`**
   - Updated to include PayPack support
   - Unified mobile money processing

### Updated Files

1. **`donation.php`**

   - Updated payment method selection
   - Replaced separate MTN/Airtel with unified PayPack option
   - Updated redirect logic

2. **`update_config.php`**
   - Added PayPack configuration support
   - Environment variable synchronization

## API Integration

### Authentication

PayPack uses OAuth2 authentication:

1. Client credentials are sent to `/auth/agents/authorize`
2. Access token is received and used for API calls
3. Token is automatically refreshed when needed

### Payment Initiation

```php
$paypackHandler = new PayPackHandler();
$result = $paypackHandler->initiateDonationPayment($donation_id, $amount, $phone);
```

**Request Format:**

```json
{
  "amount": 1000.0,
  "number": "0781234567"
}
```

**Response Format:**

```json
{
  "success": true,
  "transaction_id": 123,
  "gateway_reference": "paypack_ref_123",
  "message": "Payment initiated successfully"
}
```

### Status Checking

```php
$result = $paypackHandler->checkPaymentStatus($transaction_id);
```

**Status Mapping:**

- PayPack `successful` → System `completed`
- PayPack `failed` → System `failed`
- PayPack `pending` → System `processing`

## Phone Number Formatting

The system automatically formats phone numbers:

| Input Format | Formatted Output | Provider |
| ------------ | ---------------- | -------- |
| 250781234567 | 0781234567       | MTN      |
| 0781234567   | 0781234567       | MTN      |
| 781234567    | 0781234567       | MTN      |
| 0721234567   | 0721234567       | Airtel   |

## Notifications

### Email Notifications

- **Template**: `donation_confirmation`
- **Triggers**: Payment completion
- **Content**: Donation details, amount, reference, payment method

### SMS Notifications

- **Provider**: Africa's Talking
- **Triggers**: Payment completion
- **Content**: Confirmation message with amount and reference

## Error Handling

### Common Errors

1. **Authentication Failed**

   - Check PayPack credentials
   - Verify API connectivity

2. **Invalid Phone Number**

   - Ensure 10-digit format
   - Check for valid Rwandan prefixes

3. **Payment Failed**
   - Insufficient funds
   - Network issues
   - User cancellation

### Debugging

Enable error logging in `config.php`:

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

Check logs for detailed error information:

```bash
tail -f /var/log/apache2/error.log
```

## Testing

### Test Credentials

For development/testing, use these credentials:

- **Client ID**: `cb3de54e-4900-11f0-ab56-dead131a2dd9`
- **Client Secret**: `522f8f6a27effc0a15a685e455d8a0bfda39a3ee5e6b4b0d3255bfef95601890afd80709`

### Test Phone Numbers

Use these test numbers:

- **MTN**: 0781234567
- **Airtel**: 0721234567

### Test Flow

1. Go to donation page
2. Select "Mobile Money (PayPack)"
3. Enter test phone number
4. Complete payment process
5. Check status updates
6. Verify notifications

## Security Considerations

1. **Credentials**: Store securely in environment variables
2. **Validation**: Always validate phone numbers and amounts
3. **Logging**: Log all payment attempts and responses
4. **HTTPS**: Ensure all API calls use HTTPS
5. **Rate Limiting**: Implement rate limiting for payment attempts

## Troubleshooting

### Payment Not Received

1. Check phone number format
2. Verify mobile money account has sufficient funds
3. Check for pending transactions in mobile money app
4. Contact mobile money provider if needed

### Status Not Updating

1. Check PayPack API connectivity
2. Verify transaction reference
3. Check database connection
4. Review error logs

### Notifications Not Sent

1. Check email/SMS configuration
2. Verify notification settings
3. Check Africa's Talking credentials
4. Review notification logs

## Support

For technical support:

1. Check error logs first
2. Verify configuration settings
3. Test with known working credentials
4. Contact PayPack support if API issues persist

## Future Enhancements

1. **Webhook Support**: Real-time payment notifications
2. **Bulk Payments**: Multiple donation processing
3. **Refund Support**: Automated refund processing
4. **Analytics**: Payment success rate tracking
5. **Multi-currency**: Support for other currencies

---

**Note**: This integration replaces the previous separate MTN and Airtel payment methods with a unified PayPack solution that supports both providers through a single API.
