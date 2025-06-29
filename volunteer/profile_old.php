<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../config.php';

// Check if volunteer is logged in
if (!isset($_SESSION['volunteer_id']) || $_SESSION['volunteer_type'] !== 'volunteer') {
    header("Location: ../volunteer_login.php");
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

// Handle profile update
$update_message = '';
$update_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $firstname = trim($_POST['firstname']);
    $middlename = trim($_POST['middlename']);
    $lastname = trim($_POST['lastname']);
    $contact = trim($_POST['contact']);
    $motivation = trim($_POST['motivation']);

    // Validate input
    $errors = [];

    if (empty($firstname)) {
        $errors[] = "First name is required";
    }

    if (empty($lastname)) {
        $errors[] = "Last name is required";
    }

    if (empty($contact)) {
        $errors[] = "Contact number is required";
    } elseif (!preg_match("/^\d{10}$/", $contact)) {
        $errors[] = "Contact number must be exactly 10 digits";
    }

    if (empty($motivation)) {
        $errors[] = "Motivation is required";
    }

    // Check if contact number is already used by another volunteer
    if (!empty($contact) && $contact !== $volunteer['contact']) {
        $check_contact = "SELECT id FROM volunteer_list WHERE contact = ? AND id != ?";
        $check_stmt = $conn->prepare($check_contact);
        $check_stmt->bind_param("si", $contact, $volunteer_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $errors[] = "Contact number is already registered by another volunteer";
        }
        $check_stmt->close();
    }

    if (empty($errors)) {
        // Update profile
        $update_sql = "UPDATE volunteer_list SET firstname = ?, middlename = ?, lastname = ?, contact = ?, motivation = ?, date_updated = NOW() WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssssi", $firstname, $middlename, $lastname, $contact, $motivation, $volunteer_id);

        if ($update_stmt->execute()) {
            $update_message = "Profile updated successfully!";
            $update_type = "success";

            // Update session data
            $_SESSION['volunteer_name'] = $firstname . ' ' . $lastname;

            // Refresh volunteer data
            $stmt->execute();
            $result = $stmt->get_result();
            $volunteer = $result->fetch_assoc();
        } else {
            $update_message = "Error updating profile: " . $update_stmt->error;
            $update_type = "error";
        }
        $update_stmt->close();
    } else {
        $update_message = implode("<br>", $errors);
        $update_type = "error";
    }
}

// Handle password change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $password_errors = [];

    // Verify current password
    if (!password_verify($current_password, $volunteer['password'])) {
        $password_errors[] = "Current password is incorrect";
    }

    if (strlen($new_password) < 6) {
        $password_errors[] = "New password must be at least 6 characters long";
    }

    if ($new_password !== $confirm_password) {
        $password_errors[] = "New passwords do not match";
    }

    if (empty($password_errors)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $password_sql = "UPDATE volunteer_list SET password = ?, date_updated = NOW() WHERE id = ?";
        $password_stmt = $conn->prepare($password_sql);
        $password_stmt->bind_param("si", $hashed_password, $volunteer_id);

        if ($password_stmt->execute()) {
            $update_message = "Password changed successfully!";
            $update_type = "success";
        } else {
            $update_message = "Error changing password: " . $password_stmt->error;
            $update_type = "error";
        }
        $password_stmt->close();
    } else {
        $update_message = implode("<br>", $password_errors);
        $update_type = "error";
    }
}

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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GMS - Volunteer Profile</title>
    <link rel="shortcut icon" href="../gms1.png" type="image/png">
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

        /* Profile Content */
        .profile-content {
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
            margin-bottom: 2rem;
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

        /* Form Styles */
        .form-control {
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
            background: var(--card-bg);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
            outline: none;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .required-field::after {
            content: " *";
            color: var(--danger-color);
        }

        /* Button Styles */
        .btn-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            color: white;
            font-weight: 500;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-custom:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
            color: white;
            text-decoration: none;
        }

        .btn-secondary {
            background: var(--text-secondary);
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            color: white;
            font-weight: 500;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-secondary:hover {
            background: #475569;
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
            color: white;
            text-decoration: none;
        }

        /* Alert Styles */
        .alert {
            border-radius: 0.75rem;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border-left: 4px solid var(--success-color);
        }

        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border-left: 4px solid var(--danger-color);
        }

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border-left: 4px solid var(--warning-color);
        }

        /* Password Strength */
        .password-strength {
            height: 4px;
            border-radius: 2px;
            margin-top: 0.5rem;
            transition: all 0.3s ease;
        }

        .strength-weak {
            background: var(--danger-color);
        }

        .strength-medium {
            background: var(--warning-color);
        }

        .strength-strong {
            background: var(--success-color);
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

            .profile-content {
                padding: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .profile-info {
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
                <img src="../uploads/GMS.png" alt="GMS">
                <span>GMS Dashboard</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-item">
                <a href="../volunteer_dashboard.php" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="profile.php" class="nav-link active">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="../volunteer_dashboard.php#activities" class="nav-link">
                    <i class="fas fa-tasks"></i>
                    <span>Activities</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="../volunteer_dashboard.php#donations" class="nav-link">
                    <i class="fas fa-heart"></i>
                    <span>Donations</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="../index.php" class="nav-link">
                    <i class="fas fa-home"></i>
                    <span>Homepage</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="programs.php" class="nav-link">
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

                <a href="../?logout=1" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <!-- Profile Content -->
        <div class="profile-content">
            <!-- Alert Messages -->
            <?php if (!empty($update_message)): ?>
                <div class="alert alert-<?php echo $update_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show"
                    role="alert">
                    <i
                        class="fas fa-<?php echo $update_type === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                    <?php echo $update_message; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">My Profile</h1>
                <p class="page-subtitle">Manage your volunteer profile and information</p>
            </div>

            <!-- Profile Overview -->
            <div class="card">
                <div class="card-body">
                    <div class="profile-card">
                        <div class="profile-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <h2 class="profile-name">
                            <?php echo htmlspecialchars($volunteer['firstname'] . ' ' . $volunteer['lastname']); ?></h2>
                        <p class="profile-id">Volunteer ID: <?php echo htmlspecialchars($volunteer['roll']); ?></p>
                        <span
                            class="status-badge <?php echo $volunteer['status'] == 1 ? 'status-approved' : 'status-pending'; ?>">
                            <?php echo $volunteer['status'] == 1 ? 'Approved' : 'Pending Approval'; ?>
                        </span>

                        <?php if ($volunteer['status'] == 0): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle"></i>
                                Your account was registered on
                                <?php echo date('F j, Y', strtotime($volunteer['date_created'])); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Statistics -->
                        <div class="stats-grid">
                            <div class="stat-card primary">
                                <div class="stat-number"><?php echo $activities->num_rows; ?></div>
                                <div class="stat-label">Activities Joined</div>
                            </div>
                            <div class="stat-card info">
                                <div class="stat-number"><?php echo htmlspecialchars($volunteer['roll']); ?></div>
                                <div class="stat-label">Volunteer ID</div>
                            </div>
                            <div class="stat-card success">
                                <div class="stat-number">
                                    <?php echo date('M Y', strtotime($volunteer['date_created'])); ?></div>
                                <div class="stat-label">Member Since</div>
                            </div>
                        </div>

                        <!-- Profile Information -->
                        <div class="profile-info">
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="info-content">
                                    <h6>Email Address</h6>
                                    <p><?php echo htmlspecialchars($volunteer['email']); ?></p>
                                    <small class="text-muted">(Cannot be changed)</small>
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

            <!-- Update Profile Form -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-edit"></i>
                    Update Profile
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="firstname" class="form-label required-field">First Name</label>
                                    <input type="text" class="form-control" id="firstname" name="firstname"
                                        value="<?php echo htmlspecialchars($volunteer['firstname']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="middlename" class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" id="middlename" name="middlename"
                                        value="<?php echo htmlspecialchars($volunteer['middlename']); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lastname" class="form-label required-field">Last Name</label>
                                    <input type="text" class="form-control" id="lastname" name="lastname"
                                        value="<?php echo htmlspecialchars($volunteer['lastname']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact" class="form-label required-field">Contact Number</label>
                                    <input type="text" class="form-control" id="contact" name="contact"
                                        value="<?php echo htmlspecialchars($volunteer['contact']); ?>" pattern="\d{10}"
                                        maxlength="10" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                                    <small class="form-text text-muted">Only 10 digits allowed.</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="motivation" class="form-label required-field">Motivation</label>
                            <textarea class="form-control" id="motivation" name="motivation" rows="4"
                                required><?php echo htmlspecialchars($volunteer['motivation']); ?></textarea>
                        </div>

                        <div class="text-center">
                            <button type="submit" name="update_profile" class="btn btn-custom">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                            <a href="../volunteer_dashboard.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password Form -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-lock"></i>
                    Change Password
                </div>
                <div class="card-body">
                    <form method="POST" action="" id="passwordForm">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="current_password" class="form-label required-field">Current
                                        Password</label>
                                    <input type="password" class="form-control" id="current_password"
                                        name="current_password" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="new_password" class="form-label required-field">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password"
                                        minlength="6" required>
                                    <div class="password-strength" id="passwordStrength"></div>
                                    <small class="form-text text-muted">Password must be at least 6 characters
                                        long.</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="confirm_password" class="form-label required-field">Confirm
                                        Password</label>
                                    <input type="password" class="form-control" id="confirm_password"
                                        name="confirm_password" required>
                                    <div id="passwordMatch"></div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" name="change_password" class="btn btn-custom">
                                <i class="fas fa-key"></i> Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Activities Section -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-tasks"></i>
                    My Activities
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
                                        <h5 class="activity-title"><?php echo htmlspecialchars($activity['activity_name']); ?>
                                        </h5>
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
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password strength checker
        document.getElementById('new_password').addEventListener('input', function () {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            let strength = 0;

            if (password.length >= 6) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;

            strengthBar.className = 'password-strength';
            if (strength < 2) {
                strengthBar.classList.add('strength-weak');
                strengthBar.style.width = '20%';
            } else if (strength < 4) {
                strengthBar.classList.add('strength-medium');
                strengthBar.style.width = '60%';
            } else {
                strengthBar.classList.add('strength-strong');
                strengthBar.style.width = '100%';
            }
        });

        // Password confirmation checker
        document.getElementById('confirm_password').addEventListener('input', function () {
            const password = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            const matchDiv = document.getElementById('passwordMatch');

            if (confirmPassword === '') {
                matchDiv.innerHTML = '';
            } else if (password === confirmPassword) {
                matchDiv.innerHTML = '<small class="text-success"><i class="fas fa-check"></i> Passwords match</small>';
            } else {
                matchDiv.innerHTML = '<small class="text-danger"><i class="fas fa-times"></i> Passwords do not match</small>';
            }
        });

        // Form validation
        document.getElementById('passwordForm').addEventListener('submit', function (e) {
            const password = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }

            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
                return false;
            }
        });

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function (event) {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.querySelector('.sidebar-toggle');

            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });

        // Handle window resize
        window.addEventListener('resize', function () {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth > 768) {
                sidebar.classList.remove('show');
            }
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function () {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>
</body>

</html>