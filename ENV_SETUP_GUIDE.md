# Environment Variables Setup Guide

## Overview

This guide explains how to set up environment variables for the Dufatanye Charity Foundation donation system using dotenv.

## What's Been Set Up

### ✅ Environment Variables

- **Payment Gateway Credentials** (Stripe, PayPal, MTN, Airtel)
- **SMS Configuration** (Africa's Talking)
- **Email Configuration** (SMTP settings)
- **Database Configuration**
- **Application Settings**

### ✅ Security Features

- **dotenv package** installed for secure credential management
- **Environment variables** loaded automatically
- **Fallback system** to database settings
- **Git ignore** configured to exclude .env files

## Quick Setup

### 1. Environment File Setup

Your `.env` file has been created with your credentials:

```env
# PayPal Configuration
PAYPAL_CLIENT_ID=cb3de54e-4900-11f0-ab56-dead131a2dd9
PAYPAL_CLIENT_SECRET=522f8f6a27effc0a15a685e455d8a0bfda39a3ee5e6b4b0d3255bfef95601890afd80709

# Africa's Talking SMS Configuration
AFRICASTALKING_USERNAME=Iot_project
AFRICASTALKING_API_KEY=atsk_6ccbe2174a56e50490d59c73c1f7177fc02e47c2cdecb5343b67e6680bc321677b10c4bd
AFRICASTALKING_SENDER_ID=DUFATANYE
```

### 2. Sync Configuration

Run the configuration update script to sync environment variables with the database:

```bash
# Visit this URL in your browser:
http://localhost/utb/GMS/update_config.php
```

This will:

- Update database payment settings with environment variables
- Show which settings were updated
- Display current environment variables (with sensitive data masked)

### 3. Test SMS

Test your SMS configuration:

```bash
# Visit this URL in your browser:
http://localhost/utb/GMS/test_sms.php
```

## Environment Variables Reference

### Payment Gateways

#### PayPal

```env
PAYPAL_CLIENT_ID=your_paypal_client_id
PAYPAL_CLIENT_SECRET=your_paypal_client_secret
PAYPAL_MODE=sandbox
```

#### Stripe

```env
STRIPE_PUBLISHABLE_KEY=pk_test_your_stripe_publishable_key
STRIPE_SECRET_KEY=sk_test_your_stripe_secret_key
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret
```

#### MTN Mobile Money

```env
MTN_API_KEY=your_mtn_api_key
MTN_API_SECRET=your_mtn_api_secret
```

#### Airtel Money

```env
AIRTEL_API_KEY=your_airtel_api_key
AIRTEL_API_SECRET=your_airtel_api_secret
```

### SMS Configuration (Africa's Talking)

```env
AFRICASTALKING_USERNAME=your_username
AFRICASTALKING_API_KEY=your_api_key
AFRICASTALKING_SENDER_ID=DUFATANYE
```

### Email Configuration

```env
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password
SMTP_FROM_EMAIL=noreply@dufatanye.org
SMTP_FROM_NAME=Dufatanye Charity Foundation
```

### Database Configuration

```env
DB_HOST=localhost
DB_NAME=gms
DB_USER=root
DB_PASS=your_password
```

### Application Configuration

```env
APP_NAME=Dufatanye Charity Foundation
APP_URL=http://localhost/utb/GMS
APP_ENV=development
```

## How It Works

### 1. Loading Environment Variables

The system loads environment variables in `config.php`:

```php
// Load environment variables
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
```

### 2. Accessing Environment Variables

Use the `env()` helper function or access `$_ENV` directly:

```php
// Using helper function
$api_key = env('AFRICASTALKING_API_KEY', 'default_value');

// Direct access
$api_key = $_ENV['AFRICASTALKING_API_KEY'] ?? 'default_value';
```

### 3. PaymentProcessor Integration

The PaymentProcessor class automatically uses environment variables:

```php
private function getPaymentSetting($method, $key)
{
    // First try to get from environment variables
    $env_key = strtoupper($method . '_' . $key);
    if (isset($_ENV[$env_key])) {
        return $_ENV[$env_key];
    }

    // Fallback to database settings
    // ... database lookup
}
```

## Security Best Practices

### 1. Never Commit .env Files

The `.gitignore` file excludes:

- `.env` (production credentials)
- `.env.local` (local overrides)
- `.env.production` (production-specific)

### 2. Use .env.example for Reference

The `.env.example` file shows the structure without real credentials:

```env
PAYPAL_CLIENT_ID=your_paypal_client_id_here
AFRICASTALKING_API_KEY=your_api_key_here
```

### 3. Environment-Specific Files

For different environments:

- **Development**: `.env`
- **Staging**: `.env.staging`
- **Production**: `.env.production`

## Troubleshooting

### Environment Variables Not Loading

1. **Check file permissions**:

   ```bash
   chmod 644 .env
   ```

2. **Verify dotenv installation**:

   ```bash
   composer install
   ```

3. **Check for syntax errors**:
   ```bash
   php -l .env
   ```

### Payment Settings Not Updating

1. **Run the update script**:

   ```
   http://localhost/utb/GMS/update_config.php
   ```

2. **Check database connection**:

   ```php
   // In config.php
   var_dump($conn->ping());
   ```

3. **Verify table exists**:
   ```sql
   SHOW TABLES LIKE 'payment_settings';
   ```

### SMS Not Working

1. **Test environment variables**:

   ```php
   echo $_ENV['AFRICASTALKING_USERNAME'];
   echo $_ENV['AFRICASTALKING_API_KEY'];
   ```

2. **Check Africa's Talking credentials**:
   - Verify username and API key
   - Ensure sender ID is approved
   - Test with `test_sms.php`

## Production Deployment

### 1. Set Production Environment Variables

```env
APP_ENV=production
APP_URL=https://yourdomain.com
```

### 2. Update Database Credentials

```env
DB_HOST=production_host
DB_NAME=production_db
DB_USER=production_user
DB_PASS=production_password
```

### 3. Use Production Payment Keys

```env
STRIPE_PUBLISHABLE_KEY=pk_live_...
STRIPE_SECRET_KEY=sk_live_...
PAYPAL_MODE=live
```

### 4. Configure Production Email

```env
SMTP_HOST=your_production_smtp
SMTP_USERNAME=your_production_email
SMTP_PASSWORD=your_production_password
```

## File Structure

```
├── .env                    # Environment variables (not in git)
├── .env.example           # Example environment file (in git)
├── .gitignore             # Excludes .env files
├── config.php             # Loads environment variables
├── update_config.php      # Syncs env vars with database
├── test_sms.php           # Tests SMS configuration
└── classes/
    └── PaymentProcessor.php # Uses environment variables
```

## Next Steps

1. **Update remaining credentials** in `.env`
2. **Run `update_config.php`** to sync settings
3. **Test SMS** with `test_sms.php`
4. **Test donations** with the donation form
5. **Monitor logs** for any errors

---

**Note**: Keep your `.env` file secure and never commit it to version control. The `.env.example` file serves as a template for other developers.
