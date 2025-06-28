<?php
include 'config.php';

// Check if user is logged in
$is_logged_in = isset($_SESSION['volunteer_id']) && $_SESSION['volunteer_type'] === 'volunteer';
$user_info = null;

if ($is_logged_in) {
    // Get volunteer information
    $volunteer_id = $_SESSION['volunteer_id'];
    $sql = "SELECT * FROM volunteer_list WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $volunteer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_info = $result->fetch_assoc();
}

// Handle donation form submission
$donation_message = '';
$donation_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['donate'])) {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $amount = floatval($_POST['amount']);
    $payment_method = $_POST['payment_method'];
    $message = trim($_POST['message'] ?? '');
    $volunteer_id = $is_logged_in ? $_SESSION['volunteer_id'] : null;

    // Validation
    $errors = [];

    if (empty($fullname))
        $errors[] = "Full name is required";
    if (empty($email))
        $errors[] = "Email is required";
    if (empty($phone))
        $errors[] = "Phone number is required";
    if ($amount <= 0)
        $errors[] = "Amount must be greater than 0";
    if (empty($payment_method))
        $errors[] = "Please select a payment method";

    if (empty($errors)) {
        // Generate unique donation reference
        $donation_ref = 'DON' . date('Ymd') . strtoupper(substr(md5(uniqid()), 0, 6));

        // Save donation to database
        $sql = "INSERT INTO donations (donation_ref, volunteer_id, fullname, email, phone, amount, payment_method, message, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisssdss", $donation_ref, $volunteer_id, $fullname, $email, $phone, $amount, $payment_method, $message);

        if ($stmt->execute()) {
            $donation_id = $conn->insert_id;

            // Redirect to payment processing based on method
            switch ($payment_method) {
                case 'stripe':
                    header("Location: payment_stripe.php?donation_id=" . $donation_id);
                    exit;
                case 'mtn':
                    header("Location: payment_mtn.php?donation_id=" . $donation_id);
                    exit;
                case 'airtel':
                    header("Location: payment_airtel.php?donation_id=" . $donation_id);
                    exit;
            }
        } else {
            $donation_message = "Error processing donation: " . $stmt->error;
            $donation_type = "error";
        }
    } else {
        $donation_message = implode("<br>", $errors);
        $donation_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make a Donation - Dufatanye Charity Foundation</title>
    <link rel="shortcut icon" href="gms1.png" type="image/png">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .donation-container {
            padding: 40px 0;
        }

        .donation-card {
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

        .donation-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .donation-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #28a745, #20c997);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .donation-icon i {
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

        .payment-method {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            margin: 10px 0;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-method:hover {
            border-color: #667eea;
            background-color: #f8f9fa;
        }

        .payment-method.selected {
            border-color: #667eea;
            background-color: #e3f2fd;
        }

        .payment-method input[type="radio"] {
            margin-right: 10px;
        }

        .payment-icon {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

        .btn-donate {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            border-radius: 25px;
            padding: 15px 40px;
            font-weight: 600;
            font-size: 18px;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-donate:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }

        .login-prompt {
            background: linear-gradient(45deg, #ffc107, #fd7e14);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
            color: white;
        }

        .benefits-list {
            list-style: none;
            padding: 0;
        }

        .benefits-list li {
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .benefits-list li:before {
            content: "✓";
            color: #28a745;
            font-weight: bold;
            margin-right: 10px;
        }

        .amount-presets {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .amount-preset {
            padding: 10px 20px;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }

        .amount-preset:hover {
            border-color: #667eea;
            background-color: #f8f9fa;
        }

        .amount-preset.selected {
            border-color: #667eea;
            background-color: #667eea;
            color: white;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .progress-bar {
            height: 8px;
            border-radius: 4px;
            background: linear-gradient(45deg, #28a745, #20c997);
        }
    </style>
</head>

<body>
    <div class="donation-container">
        <div class="container">
            <!-- Back to Home Button -->
            <div class="row mb-4">
                <div class="col-12">
                    <a href="index.php" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Home
                    </a>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Login Prompt for Guest Users -->
                    <?php if (!$is_logged_in): ?>
                        <div class="login-prompt">
                            <h4><i class="fas fa-info-circle"></i> Get More Benefits!</h4>
                            <p class="mb-3">Login or register to track your donation history and participate in community
                                events.</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="volunteer_login.php" class="btn btn-light btn-sm">
                                        <i class="fas fa-sign-in-alt"></i> Login
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="registration.php" class="btn btn-light btn-sm">
                                        <i class="fas fa-user-plus"></i> Register
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Donation Form -->
                    <div class="donation-card">
                        <div class="donation-header">
                            <div class="donation-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <h2>Make a Donation</h2>
                            <p class="text-muted">Your contribution makes a real difference in our community</p>
                        </div>

                        <?php if (!empty($donation_message)): ?>
                            <div class="alert alert-<?php echo $donation_type === 'error' ? 'danger' : 'success'; ?>"
                                role="alert">
                                <i
                                    class="fas fa-<?php echo $donation_type === 'error' ? 'exclamation-triangle' : 'check-circle'; ?>"></i>
                                <?php echo $donation_message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="" id="donationForm">
                            <!-- Personal Information -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fullname">Full Name *</label>
                                        <input type="text" class="form-control" id="fullname" name="fullname"
                                            value="<?php echo $is_logged_in ? htmlspecialchars($user_info['firstname'] . ' ' . $user_info['lastname']) : ''; ?>"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email Address *</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="<?php echo $is_logged_in ? htmlspecialchars($user_info['email']) : ''; ?>"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone Number *</label>
                                        <input type="tel" class="form-control" id="phone" name="phone"
                                            value="<?php echo $is_logged_in ? htmlspecialchars($user_info['contact']) : ''; ?>"
                                            pattern="[0-9]{10}" maxlength="10" required>
                                        <small class="form-text text-muted">10 digits only (e.g., 0781234567)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="amount">Amount (RWF) *</label>
                                        <input type="number" class="form-control" id="amount" name="amount" min="100"
                                            step="100" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Amount Presets -->
                            <div class="form-group">
                                <label>Quick Amount Selection:</label>
                                <div class="amount-presets">
                                    <div class="amount-preset" data-amount="1000">1,000 RWF</div>
                                    <div class="amount-preset" data-amount="5000">5,000 RWF</div>
                                    <div class="amount-preset" data-amount="10000">10,000 RWF</div>
                                    <div class="amount-preset" data-amount="25000">25,000 RWF</div>
                                    <div class="amount-preset" data-amount="50000">50,000 RWF</div>
                                    <div class="amount-preset" data-amount="100000">100,000 RWF</div>
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div class="form-group">
                                <label>Payment Method *</label>
                                <div class="payment-method" onclick="selectPayment('stripe')">
                                    <input type="radio" name="payment_method" value="stripe" id="stripe" required>
                                    <img src="https://cdn.worldvectorlogo.com/logos/stripe-2.svg" alt="Stripe"
                                        class="payment-icon">
                                    <strong>Credit/Debit Card (Stripe)</strong>
                                    <small class="text-muted d-block">Secure payment with Stripe</small>
                                </div>

                                <div class="payment-method" onclick="selectPayment('mtn')">
                                    <input type="radio" name="payment_method" value="mtn" id="mtn">
                                    <img src="mtn.png" alt="MTN" class="payment-icon">
                                    <strong>MTN Mobile Money</strong>
                                    <small class="text-muted d-block">Pay with MTN MoMo</small>
                                </div>

                                <div class="payment-method" onclick="selectPayment('airtel')">
                                    <input type="radio" name="payment_method" value="airtel" id="airtel">
                                    <img src="airtel.png" alt="Airtel" class="payment-icon">
                                    <strong>Airtel Money</strong>
                                    <small class="text-muted d-block">Pay with Airtel Money</small>
                                </div>
                            </div>

                            <!-- Message -->
                            <div class="form-group">
                                <label for="message">Message (Optional)</label>
                                <textarea class="form-control" id="message" name="message" rows="3"
                                    placeholder="Share why you're making this donation..."></textarea>
                            </div>

                            <!-- Benefits for Logged-in Users -->
                            <?php if ($is_logged_in): ?>
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-star"></i> Benefits of Logged-in Donation:</h6>
                                    <ul class="benefits-list mb-0">
                                        <li>Track your donation history</li>
                                        <li>Receive email confirmations</li>
                                        <li>Participate in community events</li>
                                        <li>Get updates on impact</li>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <button type="submit" name="donate" class="btn btn-success btn-donate">
                                <i class="fas fa-heart"></i> Make Donation
                            </button>
                        </form>
                    </div>

                    <!-- Impact Information -->
                    <div class="donation-card">
                        <h4><i class="fas fa-chart-line"></i> Your Impact</h4>
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <h3 class="text-primary">1,000 RWF</h3>
                                <p>Provides school supplies for 1 child</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <h3 class="text-success">5,000 RWF</h3>
                                <p>Feeds a family for 1 week</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <h3 class="text-info">10,000 RWF</h3>
                                <p>Provides medical care for 2 people</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Amount preset selection
        $('.amount-preset').click(function () {
            $('.amount-preset').removeClass('selected');
            $(this).addClass('selected');
            $('#amount').val($(this).data('amount'));
        });

        // Payment method selection
        function selectPayment(method) {
            $('.payment-method').removeClass('selected');
            $('#' + method).closest('.payment-method').addClass('selected');
            $('#' + method).prop('checked', true);
        }

        // Form validation
        $('#donationForm').submit(function (e) {
            var amount = parseFloat($('#amount').val());
            if (amount < 100) {
                alert('Minimum donation amount is 100 RWF');
                e.preventDefault();
                return false;
            }
        });

        // Phone number formatting
        $('#phone').on('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>

</html>