<?php
include 'config.php';

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

// Handle payment processing
$payment_message = '';
$payment_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['process_payment'])) {
    require_once 'classes/PaymentProcessor.php';
    $paymentProcessor = new PaymentProcessor();

    $phone = $_POST['phone'];
    $amount = $donation['amount'];

    $result = $paymentProcessor->processMTNPayment($donation_id, $phone, $amount);

    if ($result['success']) {
        header("Location: donation_success.php?donation_id=" . $donation_id);
        exit;
    } else {
        $payment_message = $result['message'];
        $payment_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MTN Mobile Money Payment - Dufatanye Charity Foundation</title>
    <link rel="shortcut icon" href="gms1.png" type="image/png">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
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
            max-width: 500px;
            width: 90%;
        }

        .payment-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .payment-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #ffc107, #fd7e14);
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

        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
        }

        .btn-pay {
            background: linear-gradient(45deg, #ffc107, #fd7e14);
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
            box-shadow: 0 5px 15px rgba(255, 193, 7, 0.4);
        }

        .alert {
            border-radius: 10px;
            border: none;
        }
    </style>
</head>

<body>
    <div class="payment-card">
        <div class="payment-header">
            <div class="payment-icon">
                <i class="fas fa-mobile-alt"></i>
            </div>
            <h2>MTN Mobile Money Payment</h2>
            <p class="text-muted">Complete your donation with MTN MoMo</p>
        </div>

        <?php if (!empty($payment_message)): ?>
            <div class="alert alert-<?php echo $payment_type === 'error' ? 'danger' : 'success'; ?>" role="alert">
                <i class="fas fa-<?php echo $payment_type === 'error' ? 'exclamation-triangle' : 'check-circle'; ?>"></i>
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
                    <span class="text-success font-weight-bold"><?php echo number_format($donation['amount'], 0); ?>
                        RWF</span>
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
        <form method="post" action="">
            <div class="form-group">
                <label for="phone">MTN Mobile Money Number *</label>
                <input type="tel" class="form-control" id="phone" name="phone"
                    value="<?php echo htmlspecialchars($donation['phone']); ?>" pattern="[0-9]{10}" maxlength="10"
                    required>
                <small class="form-text text-muted">Enter your MTN MoMo number (e.g., 0781234567)</small>
            </div>

            <div class="alert alert-info">
                <h6><i class="fas fa-info-circle"></i> Payment Instructions:</h6>
                <ol class="mb-0">
                    <li>Enter your MTN Mobile Money number</li>
                    <li>Click "Process Payment"</li>
                    <li>You will receive a payment prompt on your phone</li>
                    <li>Enter your MTN MoMo PIN to complete payment</li>
                    <li>You'll receive SMS confirmation</li>
                </ol>
            </div>

            <button type="submit" name="process_payment" class="btn btn-warning btn-pay">
                <i class="fas fa-mobile-alt"></i> Process MTN Payment
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="donation.php" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Donation
            </a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Phone number formatting
        $('#phone').on('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>

</html>