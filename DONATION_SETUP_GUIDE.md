# Enhanced Donation System Setup Guide

## Overview

This guide will help you set up the enhanced donation system for Dufatanye Charity Foundation with SMS notifications using Africa's Talking.

## Features Implemented

### ✅ Payment Methods

- **Stripe** - Credit/Debit card payments
- **MTN Mobile Money** - Mobile money payments
- **Airtel Money** - Mobile money payments
- **SMS Notifications** - Via Africa's Talking

### ✅ User Experience

- Guest donations (no login required)
- Logged-in volunteer donations
- Donation history tracking
- Email and SMS confirmations
- Modern, responsive UI
- Amount presets for quick selection

### ✅ Volunteer Integration

- Donation history in volunteer dashboard
- Automatic tracking for logged-in users
- Motivation to register/login

## Setup Instructions

### 1. Database Setup

Run the database updates to create the new tables:

```sql
-- Run this SQL file to set up the donation system
source db/donation_system_updates.sql;
```

This will create:

- `donations` table
- `donation_history` table
- `payment_settings` table
- `email_templates` table
- `sms_templates` table

### 2. Install Dependencies

The required packages are already installed via Composer:

```bash
composer install
```

This installs:

- `stripe/stripe-php` - For Stripe payments
- `africastalking/africastalking` - For SMS notifications
- `phpmailer/phpmailer` - For email notifications

### 3. Configure Payment Settings

#### Stripe Configuration

1. Sign up at [Stripe](https://stripe.com)
2. Get your API keys from the dashboard
3. Update the database:

```sql
UPDATE payment_settings SET setting_value = 'pk_test_your_publishable_key'
WHERE payment_method = 'stripe' AND setting_key = 'publishable_key';

UPDATE payment_settings SET setting_value = 'sk_test_your_secret_key'
WHERE payment_method = 'stripe' AND setting_key = 'secret_key';
```

#### SMS Configuration (Africa's Talking)

1. Sign up at [Africa's Talking](https://account.africastalking.com/)
2. Create a new app and get your API key
3. Update `sms_config.php`:

```php
define('AFRICASTALKING_USERNAME', 'your_username_here');
define('AFRICASTALKING_API_KEY', 'your_api_key_here');
define('AFRICASTALKING_SENDER_ID', 'DUFATANYE');
```

4. Request approval for your sender ID "DUFATANYE"

### 4. Email Configuration

Update the email settings in `classes/PaymentProcessor.php`:

```php
$mail->Host = 'smtp.gmail.com'; // Your SMTP host
$mail->Username = 'your-email@gmail.com'; // Your email
$mail->Password = 'your-app-password'; // Your email password
```

### 5. Test the System

#### Test SMS

1. Update your phone number in `test_sms.php`
2. Visit `test_sms.php` in your browser
3. Verify you receive the test SMS

#### Test Donations

1. Visit `donation.php`
2. Try making a test donation
3. Check that notifications are sent

## File Structure

```
├── donation.php              # Main donation page
├── payment_stripe.php        # Stripe payment processing
├── payment_mtn.php           # MTN Mobile Money processing
├── payment_airtel.php        # Airtel Money processing
├── donation_success.php      # Success page
├── sms_config.php           # SMS configuration
├── test_sms.php             # SMS testing
├── classes/
│   └── PaymentProcessor.php  # Payment processing logic
└── db/
    └── donation_system_updates.sql  # Database schema
```

## User Flow

### Guest Donation Flow

1. User visits `donation.php`
2. Sees login/register prompt for benefits
3. Fills donation form (name, email, phone, amount)
4. Selects payment method
5. Completes payment
6. Receives email and SMS confirmation
7. Redirected to success page

### Logged-in Volunteer Flow

1. Volunteer visits `donation.php`
2. Form pre-filled with volunteer info
3. Makes donation
4. Receives notifications
5. Donation appears in dashboard history

## SMS Templates

The system includes these SMS templates:

### Donation Confirmation

```
Thank you {fullname}! Your donation of {amount} RWF (Ref: {donation_ref}) has been received. Dufatanye Charity Foundation
```

### Donation Receipt

```
Receipt: {donation_ref} | Amount: {amount} RWF | Date: {date} | Dufatanye Charity Foundation
```

### Volunteer Welcome

```
Welcome {fullname}! Your volunteer account has been approved. Login at our website to start making a difference.
```

## Customization

### Adding New Payment Methods

1. Create payment processing file (e.g., `payment_newmethod.php`)
2. Add method to `donation.php` form
3. Update `PaymentProcessor.php` with new method
4. Add settings to database

### Customizing SMS Messages

1. Edit templates in `sms_templates` table
2. Use placeholders: `{fullname}`, `{amount}`, `{donation_ref}`, `{date}`
3. Keep messages under 160 characters

### Styling

- Main styles are in each PHP file
- Uses Bootstrap 4.5.2
- Font Awesome 5.15.4 for icons
- Responsive design

## Security Considerations

1. **Input Validation**: All user inputs are validated and sanitized
2. **SQL Injection**: Uses prepared statements
3. **XSS Protection**: Output is escaped with `htmlspecialchars()`
4. **CSRF Protection**: Consider adding CSRF tokens
5. **Payment Security**: Stripe handles sensitive payment data

## Monitoring

### Database Views

The system includes these views for monitoring:

```sql
-- Donation statistics
SELECT * FROM donation_stats;

-- Volunteer donations
SELECT * FROM volunteer_donations WHERE volunteer_id = ?;
```

### Log Files

- Email errors: Check PHP error log
- SMS errors: Check PHP error log
- Payment errors: Check Stripe dashboard

## Troubleshooting

### SMS Not Working

1. Check Africa's Talking credentials
2. Verify sender ID is approved
3. Test with `test_sms.php`
4. Check phone number format (+250...)

### Payments Not Processing

1. Verify Stripe API keys
2. Check payment method settings
3. Review error logs
4. Test with Stripe test cards

### Email Not Sending

1. Verify SMTP settings
2. Check email credentials
3. Review PHP error log
4. Test with `test_email.php`

## Support

For technical support:

1. Check error logs
2. Verify all configurations
3. Test individual components
4. Review this setup guide

## Future Enhancements

Potential improvements:

- Recurring donations
- Donation campaigns
- Social sharing
- Analytics dashboard
- Mobile app integration
- USSD payments
- More payment methods

---

**Note**: This system is designed to be secure, scalable, and user-friendly. Regular backups and monitoring are recommended for production use.
