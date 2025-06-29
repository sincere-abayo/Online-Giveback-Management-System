# Volunteer Assignment Notification System Guide

## Overview

The Volunteer Assignment Notification System automatically sends email and SMS notifications to volunteers when they are assigned to activities by administrators. This system ensures volunteers are promptly informed about their new assignments and can prepare accordingly.

## Features

### 🔔 Automatic Notifications

- **Email Notifications**: Professional HTML emails with assignment details
- **SMS Notifications**: Concise text messages via Africa's Talking
- **Dual Delivery**: Both email and SMS are sent for maximum reach
- **Status Tracking**: Track which notifications were successfully sent

### 📧 Email Features

- **Professional Design**: Modern, responsive email template
- **Complete Information**: Activity details, program info, date, session
- **Action Buttons**: Direct links to volunteer dashboard and activities
- **Mobile Friendly**: Responsive design for all devices

### 📱 SMS Features

- **Template-Based**: Uses customizable SMS templates
- **Character Limit**: Automatically truncates to 160 characters
- **Variable Replacement**: Dynamic content insertion
- **Fallback Support**: Default message if template not found

## System Components

### 1. Database Structure

The system uses the existing `volunteer_history` table with added notification tracking columns:

```sql
-- Add notification tracking columns to volunteer_history table
ALTER TABLE `volunteer_history`
ADD COLUMN `email_sent` TINYINT(1) NOT NULL DEFAULT 0 AFTER `end_status`,
ADD COLUMN `sms_sent` TINYINT(1) NOT NULL DEFAULT 0 AFTER `email_sent`;
```

### 2. SMS Templates

SMS templates are stored in the `sms_templates` table:

```sql
INSERT INTO `sms_templates` (`template_name`, `message`, `is_active`) VALUES
('volunteer_assignment', 'Hello {firstname}! You have been assigned to {activity_name} ({program_name}) on {date}. Session: {session}. Dufatanye Charity Foundation', 1);
```

**Template Variables:**

- `{firstname}` - Volunteer's first name
- `{activity_name}` - Name of the assigned activity
- `{program_name}` - Name of the program
- `{date}` - Assignment date (formatted as M j, Y)
- `{session}` - Session/period information

### 3. Core Classes

#### MessagingService.php

- **Main notification orchestrator**
- Handles both email and SMS sending
- Manages template processing
- Tracks notification status

#### Master.php (Updated)

- **Integration point for notifications**
- Automatically triggers notifications on new assignments
- Only sends notifications for new assignments (not updates)

## Configuration

### 1. Environment Variables

Ensure these variables are set in your `.env` file:

```env
# Africa's Talking SMS Configuration
AFRICASTALKING_USERNAME=your_username
AFRICASTALKING_API_KEY=your_api_key
AFRICASTALKING_SENDER_ID=DUFATANYE

# SMTP Email Configuration
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your_email@gmail.com
SMTP_PASSWORD=your_app_password
FROM_EMAIL=noreply@dufatanye.org
FROM_NAME=Dufatanye Charity Foundation
```

### 2. Database Setup

Run the notification columns SQL:

```sql
-- Add notification tracking columns to volunteer_history table
ALTER TABLE `volunteer_history`
ADD COLUMN `email_sent` TINYINT(1) NOT NULL DEFAULT 0 AFTER `end_status`,
ADD COLUMN `sms_sent` TINYINT(1) NOT NULL DEFAULT 0 AFTER `email_sent`;
```

### 3. SMS Templates Setup

Insert the volunteer assignment SMS template:

```sql
INSERT INTO `sms_templates` (`template_name`, `message`, `is_active`) VALUES
('volunteer_assignment', 'Hello {firstname}! You have been assigned to {activity_name} ({program_name}) on {date}. Session: {session}. Dufatanye Charity Foundation', 1)
ON DUPLICATE KEY UPDATE message = VALUES(message), is_active = VALUES(is_active);
```

## How It Works

### 1. Assignment Creation Flow

1. **Admin assigns activity** to volunteer via admin panel
2. **System validates** assignment (no duplicates, future dates only)
3. **Assignment saved** to `volunteer_history` table
4. **Notification triggered** automatically for new assignments
5. **Email and SMS sent** simultaneously
6. **Status updated** in database (email_sent, sms_sent)

### 2. Notification Process

1. **Data Retrieval**: Fetch assignment details with volunteer and activity info
2. **Email Processing**: Generate HTML email with assignment details
3. **SMS Processing**: Apply template variables to SMS message
4. **Delivery**: Send both notifications via respective services
5. **Status Update**: Mark notifications as sent in database

### 3. Error Handling

- **Graceful Degradation**: If one notification fails, the other still sends
- **Logging**: All errors are logged for debugging
- **Fallback Messages**: Default SMS message if template not found
- **Status Tracking**: Failed notifications are marked accordingly

## Email Template Features

### Design Elements

- **Professional Header**: Gradient background with organization branding
- **Assignment Banner**: Highlighted section with assignment confirmation
- **Information Grid**: Organized display of assignment details
- **Action Buttons**: Direct links to volunteer dashboard
- **Responsive Design**: Works on all device sizes

### Content Sections

1. **Assignment Confirmation**: Clear notification of new assignment
2. **Activity Details**: Program, activity, date, and session information
3. **Volunteer Information**: ID and assignment date
4. **Expectations**: What to bring and how to prepare
5. **Important Reminders**: Attendance confirmation and contact info
6. **Action Links**: Quick access to dashboard and activities

## SMS Template Features

### Message Structure

- **Personalized Greeting**: Uses volunteer's first name
- **Activity Information**: Program and activity names
- **Date and Session**: Formatted date and session details
- **Organization Branding**: Clear sender identification

### Character Management

- **Automatic Truncation**: Limits to 160 characters
- **Ellipsis Addition**: Adds "..." when truncated
- **Template Optimization**: Designed to fit within limits

## Testing and Monitoring

### 1. Test File

Use `test_volunteer_notification.php` to:

- Test notification system with real data
- Verify configuration settings
- Check SMS template availability
- View recent assignments and notification status

### 2. Admin Panel Monitoring

The volunteer view page shows:

- **Email Status**: ✓ Sent or ✗ Not Sent indicators
- **SMS Status**: ✓ Sent or ✗ Not Sent indicators
- **Assignment History**: Complete list with notification tracking

### 3. Log Monitoring

Check error logs for:

- SMTP connection issues
- Africa's Talking API errors
- Template processing problems
- Database update failures

## Troubleshooting

### Common Issues

#### 1. Notifications Not Sending

**Symptoms**: No email/SMS received, status shows "Not Sent"
**Solutions**:

- Check environment variables in `.env` file
- Verify SMTP credentials and settings
- Confirm Africa's Talking API credentials
- Check server error logs

#### 2. Email Delivery Issues

**Symptoms**: Email status shows "Not Sent"
**Solutions**:

- Verify SMTP host and port settings
- Check email credentials and app passwords
- Ensure FROM_EMAIL is properly configured
- Test SMTP connection manually

#### 3. SMS Delivery Issues

**Symptoms**: SMS status shows "Not Sent"
**Solutions**:

- Verify Africa's Talking credentials
- Check account balance and sender ID approval
- Ensure phone number format is correct
- Test SMS API manually

#### 4. Template Issues

**Symptoms**: SMS shows fallback message
**Solutions**:

- Check if SMS template exists in database
- Verify template is active (is_active = 1)
- Ensure template variables are correct
- Check template message format

### Debug Steps

1. **Check Configuration**:

   ```php
   // Run test_volunteer_notification.php
   // Verify all environment variables are set
   ```

2. **Test Individual Components**:

   ```php
   // Test email sending
   require_once 'classes/MessagingService.php';
   $service = new MessagingService();
   $result = $service->sendVolunteerAssignmentEmail($assignment_data);
   ```

3. **Check Database**:

   ```sql
   -- Verify notification columns exist
   DESCRIBE volunteer_history;

   -- Check SMS templates
   SELECT * FROM sms_templates WHERE template_name = 'volunteer_assignment';
   ```

4. **Monitor Logs**:

   ```bash
   # Check PHP error logs
   tail -f /var/log/php_errors.log

   # Check application logs
   grep "volunteer assignment" /var/log/application.log
   ```

## Best Practices

### 1. Configuration Management

- **Environment Variables**: Use `.env` file for all sensitive data
- **Template Management**: Store SMS templates in database for easy updates
- **Error Logging**: Implement comprehensive error logging
- **Status Tracking**: Always track notification delivery status

### 2. User Experience

- **Dual Delivery**: Send both email and SMS for maximum reach
- **Professional Design**: Use consistent branding and professional templates
- **Clear Information**: Provide all necessary assignment details
- **Action Items**: Include clear next steps and contact information

### 3. System Reliability

- **Graceful Degradation**: System continues working if one notification fails
- **Fallback Messages**: Provide default messages if templates unavailable
- **Error Handling**: Comprehensive error handling and logging
- **Status Tracking**: Track delivery status for monitoring and debugging

### 4. Maintenance

- **Regular Testing**: Test notification system regularly
- **Template Updates**: Keep SMS templates current and relevant
- **Monitoring**: Monitor delivery rates and error logs
- **Backup**: Maintain backup notification methods

## Future Enhancements

### Potential Improvements

1. **Notification Preferences**: Allow volunteers to choose notification methods
2. **Reminder Notifications**: Send reminders before assignment dates
3. **Bulk Notifications**: Send notifications for multiple assignments
4. **Notification History**: Detailed notification delivery history
5. **Template Management**: Admin interface for managing SMS templates
6. **Delivery Reports**: Detailed delivery and read receipts
7. **Multi-language Support**: Support for multiple languages
8. **Push Notifications**: Mobile app push notifications

### Integration Opportunities

1. **Calendar Integration**: Add assignments to volunteer calendars
2. **Social Media**: Post assignment announcements to social media
3. **WhatsApp Integration**: Send notifications via WhatsApp Business API
4. **Voice Calls**: Automated voice call notifications for important assignments
5. **Dashboard Notifications**: Real-time notifications in volunteer dashboard

## Support and Maintenance

### Regular Tasks

- **Weekly**: Check notification delivery rates
- **Monthly**: Review and update SMS templates
- **Quarterly**: Test notification system end-to-end
- **Annually**: Review and update notification policies

### Contact Information

For technical support or questions about the notification system:

- **Email**: tech@dufatanye.org
- **Phone**: +250 788445566
- **Documentation**: This guide and inline code comments

---

**Last Updated**: January 2025
**Version**: 1.0
**System**: Dufatanye Charity Foundation Volunteer Management System
