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
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .profile-container {
            padding: 30px 0;
        }

        .profile-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
        }

        .profile-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f8f9fa;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 50px;
            color: white;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .status-badge {
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            display: inline-block;
            margin-top: 10px;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .form-section {
            margin-bottom: 30px;
        }

        .section-title {
            color: #667eea;
            font-weight: 700;
            font-size: 1.3rem;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f8f9fa;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }

        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-custom {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
            text-decoration: none;
        }

        .btn-secondary {
            background: #6c757d;
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.4);
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
        }

        .activity-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 5px solid #667eea;
            transition: all 0.3s ease;
        }

        .activity-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .password-strength {
            height: 5px;
            border-radius: 3px;
            margin-top: 5px;
            transition: all 0.3s ease;
        }

        .strength-weak {
            background: #dc3545;
        }

        .strength-medium {
            background: #ffc107;
        }

        .strength-strong {
            background: #28a745;
        }

        .required-field::after {
            content: " *";
            color: #dc3545;
        }

        .stats-card {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stats-label {
            font-size: 1rem;
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            .profile-card {
                padding: 20px;
                margin: 10px;
            }

            .section-title {
                font-size: 1.1rem;
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .btn-custom,
            .btn-secondary {
                display: block;
                width: 100%;
                margin: 5px 0;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <img src="../uploads/GMS.png" width="30" height="30" class="d-inline-block align-top" alt="GMS">
                GMS - Volunteer Profile
            </a>

            <div class="navbar-nav ml-auto">
                <span class="navbar-text mr-3">
                    Welcome, <?php echo htmlspecialchars($_SESSION['volunteer_name']); ?>
                </span>
                <a href="../volunteer_dashboard.php" class="btn btn-outline-primary btn-sm mr-2">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="programs.php" class="btn btn-outline-success btn-sm mr-2">
                    <i class="fas fa-project-diagram"></i> Programs
                </a>
                <a href="../logout.php" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="profile-container">
        <div class="container">
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

            <!-- Profile Header -->
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <h2><?php echo htmlspecialchars($volunteer['firstname'] . ' ' . $volunteer['lastname']); ?></h2>
                    <p class="text-muted">Volunteer ID: <?php echo htmlspecialchars($volunteer['roll']); ?></p>
                    <span
                        class="status-badge <?php echo $volunteer['status'] == 1 ? 'status-approved' : 'status-pending'; ?>">
                        <?php echo $volunteer['status'] == 1 ? 'Approved' : 'Pending Approval'; ?>
                    </span>
                </div>

                <!-- Statistics -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-number"><?php echo $activities->num_rows; ?></div>
                            <div class="stats-label">Activities Joined</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-number"><?php echo htmlspecialchars($volunteer['roll']); ?></div>
                            <div class="stats-label">Volunteer ID</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-number"><?php echo date('M Y', strtotime($volunteer['date_created'])); ?>
                            </div>
                            <div class="stats-label">Member Since</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Information -->
            <div class="profile-card">
                <h3 class="section-title">
                    <i class="fas fa-user-circle"></i>
                    Profile Information
                </h3>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <strong>Email:</strong><br>
                                <?php echo htmlspecialchars($volunteer['email']); ?>
                                <small class="text-muted d-block">(Cannot be changed)</small>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <strong>Contact:</strong><br>
                                <?php echo htmlspecialchars($volunteer['contact']); ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div>
                                <strong>Motivation:</strong><br>
                                <?php echo htmlspecialchars($volunteer['motivation']); ?>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div>
                                <strong>Registered:</strong><br>
                                <?php echo date('F j, Y', strtotime($volunteer['date_created'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Update Profile Form -->
            <div class="profile-card">
                <h3 class="section-title">
                    <i class="fas fa-edit"></i>
                    Update Profile
                </h3>

                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="firstname" class="required-field">First Name:</label>
                                <input type="text" class="form-control" id="firstname" name="firstname"
                                    value="<?php echo htmlspecialchars($volunteer['firstname']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="middlename">Middle Name:</label>
                                <input type="text" class="form-control" id="middlename" name="middlename"
                                    value="<?php echo htmlspecialchars($volunteer['middlename']); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lastname" class="required-field">Last Name:</label>
                                <input type="text" class="form-control" id="lastname" name="lastname"
                                    value="<?php echo htmlspecialchars($volunteer['lastname']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact" class="required-field">Contact Number:</label>
                                <input type="text" class="form-control" id="contact" name="contact"
                                    value="<?php echo htmlspecialchars($volunteer['contact']); ?>" pattern="\d{10}"
                                    maxlength="10" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                                <small class="form-text text-muted">Only 10 digits allowed.</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="motivation" class="required-field">Motivation:</label>
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

            <!-- Change Password Form -->
            <div class="profile-card">
                <h3 class="section-title">
                    <i class="fas fa-lock"></i>
                    Change Password
                </h3>

                <form method="POST" action="" id="passwordForm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="current_password" class="required-field">Current Password:</label>
                                <input type="password" class="form-control" id="current_password"
                                    name="current_password" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="new_password" class="required-field">New Password:</label>
                                <input type="password" class="form-control" id="new_password" name="new_password"
                                    minlength="6" required>
                                <div class="password-strength" id="passwordStrength"></div>
                                <small class="form-text text-muted">Password must be at least 6 characters long.</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="confirm_password" class="required-field">Confirm Password:</label>
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

            <!-- Activities Section -->
            <div class="profile-card">
                <h3 class="section-title">
                    <i class="fas fa-tasks"></i>
                    My Activities
                </h3>

                <?php if ($volunteer['status'] == 0): ?>
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> You can view activities here once your account is approved by the
                        administrator.
                    </div>
                <?php endif; ?>

                <?php if ($activities->num_rows > 0): ?>
                    <?php while ($activity = $activities->fetch_assoc()): ?>
                        <div class="activity-card">
                            <h5><?php echo htmlspecialchars($activity['activity_name']); ?></h5>
                            <p class="text-muted"><?php echo htmlspecialchars($activity['activity_description']); ?></p>
                            <div class="row">
                                <div class="col-md-6">
                                    <small><strong>Year:</strong> <?php echo htmlspecialchars($activity['year']); ?></small>
                                </div>
                                <div class="col-md-6">
                                    <small><strong>Status:</strong>
                                        <span
                                            class="badge badge-<?php echo $activity['status'] == 1 ? 'success' : 'warning'; ?>">
                                            <?php echo $activity['status'] == 1 ? 'Active' : 'Pending'; ?>
                                        </span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No activities yet</h5>
                        <p class="text-muted">You haven't joined any activities yet.
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

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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

        // Auto-hide alerts after 5 seconds
        setTimeout(function () {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>
</body>

</html>