<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';
require_once 'classes/CurrencyConverter.php';

// Check if donation_id is provided
if (!isset($_GET['donation_id'])) {
    header("Location: donation.php");
    exit;
}

$donation_id = intval($_GET['donation_id']);

// Get donation details
$sql = "SELECT * FROM donations WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $donation_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: donation.php");
    exit;
}

$donation = $result->fetch_assoc();

// If session_id is provided (from Stripe Checkout), verify the payment
if (isset($_GET['session_id'])) {
    require_once 'vendor/autoload.php';

    // Get Stripe secret key
    $stripe_secret_key = $_ENV['STRIPE_SECRET_KEY'] ?? 'sk_test_your_stripe_secret_key';
    $stripe_key_sql = "SELECT setting_value FROM payment_settings WHERE payment_method = 'stripe' AND setting_key = 'secret_key' AND is_active = 1";
    $stripe_result = $conn->query($stripe_key_sql);
    if ($stripe_result->num_rows > 0) {
        $stripe_secret_key = $stripe_result->fetch_assoc()['setting_value'];
    }

    // Initialize Stripe
    \Stripe\Stripe::setApiKey($stripe_secret_key);

    try {
        // Retrieve the session to verify payment
        $session = \Stripe\Checkout\Session::retrieve($_GET['session_id']);

        if ($session->payment_status === 'paid') {
            // Payment is confirmed, update donation status if not already updated
            if ($donation['status'] !== 'completed') {
                require_once 'classes/PaymentProcessor.php';
                $paymentProcessor = new PaymentProcessor();
                $paymentProcessor->processWebhookPayment($donation_id, $session->payment_intent);

                // Refresh donation data
                $stmt->execute();
                $result = $stmt->get_result();
                $donation = $result->fetch_assoc();
            }
        }
    } catch (Exception $e) {
        error_log('Error verifying Stripe session: ' . $e->getMessage());
    }
}

// Get volunteer details if logged in
$volunteer = null;
if (isset($_SESSION['volunteer_id'])) {
    $volunteer_sql = "SELECT * FROM volunteer_list WHERE id = ?";
    $volunteer_stmt = $conn->prepare($volunteer_sql);
    $volunteer_stmt->bind_param("i", $_SESSION['volunteer_id']);
    $volunteer_stmt->execute();
    $volunteer_result = $volunteer_stmt->get_result();
    if ($volunteer_result->num_rows > 0) {
        $volunteer = $volunteer_result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Successful - Dufatanye Charity Foundation</title>
    <link rel="shortcut icon" href="gms1.png" type="image/png">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding: 20px 0;
    }

    .success-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        padding: 40px;
        max-width: 800px;
        margin: 0 auto;
    }

    .success-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(45deg, #28a745, #20c997);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 30px;
    }

    .success-icon i {
        font-size: 50px;
        color: white;
    }

    .donation-details {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 25px;
        margin: 30px 0;
    }

    .notification-status {
        background: #e3f2fd;
        border-radius: 15px;
        padding: 25px;
        margin: 20px 0;
    }

    .status-item {
        display: flex;
        align-items: center;
        margin: 10px 0;
        padding: 10px;
        background: white;
        border-radius: 8px;
    }

    .status-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }

    .status-icon.success {
        background: #d4edda;
        color: #155724;
    }

    .status-icon.pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-icon.failed {
        background: #f8d7da;
        color: #721c24;
    }

    .btn-custom {
        border-radius: 25px;
        padding: 12px 30px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-custom:hover {
        transform: translateY(-2px);
    }

    .social-share {
        background: #f3e5f5;
        border-radius: 15px;
        padding: 25px;
        margin: 20px 0;
    }

    .share-btn {
        display: inline-block;
        margin: 5px;
        padding: 10px 20px;
        border-radius: 20px;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .share-btn:hover {
        transform: translateY(-2px);
        color: white;
        text-decoration: none;
    }

    .share-btn.facebook {
        background: #3b5998;
    }

    .share-btn.twitter {
        background: #1da1f2;
    }

    .share-btn.whatsapp {
        background: #25d366;
    }

    .share-btn.telegram {
        background: #0088cc;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="success-card">
            <!-- Success Header -->
            <div class="text-center">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h1 class="text-success mb-3">Donation Successful!</h1>
                <p class="lead text-muted">Thank you for your generous contribution to Dufatanye Charity Foundation</p>
            </div>

            <!-- Donation Details -->
            <div class="donation-details">
                <h4><i class="fas fa-receipt"></i> Donation Summary</h4>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Reference Number:</strong><br>
                            <span
                                class="text-primary font-weight-bold"><?php echo htmlspecialchars($donation['donation_ref']); ?></span>
                        </p>

                        <p><strong>Amount:</strong><br>
                            <?php 
                            $currencyConverter = new CurrencyConverter();
                            $original_currency = $donation['original_currency'] ?? 'RWF';
                            $original_amount = $donation['original_amount'] ?? $donation['amount'];
                            ?>
                            <span class="text-success font-weight-bold h5">
                                <?php echo $currencyConverter->formatAmount($original_amount, $original_currency); ?>
                            </span>
                            <?php if ($original_currency !== 'RWF'): ?>
                            <br><small class="text-muted">
                                (≈ <?php echo number_format($donation['amount'], 0); ?> RWF)
                            </small>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Payment Method:</strong><br>
                            <span class="text-info"><?php echo ucfirst($donation['payment_method']); ?></span>
                        </p>

                        <p><strong>Transaction ID:</strong><br>
                            <span
                                class="text-muted"><?php echo htmlspecialchars($donation['payment_id'] ?? 'N/A'); ?></span>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Donor Name:</strong><br>
                            <span class="text-dark"><?php echo htmlspecialchars($donation['fullname']); ?></span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Date:</strong><br>
                            <span
                                class="text-muted"><?php echo date('F j, Y \a\t g:i A', strtotime($donation['created_at'])); ?></span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Notification Status -->
            <div class="notification-status">
                <h4><i class="fas fa-bell"></i> Notification Status</h4>

                <div class="status-item">
                    <div class="status-icon <?php echo $donation['email_sent'] ? 'success' : 'pending'; ?>">
                        <i class="fas fa-<?php echo $donation['email_sent'] ? 'check' : 'clock'; ?>"></i>
                    </div>
                    <div>
                        <strong>Email Confirmation</strong><br>
                        <small class="text-muted">
                            <?php if ($donation['email_sent']): ?>
                            ✅ Sent to <?php echo htmlspecialchars($donation['email']); ?>
                            <?php else: ?>
                            ⏳ Processing...
                            <?php endif; ?>
                        </small>
                    </div>
                </div>

                <div class="status-item">
                    <div class="status-icon <?php echo $donation['sms_sent'] ? 'success' : 'pending'; ?>">
                        <i class="fas fa-<?php echo $donation['sms_sent'] ? 'check' : 'clock'; ?>"></i>
                    </div>
                    <div>
                        <strong>SMS Confirmation</strong><br>
                        <small class="text-muted">
                            <?php if ($donation['sms_sent']): ?>
                            ✅ Sent to <?php echo htmlspecialchars($donation['phone']); ?>
                            <?php else: ?>
                            ⏳ Processing...
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
            </div>

            <!-- Social Sharing -->
            <div class="social-share">
                <h4><i class="fas fa-share-alt"></i> Share Your Generosity</h4>
                <p class="text-muted">Help us spread the word about Dufatanye Charity Foundation</p>

                <?php
                $share_text = "I just made a donation of " . number_format($donation['amount'], 0) . " RWF to Dufatanye Charity Foundation! Join me in making a difference. #Dufatanye #Charity #Rwanda";
                $share_url = urlencode('http://' . $_SERVER['HTTP_HOST'] . '/donation.php');
                ?>

                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>&quote=<?php echo urlencode($share_text); ?>"
                    class="share-btn facebook" target="_blank">
                    <i class="fab fa-facebook-f"></i> Facebook
                </a>

                <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode($share_text); ?>&url=<?php echo $share_url; ?>"
                    class="share-btn twitter" target="_blank">
                    <i class="fab fa-twitter"></i> Twitter
                </a>

                <a href="https://wa.me/?text=<?php echo urlencode($share_text . ' ' . $share_url); ?>"
                    class="share-btn whatsapp" target="_blank">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>

                <a href="https://t.me/share/url?url=<?php echo $share_url; ?>&text=<?php echo urlencode($share_text); ?>"
                    class="share-btn telegram" target="_blank">
                    <i class="fab fa-telegram-plane"></i> Telegram
                </a>
            </div>

            <!-- Action Buttons -->
            <div class="text-center mt-4">
                <?php if ($volunteer): ?>
                <a href="volunteer_dashboard.php" class="btn btn-primary btn-custom mr-3">
                    <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                </a>
                <?php else: ?>
                <a href="volunteer_login.php" class="btn btn-primary btn-custom mr-3">
                    <i class="fas fa-user"></i> Login to Track Donations
                </a>
                <a href="registration.php" class="btn btn-success btn-custom mr-3">
                    <i class="fas fa-user-plus"></i> Register as Volunteer
                </a>
                <?php endif; ?>

                <a href="donation.php" class="btn btn-outline-primary btn-custom">
                    <i class="fas fa-heart"></i> Make Another Donation
                </a>
            </div>

            <!-- Additional Information -->
            <div class="mt-4 text-center">
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i>
                    A receipt has been sent to your email. Keep this reference number for your records.
                </small>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>