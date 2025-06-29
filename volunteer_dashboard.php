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

        .dashboard-container {
            padding: 30px 0;
        }

        .dashboard-card {
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
        }

        .status-badge {
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
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
        }

        .btn-custom {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
            text-decoration: none;
        }

        .stats-card {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }

        .stats-card.bg-warning {
            background: linear-gradient(45deg, #ffc107, #ff8f00);
            color: #212529;
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

        .status-banner {
            border-left: 5px solid #ffc107;
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .status-banner h4 {
            color: #856404;
            font-weight: 600;
        }

        .status-banner p {
            color: #856404;
            margin-bottom: 10px;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .alert-warning {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            color: #856404;
            border-left: 4px solid #ffc107;
        }

        .alert-info {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="uploads/GMS.png" width="30" height="30" class="d-inline-block align-top" alt="GMS">
                GMS - Volunteer Dashboard
            </a>

            <div class="navbar-nav ml-auto">
                <span class="navbar-text mr-3">
                    Welcome, <?php echo htmlspecialchars($_SESSION['volunteer_name']); ?>
                </span>
                <a href="?logout=1" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="container">
            <!-- Status Banner for Pending Volunteers -->
            <?php if ($volunteer['status'] == 0): ?>
                <div class="dashboard-card status-banner">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <i class="fas fa-clock fa-3x text-warning"></i>
                        </div>
                        <div class="col-md-8">
                            <h4 class="text-warning mb-2"><i class="fas fa-exclamation-triangle"></i> Account Pending
                                Approval</h4>
                            <p class="mb-2">Your volunteer account is currently under review by our administrators. You can
                                still access your dashboard and view your information.</p>
                            <p class="mb-0"><strong>What happens next?</strong> Once approved, you'll be able to participate
                                in volunteer activities and receive notifications about upcoming events.</p>
                        </div>
                        <div class="col-md-2 text-center">
                            <button class="btn btn-outline-warning btn-sm" onclick="window.location.href='index.php'">
                                <i class="fas fa-home"></i> Go Home
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Profile Section -->
            <div class="dashboard-card">
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
                    <?php if ($volunteer['status'] == 0): ?>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Your account was registered on
                                <?php echo date('F j, Y', strtotime($volunteer['date_created'])); ?>
                            </small>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <strong>Email:</strong><br>
                                <?php echo htmlspecialchars($volunteer['email']); ?>
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

            <!-- Statistics -->
            <div class="row">
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number"><?php echo $activities->num_rows; ?></div>
                        <div class="stats-label">Activities Joined</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card <?php echo $volunteer['status'] == 0 ? 'bg-warning' : ''; ?>">
                        <div class="stats-number"><?php echo $volunteer['status'] == 1 ? 'Active' : 'Pending'; ?></div>
                        <div class="stats-label">Account Status</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number"><?php echo htmlspecialchars($volunteer['roll']); ?></div>
                        <div class="stats-label">Volunteer ID</div>
                    </div>
                </div>
            </div>

            <!-- Activities Section -->
            <div class="dashboard-card">
                <h3><i class="fas fa-tasks"></i> My Activities</h3>

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

            <!-- Donation History Section -->
            <div class="dashboard-card">
                <h3><i class="fas fa-heart"></i> My Donations</h3>

                <?php if ($donations->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
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
                    <div class="text-center py-4">
                        <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No donations yet</h5>
                        <p class="text-muted">You haven't made any donations yet. Start making a difference today!</p>
                        <a href="donation.php" class="btn btn-success">
                            <i class="fas fa-heart"></i> Make a Donation
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-card">
                <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                <div class="row">
                    <div class="col-md-3">
                        <a href="index.php" class="btn btn-custom btn-block">
                            <i class="fas fa-home"></i> Go to Homepage
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="volunteer/profile.php" class="btn btn-custom btn-block">
                            <i class="fas fa-user-edit"></i> Update Profile
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="volunteer/programs.php" class="btn btn-custom btn-block">
                            <i class="fas fa-project-diagram"></i> View Programs
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="?logout=1" class="btn btn-danger btn-block">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>

                <?php if ($volunteer['status'] == 0): ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-question-circle"></i>
                                <strong>Need help?</strong> If you have questions about your pending application,
                                please contact the administrator or visit our homepage for more information.
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>