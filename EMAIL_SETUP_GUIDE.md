# Email Setup Guide for GMS

This guide will help you set up email functionality for the Giveback Management System (GMS).

## Overview

The email system uses PHPMailer with Gmail SMTP to send welcome emails to new volunteers. It includes both HTML and text versions of emails with a professional design.

## Files Structure

```
├── classes/
│   └── SimpleEmailService.php    # Main email service class
├── email_settings.php            # Email configuration file
├── test_email.php               # Test script for email functionality
├── composer.json                # PHP dependencies
└── vendor/                      # PHPMailer library (installed via composer)
```

## Setup Instructions

### 1. Install Dependencies

Make sure you have Composer installed, then run:

```bash
composer install
```

This will install PHPMailer and other required dependencies.

### 2. Configure Gmail Account

To use Gmail SMTP, you need to:

1. **Enable 2-Factor Authentication** on your Gmail account
2. **Generate an App Password**:
   - Go to Google Account settings
   - Security → 2-Step Verification → App passwords
   - Generate a new app password for "Mail"
   - Copy the 16-character password

### 3. Update Email Configuration

Edit `email_settings.php` and update the following values:

```php
// Replace with your actual Gmail credentials
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-16-character-app-password');

// Update sender information
define('FROM_EMAIL', 'your-email@gmail.com');
define('FROM_NAME', 'Your Organization Name');
```

### 4. Test Email Functionality

1. Update `test_email.php` with your test email address:

   ```php
   $testEmail = 'your-test-email@example.com';
   ```

2. Run the test script in your browser:

   ```
   http://your-domain.com/test_email.php
   ```

3. Check if the email is received and displays correctly.

## Configuration Options

### Email Settings

| Setting           | Description                  | Default          |
| ----------------- | ---------------------------- | ---------------- |
| `SMTP_HOST`       | SMTP server hostname         | `smtp.gmail.com` |
| `SMTP_PORT`       | SMTP server port             | `587`            |
| `SMTP_ENCRYPTION` | Encryption type              | `tls`            |
| `ENABLE_EMAIL`    | Enable/disable email sending | `true`           |
| `EMAIL_DEBUG`     | Enable debug output          | `false`          |

### Disabling Email During Development

To disable email sending during development, set:

```php
define('ENABLE_EMAIL', false);
```

### Enabling Debug Mode

To see detailed SMTP communication, set:

```php
define('EMAIL_DEBUG', true);
```

## Email Templates

The system includes:

- **HTML Email**: Beautiful, responsive design with branding
- **Text Email**: Plain text fallback for email clients that don't support HTML

### Customizing Email Content

To customize email content, edit the `getWelcomeEmailHTML()` and `getWelcomeEmailText()` methods in `SimpleEmailService.php`.

## Troubleshooting

### Common Issues

1. **"Authentication failed" error**:

   - Ensure 2-Factor Authentication is enabled
   - Use the correct 16-character app password
   - Check that the email address is correct

2. **"Connection refused" error**:

   - Check if your server allows outbound SMTP connections
   - Verify firewall settings
   - Try using port 465 with SSL instead of 587 with TLS

3. **Emails not sending**:
   - Check error logs for detailed messages
   - Enable debug mode to see SMTP communication
   - Verify that `ENABLE_EMAIL` is set to `true`

### Debug Mode

To troubleshoot email issues, enable debug mode:

```php
define('EMAIL_DEBUG', true);
```

This will show detailed SMTP communication in the browser or error logs.

### Fallback System

The email system includes a fallback mechanism:

1. **PHPMailer**: Primary method using SMTP
2. **PHP mail()**: Fallback method if PHPMailer is unavailable

## Security Considerations

1. **Never commit credentials to version control**
2. **Use environment variables in production**
3. **Regularly rotate app passwords**
4. **Monitor email sending logs**

## Production Deployment

For production deployment:

1. Use environment variables for sensitive data
2. Set `EMAIL_DEBUG` to `false`
3. Configure proper error logging
4. Set up email monitoring and alerts
5. Consider using a dedicated email service (SendGrid, Mailgun, etc.)

## Support

If you encounter issues:

1. Check the error logs
2. Enable debug mode
3. Verify Gmail account settings
4. Test with a different email client
5. Contact your hosting provider for SMTP restrictions

## Files Cleanup

The following files were removed to simplify the system:

- `classes/EmailService.php` (replaced by SimpleEmailService.php)
- `email_config.php` (replaced by email_settings.php)

The new system is more streamlined and easier to maintain.
