<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';
require_once 'classes/CurrencyConverter.php';

// Check if volunteer is logged in
if (!isset($_SESSION['volunteer_id']) || $_SESSION['volunteer_type'] !== 'volunteer') {
    header("Location: volunteer_login.php");
    exit;
}

// Get volunteer information
$volunteer_id = $_SESSION['volunteer_id'];
$sql = "SELECT * FROM volunteer_list WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $volunteer_id);
$stmt->execute();
$result = $stmt->get_result();
$volunteer = $result->fetch_assoc();

// Get volunteer activities
$activity_sql = "SELECT vh.*, al.name as activity_name, al.description as activity_description 
                 FROM volunteer_history vh 
                 JOIN activity_list al ON vh.activity_id = al.id 
                 WHERE vh.volunteer_id = ? 
                 ORDER BY vh.date_created DESC";
$activity_stmt = $conn->prepare($activity_sql);
$activity_stmt->bind_param("i", $volunteer_id);
$activity_stmt->execute();
$activities = $activity_stmt->get_result();

// Get volunteer donations
$donation_sql = "SELECT d.*, dh.status as history_status 
                 FROM donations d 
                 LEFT JOIN donation_history dh ON d.id = dh.donation_id 
                 WHERE d.volunteer_id = ? 
                 ORDER BY d.created_at DESC";
$donation_stmt = $conn->prepare($donation_sql);
$donation_stmt->bind_param("i", $volunteer_id);
$donation_stmt->execute();
$donations = $donation_stmt->get_result();

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GMS - Volunteer Dashboard</title>
    <link rel="shortcut icon" href="gms1.png" type="image/png">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
    :root {
        --primary-color: #4f46e5;
        --primary-dark: #3730a3;
        --secondary-color: #06b6d4;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --info-color: #3b82f6;
        --light-bg: #f8fafc;
        --card-bg: #ffffff;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --border-color: #e2e8f0;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        color: var(--text-primary);
        line-height: 1.6;
    }

    /* Sidebar Styles */
    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        width: 280px;
        background: var(--card-bg);
        box-shadow: var(--shadow-xl);
        z-index: 1000;
        transition: all 0.3s ease;
        overflow-y: auto;
    }

    .sidebar-header {
        padding: 2rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
    }

    .sidebar-brand {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.25rem;
        font-weight: 700;
    }

    .sidebar-brand img {
        width: 32px;
        height: 32px;
    }

    .sidebar-nav {
        padding: 1.5rem 0;
    }

    .nav-item {
        margin-bottom: 0.25rem;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.875rem 1.5rem;
        color: var(--text-secondary);
        text-decoration: none;
        transition: all 0.2s ease;
        border-radius: 0;
        font-weight: 500;
    }

    .nav-link:hover {
        background: var(--light-bg);
        color: var(--primary-color);
        text-decoration: none;
    }

    .nav-link.active {
        background: var(--primary-color);
        color: white;
    }

    .nav-link i {
        width: 20px;
        text-align: center;
    }

    /* Main Content */
    .main-content {
        margin-left: 280px;
        min-height: 100vh;
        background: var(--light-bg);
    }

    .top-navbar {
        background: var(--card-bg);
        border-bottom: 1px solid var(--border-color);
        padding: 1rem 2rem;
        box-shadow: var(--shadow-sm);
    }

    .top-navbar-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
    }

    .user-details h6 {
        margin: 0;
        font-weight: 600;
        color: var(--text-primary);
    }

    .user-details small {
        color: var(--text-secondary);
    }

    .logout-btn {
        background: var(--danger-color);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .logout-btn:hover {
        background: #dc2626;
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
    }

    /* Dashboard Content */
    .dashboard-content {
        padding: 2rem;
    }

    .page-header {
        margin-bottom: 2rem;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .page-subtitle {
        color: var(--text-secondary);
        font-size: 1.1rem;
    }

    /* Cards */
    .card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 1rem;
        box-shadow: var(--shadow-sm);
        transition: all 0.2s ease;
        overflow: hidden;
    }

    .card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .card-header {
        background: var(--light-bg);
        border-bottom: 1px solid var(--border-color);
        padding: 1.5rem;
        font-weight: 600;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    /* Status Banner */
    .status-banner {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 1px solid #f59e0b;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .status-icon {
        width: 60px;
        height: 60px;
        background: #f59e0b;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }

    .status-content h4 {
        color: #92400e;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .status-content p {
        color: #92400e;
        margin-bottom: 0.5rem;
    }

    /* Profile Section */
    .profile-card {
        text-align: center;
        padding: 2rem;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 3rem;
        color: white;
        box-shadow: var(--shadow-lg);
    }

    .profile-name {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .profile-id {
        color: var(--text-secondary);
        font-size: 1rem;
        margin-bottom: 1rem;
    }

    .status-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 1.5rem;
    }

    .status-approved {
        background: #dcfce7;
        color: #166534;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .profile-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--light-bg);
        border-radius: 0.75rem;
        border: 1px solid var(--border-color);
    }

    .info-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
    }

    .info-content h6 {
        margin: 0;
        font-weight: 600;
        color: var(--text-primary);
    }

    .info-content p {
        margin: 0;
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--card-bg);
        border-radius: 1rem;
        padding: 1.5rem;
        text-align: center;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        transition: all 0.2s ease;
    }

    .stat-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .stat-card.primary {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
    }

    .stat-card.success {
        background: linear-gradient(135deg, var(--success-color), #059669);
        color: white;
    }

    .stat-card.warning {
        background: linear-gradient(135deg, var(--warning-color), #d97706);
        color: white;
    }

    .stat-card.info {
        background: linear-gradient(135deg, var(--info-color), #2563eb);
        color: white;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 0.875rem;
        opacity: 0.9;
        font-weight: 500;
    }

    /* Activity Cards */
    .activity-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.2s ease;
        border-left: 4px solid var(--primary-color);
    }

    .activity-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-1px);
    }

    .activity-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .activity-title {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .activity-description {
        color: var(--text-secondary);
        font-size: 0.875rem;
        line-height: 1.5;
    }

    .activity-meta {
        display: flex;
        gap: 1rem;
        font-size: 0.875rem;
    }

    .activity-meta span {
        color: var(--text-secondary);
    }

    .activity-status {
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-active {
        background: #dcfce7;
        color: #166534;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    /* Table Styles */
    .table-container {
        background: var(--card-bg);
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .table {
        margin: 0;
    }

    .table th {
        background: var(--light-bg);
        border: none;
        padding: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .table td {
        padding: 1rem;
        border: none;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background: var(--light-bg);
    }

    .badge {
        padding: 0.5rem 0.75rem;
        border-radius: 0.5rem;
        font-weight: 500;
        font-size: 0.75rem;
    }

    /* Quick Actions */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .action-btn {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.5rem;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        text-decoration: none;
        color: var(--text-primary);
        font-weight: 500;
        transition: all 0.2s ease;
        text-align: left;
    }

    .action-btn:hover {
        background: var(--primary-color);
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .action-btn i {
        font-size: 1.25rem;
        width: 24px;
    }

    /* Empty States */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }

    .empty-state i {
        font-size: 4rem;
        color: var(--text-secondary);
        margin-bottom: 1rem;
    }

    .empty-state h5 {
        color: var(--text-secondary);
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: var(--text-secondary);
        margin-bottom: 1.5rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.show {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0;
        }

        .dashboard-content {
            padding: 1rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .profile-info {
            grid-template-columns: 1fr;
        }

        .quick-actions {
            grid-template-columns: 1fr;
        }
    }

    /* Toggle Button for Mobile */
    .sidebar-toggle {
        display: none;
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 0.5rem;
        border-radius: 0.5rem;
        font-size: 1.25rem;
    }

    @media (max-width: 768px) {
        .sidebar-toggle {
            display: block;
        }
    }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <img src="uploads/GMS.png" alt="GMS">
                <span>GMS Dashboard</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-item">
                <a href="#dashboard" class="nav-link active" onclick="showSection('dashboard')">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="#profile" class="nav-link" onclick="showSection('profile')">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="#activities" class="nav-link" onclick="showSection('activities')">
                    <i class="fas fa-tasks"></i>
                    <span>Activities</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="#donations" class="nav-link" onclick="showSection('donations')">
                    <i class="fas fa-heart"></i>
                    <span>Donations</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="index.php" class="nav-link">
                    <i class="fas fa-home"></i>
                    <span>Homepage</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="volunteer/profile.php" class="nav-link">
                    <i class="fas fa-user-edit"></i>
                    <span>Edit Profile</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="volunteer/programs.php" class="nav-link">
                    <i class="fas fa-project-diagram"></i>
                    <span>Programs</span>
                </a>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="top-navbar-content">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($volunteer['firstname'], 0, 1)); ?>
                    </div>
                    <div class="user-details">
                        <h6><?php echo htmlspecialchars($volunteer['firstname'] . ' ' . $volunteer['lastname']); ?></h6>
                        <small>Volunteer</small>
                    </div>
                </div>

                <a href="?logout=1" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <!-- Status Banner for Pending Volunteers -->
            <?php if ($volunteer['status'] == 0): ?>
            <div class="status-banner">
                <div class="status-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="status-content">
                    <h4><i class="fas fa-exclamation-triangle"></i> Account Pending Approval</h4>
                    <p>Your volunteer account is currently under review by our administrators. You can still access your
                        dashboard and view your information.</p>
                    <p><strong>What happens next?</strong> Once approved, you'll be able to participate in volunteer
                        activities and receive notifications about upcoming events.</p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Dashboard Section -->
            <div id="dashboard-section" class="dashboard-section">
                <div class="page-header">
                    <h1 class="page-title">Welcome back, <?php echo htmlspecialchars($volunteer['firstname']); ?>!</h1>
                    <p class="page-subtitle">Here's an overview of your volunteer activities and contributions</p>
                </div>

                <!-- Statistics -->
                <div class="stats-grid">
                    <div class="stat-card primary">
                        <div class="stat-number"><?php echo $activities->num_rows; ?></div>
                        <div class="stat-label">Activities Joined</div>
                    </div>
                    <div class="stat-card <?php echo $volunteer['status'] == 1 ? 'success' : 'warning'; ?>">
                        <div class="stat-number"><?php echo $volunteer['status'] == 1 ? 'Active' : 'Pending'; ?></div>
                        <div class="stat-label">Account Status</div>
                    </div>
                    <div class="stat-card info">
                        <div class="stat-number"><?php echo $donations->num_rows; ?></div>
                        <div class="stat-label">Total Donations</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo htmlspecialchars($volunteer['roll']); ?></div>
                        <div class="stat-label">Volunteer ID</div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-bolt"></i>
                        Quick Actions
                    </div>
                    <div class="card-body">
                        <div class="quick-actions">
                            <a href="index.php" class="action-btn">
                                <i class="fas fa-home"></i>
                                <span>Go to Homepage</span>
                            </a>
                            <a href="volunteer/profile.php" class="action-btn">
                                <i class="fas fa-user-edit"></i>
                                <span>Update Profile</span>
                            </a>
                            <a href="volunteer/programs.php" class="action-btn">
                                <i class="fas fa-project-diagram"></i>
                                <span>View Programs</span>
                            </a>
                            <a href="donation.php" class="action-btn">
                                <i class="fas fa-heart"></i>
                                <span>Make Donation</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Section -->
            <div id="profile-section" class="dashboard-section" style="display: none;">
                <div class="page-header">
                    <h1 class="page-title">My Profile</h1>
                    <p class="page-subtitle">Manage your volunteer profile and information</p>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="profile-card">
                            <div class="profile-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <h2 class="profile-name">
                                <?php echo htmlspecialchars($volunteer['firstname'] . ' ' . $volunteer['lastname']); ?>
                            </h2>
                            <p class="profile-id">Volunteer ID: <?php echo htmlspecialchars($volunteer['roll']); ?></p>
                            <span
                                class="status-badge <?php echo $volunteer['status'] == 1 ? 'status-approved' : 'status-pending'; ?>">
                                <?php echo $volunteer['status'] == 1 ? 'Approved' : 'Pending Approval'; ?>
                            </span>

                            <?php if ($volunteer['status'] == 0): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                Your account was registered on
                                <?php echo date('F j, Y', strtotime($volunteer['date_created'])); ?>
                            </div>
                            <?php endif; ?>

                            <div class="profile-info">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div class="info-content">
                                        <h6>Email Address</h6>
                                        <p><?php echo htmlspecialchars($volunteer['email']); ?></p>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <div class="info-content">
                                        <h6>Contact Number</h6>
                                        <p><?php echo htmlspecialchars($volunteer['contact']); ?></p>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-heart"></i>
                                    </div>
                                    <div class="info-content">
                                        <h6>Motivation</h6>
                                        <p><?php echo htmlspecialchars($volunteer['motivation']); ?></p>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-calendar"></i>
                                    </div>
                                    <div class="info-content">
                                        <h6>Registration Date</h6>
                                        <p><?php echo date('F j, Y', strtotime($volunteer['date_created'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activities Section -->
            <div id="activities-section" class="dashboard-section" style="display: none;">
                <div class="page-header">
                    <h1 class="page-title">My Activities</h1>
                    <p class="page-subtitle">Track your volunteer activities and contributions</p>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-tasks"></i>
                        Activity History
                    </div>
                    <div class="card-body">
                        <?php if ($volunteer['status'] == 0): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> You can view activities here once your account is approved by the
                            administrator.
                        </div>
                        <?php endif; ?>

                        <?php if ($activities->num_rows > 0): ?>
                        <?php while ($activity = $activities->fetch_assoc()): ?>
                        <div class="activity-card">
                            <div class="activity-header">
                                <div>
                                    <h5 class="activity-title">
                                        <?php echo htmlspecialchars($activity['activity_name']); ?></h5>
                                    <p class="activity-description">
                                        <?php echo htmlspecialchars($activity['activity_description']); ?></p>
                                </div>
                                <span
                                    class="activity-status <?php echo $activity['status'] == 1 ? 'status-active' : 'status-pending'; ?>">
                                    <?php echo $activity['status'] == 1 ? 'Active' : 'Pending'; ?>
                                </span>
                            </div>
                            <div class="activity-meta">
                                <span><strong>Year:</strong> <?php echo htmlspecialchars($activity['year']); ?></span>
                                <span><strong>Date Created:</strong>
                                    <?php echo date('M j, Y', strtotime($activity['date_created'])); ?></span>
                            </div>
                        </div>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-clipboard-list"></i>
                            <h5>No activities yet</h5>
                            <p>You haven't joined any activities yet.
                                <?php if ($volunteer['status'] == 0): ?>
                                Once your account is approved, you'll be able to join activities.
                                <?php else: ?>
                                Contact the administrator to get started!
                                <?php endif; ?>
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Donations Section -->
            <div id="donations-section" class="dashboard-section" style="display: none;">
                <div class="page-header">
                    <h1 class="page-title">My Donations</h1>
                    <p class="page-subtitle">Track your donation history and contributions</p>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-heart"></i>
                        Donation History
                    </div>
                    <div class="card-body">
                        <?php if ($donations->num_rows > 0): ?>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Notifications</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($donation = $donations->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($donation['donation_ref']); ?></strong>
                                        </td>
                                        <td>
                                            <?php
                                                    $currencyConverter = new CurrencyConverter();
                                                    $original_currency = $donation['original_currency'] ?? 'RWF';
                                                    $original_amount = $donation['original_amount'] ?? $donation['amount'];
                                                    ?>
                                            <span class="text-success font-weight-bold">
                                                <?php echo $currencyConverter->formatAmount($original_amount, $original_currency); ?>
                                            </span>
                                            <?php if ($original_currency !== 'RWF'): ?>
                                            <br><small class="text-muted">
                                                (≈ <?php echo number_format($donation['amount'], 0); ?> RWF)
                                            </small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                <?php echo ucfirst($donation['payment_method']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                                    $status_class = '';
                                                    $status_text = '';
                                                    switch ($donation['status']) {
                                                        case 'completed':
                                                            $status_class = 'success';
                                                            $status_text = 'Completed';
                                                            break;
                                                        case 'pending':
                                                            $status_class = 'warning';
                                                            $status_text = 'Pending';
                                                            break;
                                                        case 'failed':
                                                            $status_class = 'danger';
                                                            $status_text = 'Failed';
                                                            break;
                                                        default:
                                                            $status_class = 'secondary';
                                                            $status_text = ucfirst($donation['status']);
                                                    }
                                                    ?>
                                            <span class="badge badge-<?php echo $status_class; ?>">
                                                <?php echo $status_text; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo date('M j, Y', strtotime($donation['created_at'])); ?>
                                        </td>
                                        <td>
                                            <?php if ($donation['email_sent']): ?>
                                            <i class="fas fa-envelope text-success" title="Email sent"></i>
                                            <?php endif; ?>
                                            <?php if ($donation['sms_sent']): ?>
                                            <i class="fas fa-sms text-info" title="SMS sent"></i>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-heart"></i>
                            <h5>No donations yet</h5>
                            <p>You haven't made any donations yet. Start making a difference today!</p>
                            <a href="donation.php" class="btn btn-success">
                                <i class="fas fa-heart"></i> Make a Donation
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function showSection(sectionName) {
        // Hide all sections
        document.querySelectorAll('.dashboard-section').forEach(section => {
            section.style.display = 'none';
        });

        // Show selected section
        document.getElementById(sectionName + '-section').style.display = 'block';

        // Update active nav link
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });
        event.target.classList.add('active');
    }

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('show');
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.querySelector('.sidebar-toggle');

        if (window.innerWidth <= 768) {
            if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                sidebar.classList.remove('show');
            }
        }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        const sidebar = document.getElementById('sidebar');
        if (window.innerWidth > 768) {
            sidebar.classList.remove('show');
        }
    });
    </script>
</body>

</html>