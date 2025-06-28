# Volunteer Profile System

This directory contains the volunteer profile management system for the GMS (Giveback Management System).

## Files Overview

### `profile.php`

The main volunteer profile page that allows volunteers to:

- View their profile information
- Update personal details (name, contact, motivation)
- Change their password
- View their activity history
- See their account status

### `index.php`

Simple redirect file that sends users to the main volunteer dashboard.

## Features

### Profile Management

- **View Profile**: Display volunteer information including name, email, contact, motivation, and registration date
- **Update Profile**: Modify personal information with validation
- **Change Password**: Secure password change with current password verification
- **Activity History**: View all activities the volunteer has participated in

### Security Features

- Session-based authentication
- Password hashing using PHP's `password_hash()`
- Input validation and sanitization
- Prepared statements to prevent SQL injection
- Current password verification for password changes

### User Experience

- Modern, responsive design with gradient backgrounds
- Real-time password strength checking
- Password confirmation validation
- Success/error message display
- Auto-hiding alerts
- Mobile-friendly layout

## Access Control

- Only logged-in volunteers can access the profile page
- Volunteers can update their own information only
- Email address cannot be changed (for security reasons)
- Contact number uniqueness validation

## Database Integration

The profile system integrates with the following database tables:

- `volunteer_list`: Main volunteer information
- `volunteer_history`: Activity participation records
- `activity_list`: Activity details

## Navigation

The profile page includes navigation to:

- Main volunteer dashboard
- Logout functionality
- Homepage

## Styling

The profile page uses:

- Bootstrap 4 for responsive layout
- Font Awesome icons
- Custom CSS with gradient backgrounds
- Modern card-based design
- Consistent color scheme with the main GMS system

## Usage

1. Volunteers access the profile page from the main dashboard
2. They can view their current information
3. Update personal details as needed
4. Change their password securely
5. View their activity participation history

## Security Notes

- All form inputs are validated and sanitized
- Passwords are hashed using PHP's secure hashing functions
- Session management prevents unauthorized access
- SQL injection is prevented through prepared statements
- XSS attacks are prevented through proper output escaping
