<?php
include 'config.php';
require_once 'classes/PayPackHandler.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if donation ID is provided
if (!isset($_GET['donation_id']) || empty($_GET['donation_id'])) {
    header("Location: donation.php?error=Invalid donation ID");
    exit;
}

$donation_id = (int) $_GET['donation_id'];

// Get donation details
$sql = "SELECT * FROM donations WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $donation_id);
$stmt->execute();
$result = $stmt->get_result();
$donation = $result->fetch_assoc();

if (!$donation) {
    header("Location: donation.php?error=Donation not found");
    exit;
}

// Check if donation is already paid
if ($donation['status'] === 'completed') {
    header("Location: donation_success.php?donation_id=" . $donation_id);
    exit;
}

// Initialize PayPack handler
$paypackHandler = null;
try {
    $paypackHandler = new PayPackHandler();
    error_log("PayPack handler initialized successfully");
} catch (Exception $e) {
    error_log("Failed to initialize PayPack handler: " . $e->getMessage());
}

// Handle payment processing
$errors = [];
$success = false;
$paymentResult = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phoneNumber = trim($_POST['phone_number'] ?? '');
    error_log("Phone number received: '" . $phoneNumber . "'");

    if (empty($phoneNumber)) {
        $errors[] = 'Phone number is required for PayPack payment';
        error_log("Phone number is empty");
    } else {
        // Validate phone number format
        $cleanPhone = preg_replace('/[^0-9]/', '', $phoneNumber);
        if (strlen($cleanPhone) < 9 || strlen($cleanPhone) > 12) {
            $errors[] = 'Invalid phone number format. Please enter a valid Rwandan mobile number.';
            error_log("Invalid phone number length: " . strlen($cleanPhone));
        } else {
            error_log("Calling PayPack handler with phone: " . $phoneNumber);
            error_log("Donation ID: " . $donation_id);
            error_log("Amount: " . $donation['amount']);

            try {
                $paymentResult = $paypackHandler->initiateDonationPayment($donation_id, $donation['amount'], $phoneNumber);
                error_log("PayPack result: " . json_encode($paymentResult));

                if ($paymentResult['success']) {
                    $_SESSION['payment_transaction_id'] = $paymentResult['transaction_id'];
                    error_log("Payment successful, redirecting to status page");
                    header('Location: donation_payment_status.php?transaction_id=' . $paymentResult['transaction_id']);
                    exit;
                } else {
                    $errors[] = $paymentResult['message'];
                    error_log("PayPack payment failed: " . $paymentResult['message']);

                    // Store payment result for debugging
                    if (isset($paymentResult['debug_info'])) {
                        error_log("Debug info: " . json_encode($paymentResult['debug_info']));
                    }
                }
            } catch (Exception $e) {
                error_log("Exception during PayPack payment: " . $e->getMessage());
                $errors[] = 'Payment processing error: ' . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayPack Payment - Dufatanye Charity Foundation</title>
    <link rel="shortcut icon" href="gms1.png" type="image/png">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .payment-container {
        padding: 40px 0;
    }

    .payment-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        padding: 40px;
        margin-bottom: 30px;
        animation: slideInUp 0.8s ease-out;
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(50px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .payment-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .payment-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(45deg, #28a745, #20c997);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }

    .payment-icon i {
        font-size: 40px;
        color: white;
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

    .btn-pay {
        background: linear-gradient(45deg, #28a745, #20c997);
        border: none;
        border-radius: 25px;
        padding: 15px 40px;
        font-weight: 600;
        font-size: 18px;
        transition: all 0.3s ease;
        width: 100%;
    }

    .btn-pay:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
    }

    .donation-summary {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 30px;
    }

    .amount-display {
        font-size: 2.5rem;
        font-weight: bold;
        color: #28a745;
        text-align: center;
        margin: 20px 0;
    }

    .phone-input-group {
        position: relative;
    }

    .phone-prefix {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        font-weight: 500;
        z-index: 10;
    }

    .phone-input {
        padding-left: 50px !important;
    }

    .mobile-money-info {
        background: linear-gradient(45deg, #ff6b6b, #ee5a24);
        color: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 30px;
    }

    .error-message {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    </style>
</head>

<body>
    <div class="container payment-container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="payment-card">
                    <div class="payment-header">
                        <div class="payment-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-800 mb-2">Mobile Money Payment</h1>
                        <p class="text-gray-600">Complete your donation using PayPack</p>
                    </div>

                    <!-- Donation Summary -->
                    <div class="donation-summary">
                        <h3 class="text-center mb-3">Donation Summary</h3>
                        <div class="amount-display">
                            RWF <?php echo number_format($donation['amount'], 0); ?>
                        </div>
                        <div class="text-center">
                            <p class="mb-1"><strong>Donor:</strong>
                                <?php echo htmlspecialchars($donation['fullname']); ?></p>
                            <p class="mb-1"><strong>Reference:</strong>
                                <?php echo htmlspecialchars($donation['donation_ref']); ?></p>
                            <p class="mb-0"><strong>Date:</strong>
                                <?php echo date('F d, Y', strtotime($donation['created_at'])); ?></p>
                        </div>
                    </div>

                    <!-- Mobile Money Info -->
                    <div class="mobile-money-info">
                        <h4 class="mb-3"><i class="fas fa-info-circle mr-2"></i>PayPack Mobile Money</h4>
                        <p class="mb-2">PayPack supports both MTN and Airtel mobile money in Rwanda.</p>
                        <div class="row">
                            <div class="col-6">
                                <div class="text-center">
                                    <i class="fas fa-sim-card mb-2" style="font-size: 24px;"></i>
                                    <p class="mb-0"><strong>MTN</strong></p>
                                    <small>078X, 079X</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <i class="fas fa-wifi mb-2" style="font-size: 24px;"></i>
                                    <p class="mb-0"><strong>Airtel</strong></p>
                                    <small>072X, 073X</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error Messages -->
                    <?php if (!empty($errors)): ?>
                    <div class="error-message">
                        <h5><i class="fas fa-exclamation-triangle mr-2"></i>Payment Error:</h5>
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <!-- Payment Form -->
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="phone_number" class="form-label">
                                <i class="fas fa-phone mr-2"></i>Mobile Money Phone Number
                            </label>
                            <div class="phone-input-group">
                                <span class="phone-prefix">+250</span>
                                <input type="tel" id="phone_number" name="phone_number" class="form-control phone-input"
                                    placeholder="0781234567"
                                    value="<?php echo isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : ''; ?>"
                                    pattern="[0-9]{10}" maxlength="10" required>
                            </div>
                            <small class="form-text text-muted">
                                Enter your 10-digit mobile money number (e.g., 0781234567 for MTN or 0721234567 for
                                Airtel)
                            </small>
                        </div>

                        <button type="submit" class="btn btn-pay text-white">
                            <i class="fas fa-credit-card mr-2"></i>
                            Pay RWF <?php echo number_format($donation['amount'], 0); ?>
                        </button>
                    </form>

                    <!-- Back to Donation -->
                    <div class="text-center mt-4">
                        <a href="donation.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Donation Form
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
    // Phone number validation
    document.getElementById('phone_number').addEventListener('input', function(e) {
        // Remove any non-digit characters
        let value = e.target.value.replace(/\D/g, '');

        // Ensure it starts with 0
        if (value.length > 0 && value[0] !== '0') {
            value = '0' + value;
        }

        // Limit to 10 digits
        if (value.length > 10) {
            value = value.substring(0, 10);
        }

        e.target.value = value;
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const phone = document.getElementById('phone_number').value;
        const cleanPhone = phone.replace(/\D/g, '');

        if (cleanPhone.length !== 10) {
            e.preventDefault();
            alert('Please enter a valid 10-digit phone number (e.g., 0781234567)');
            return false;
        }

        if (!cleanPhone.startsWith('0')) {
            e.preventDefault();
            alert('Phone number must start with 0');
            return false;
        }
    });
    </script>
</body>

</html>