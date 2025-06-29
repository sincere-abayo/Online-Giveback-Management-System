# Programs & Events Page

This page displays all programs with their activities and all events for volunteers in the GMS (Giveback Management System).

## Features

### Programs & Activities Tab

- **Program Overview**: Shows all active programs with descriptions
- **Activity Count**: Displays the number of activities in each program
- **Activity Details**: Lists all activities within each program
- **Volunteer Status**: Highlights activities that the volunteer has already joined
- **Visual Indicators**: Activities the volunteer has joined are marked with a "✓ Joined" badge

### Events Tab

- **Event Cards**: Displays upcoming events in a grid layout
- **Event Images**: Shows event images if available
- **Event Details**: Includes title, description, and scheduled date
- **Responsive Design**: Adapts to different screen sizes

### Statistics Dashboard

- **Active Programs**: Total number of active programs
- **Upcoming Events**: Total number of scheduled events
- **Your Activities**: Number of activities the volunteer has joined

## Navigation

The page includes navigation to:

- Main volunteer dashboard
- Volunteer profile page
- Logout functionality
- Homepage

## Access Control

- Only logged-in volunteers can access the page
- Volunteers can view all programs and events regardless of approval status
- Activities are highlighted based on volunteer's participation history

## Database Integration

The page integrates with the following database tables:

- `program_list`: Program information
- `activity_list`: Activity details linked to programs
- `events`: Event information
- `volunteer_history`: Volunteer activity participation records

## Styling

The page uses:

- Bootstrap 4 for responsive layout
- Font Awesome icons
- Custom CSS with gradient backgrounds
- Modern card-based design
- Tabbed interface for organization
- Hover effects and animations
- Mobile-friendly responsive design

## Usage

1. Volunteers access the page from the main dashboard or profile page
2. They can view all available programs and their activities
3. They can see which activities they have already joined
4. They can browse upcoming events
5. The page provides statistics about their participation

## Security Features

- Session-based authentication
- Input validation and sanitization
- Prepared statements to prevent SQL injection
- XSS prevention through proper output escaping

## File Location

- **Main File**: `volunteer/programs.php`
- **Access**: Available from volunteer dashboard and profile page
- **Dependencies**: Requires volunteer login session
