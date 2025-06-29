<?php
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

// Get Stripe publishable key
$stripe_key_sql = "SELECT setting_value FROM payment_settings WHERE payment_method = 'stripe' AND setting_key = 'publishable_key' AND is_active = 1";
$stripe_result = $conn->query($stripe_key_sql);
$stripe_key = $_ENV['STRIPE_PUBLISHABLE_KEY'] ?? 'pk_test_your_stripe_publishable_key'; // Default for demo

if ($stripe_result->num_rows > 0) {
    $stripe_key = $stripe_result->fetch_assoc()['setting_value'];
}

// Handle payment processing
$payment_message = '';
$payment_type = '';

// Check if payment was cancelled
if (isset($_GET['cancelled'])) {
    $payment_message = 'Payment was cancelled. You can try again or choose a different payment method.';
    $payment_type = "warning";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Payment - Dufatanye Charity Foundation</title>
    <link rel="shortcut icon" href="gms1.png" type="image/png">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .payment-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        padding: 40px;
        max-width: 900px;
        width: 90%;
    }

    .payment-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .payment-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(45deg, #6772e5, #6772e5);
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

    .donation-summary {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 30px;
    }

    .btn-pay {
        background: linear-gradient(45deg, #6772e5, #6772e5);
        border: none;
        border-radius: 25px;
        padding: 15px 40px;
        font-weight: 600;
        font-size: 18px;
        transition: all 0.3s ease;
        width: 100%;
        color: white;
    }

    .btn-pay:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(103, 114, 229, 0.4);
    }

    .payment-options {
        margin: 20px 0;
    }

    .payment-option {
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 15px;
        margin: 10px 0;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .payment-option:hover {
        border-color: #6772e5;
        background: #f0f2ff;
    }

    .payment-option.selected {
        border-color: #6772e5;
        background: #f0f2ff;
    }
    </style>
</head>

<body>
    <div class="payment-card">
        <div class="payment-header">
            <div class="payment-icon">
                <i class="fab fa-stripe"></i>
            </div>
            <h2>Complete Your Payment</h2>
            <p class="text-muted">Secure payment powered by Stripe</p>
        </div>

        <?php if (!empty($payment_message)): ?>
        <div class="alert alert-<?php echo $payment_type === 'error' ? 'danger' : ($payment_type === 'warning' ? 'warning' : 'success'); ?>"
            role="alert">
            <i
                class="fas fa-<?php echo $payment_type === 'error' ? 'exclamation-triangle' : ($payment_type === 'warning' ? 'exclamation-circle' : 'check-circle'); ?>"></i>
            <?php echo $payment_message; ?>
        </div>
        <?php endif; ?>

        <!-- Donation Summary -->
        <div class="donation-summary">
            <h5><i class="fas fa-receipt"></i> Donation Summary</h5>
            <div class="row">
                <div class="col-6">
                    <strong>Reference:</strong><br>
                    <span class="text-muted"><?php echo htmlspecialchars($donation['donation_ref']); ?></span>
                </div>
                <div class="col-6">
                    <strong>Amount:</strong><br>
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
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-6">
                    <strong>Donor:</strong><br>
                    <span class="text-muted"><?php echo htmlspecialchars($donation['fullname']); ?></span>
                </div>
                <div class="col-6">
                    <strong>Date:</strong><br>
                    <span class="text-muted"><?php echo date('M j, Y', strtotime($donation['created_at'])); ?></span>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <form action="create_stripe_checkout.php" method="POST" id="stripe-checkout-form">
            <input type="hidden" name="donation_id" value="<?php echo $donation_id; ?>">

            <!-- Payment Options -->
            <div class="payment-options mb-4">
                <h5><i class="fas fa-credit-card"></i> Choose Payment Method</h5>

                <!-- Stripe Checkout Option -->
                <div class="payment-option selected"
                    onclick="document.getElementById('stripe-checkout-form').submit();">
                    <div class="row align-items-center">
                        <div class="col-2">
                            <i class="fab fa-stripe fa-2x text-primary"></i>
                        </div>
                        <div class="col-8">
                            <strong>Credit/Debit Card</strong><br>
                            <small class="text-muted">Visa, Mastercard, American Express</small>
                        </div>
                        <div class="col-2 text-right">
                            <i class="fas fa-arrow-right text-primary"></i>
                        </div>
                    </div>
                </div>


            </div>

            <button type="submit" class="btn btn-primary btn-pay">
                <i class="fas fa-lock"></i> Pay <?php echo $currencyConverter->formatAmount($original_amount, $original_currency); ?> Securely
            </button>
        </form>

        <div class="text-center mt-3">
            <small class="text-muted">
                <i class="fas fa-shield-alt"></i> Your payment is secure and encrypted
            </small>
        </div>

        <div class="text-center mt-3">
            <a href="donation.php" class="btn btn-link">
                <i class="fas fa-arrow-left"></i> Back to Donation
            </a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>