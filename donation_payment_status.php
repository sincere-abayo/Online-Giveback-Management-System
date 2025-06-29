<?php
include 'config.php';
require_once 'classes/PayPackHandler.php';
require_once 'classes/CurrencyConverter.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if transaction ID is provided
if (!isset($_GET['transaction_id']) || empty($_GET['transaction_id'])) {
    header("Location: donation.php?error=Invalid transaction ID");
    exit;
}

$transactionId = (int) $_GET['transaction_id'];

// Get transaction details
$sql = "SELECT pt.*, d.donation_ref, d.fullname, d.email, d.phone, d.amount, d.message, d.original_currency, d.original_amount
        FROM payment_transactions pt
        JOIN donations d ON pt.donation_id = d.id
        WHERE pt.transaction_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $transactionId);
$stmt->execute();
$result = $stmt->get_result();
$transaction = $result->fetch_assoc();

if (!$transaction) {
    header("Location: donation.php?error=Transaction not found");
    exit;
}

// Initialize PayPack handler for status checking
$paypackHandler = new PayPackHandler();
$statusResult = null;
$notificationsSent = false;

// Check for status update if it's a PayPack transaction
if ($transaction['payment_method'] === 'paypack' && $transaction['status'] === 'processing') {
    $statusResult = $paypackHandler->checkPaymentStatus($transactionId);

    // Refresh transaction data if status was updated
    if ($statusResult['success']) {
        $stmt->execute();
        $transaction = $stmt->get_result()->fetch_assoc();

        // Check if notifications were sent
        if (isset($statusResult['notifications_sent'])) {
            $notificationsSent = $statusResult['notifications_sent'];
            error_log("Notifications sent result: " . json_encode($notificationsSent));
        }
    }
}

// For completed transactions, try to send notifications if not already sent
if ($transaction['status'] === 'completed' && !$notificationsSent) {
    $statusResult = $paypackHandler->checkPaymentStatus($transactionId);
    if (isset($statusResult['notifications_sent'])) {
        $notificationsSent = $statusResult['notifications_sent'];
    }
}

// AJAX handler for polling status
if (isset($_GET['ajax']) && $_GET['ajax'] == '1' && isset($_GET['transaction_id'])) {
    $handler = new PayPackHandler();
    $result = $handler->checkPaymentStatus($transactionId);

    // Fetch updated transaction from DB
    $stmt = $conn->prepare("SELECT status FROM payment_transactions WHERE transaction_id = ?");
    $stmt->bind_param("i", $transactionId);
    $stmt->execute();
    $status = $stmt->get_result()->fetch_assoc()['status'] ?? 'unknown';

    header('Content-Type: application/json');
    echo json_encode([
        'status' => $status,
        'result' => $result
    ]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Status - Dufatanye Charity Foundation</title>
    <link rel="shortcut icon" href="gms1.png" type="image/png">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .status-container {
        padding: 40px 0;
    }

    .status-card {
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

    .status-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .status-icon {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 50px;
    }

    .status-icon.success {
        background: linear-gradient(45deg, #28a745, #20c997);
        color: white;
    }

    .status-icon.processing {
        background: linear-gradient(45deg, #ffc107, #fd7e14);
        color: white;
        animation: pulse 2s infinite;
    }

    .status-icon.failed {
        background: linear-gradient(45deg, #dc3545, #c82333);
        color: white;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }

    .amount-display {
        font-size: 2.5rem;
        font-weight: bold;
        color: #28a745;
        text-align: center;
        margin: 20px 0;
    }

    .transaction-details {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 30px;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #dee2e6;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 600;
        color: #495057;
    }

    .detail-value {
        color: #6c757d;
    }

    .btn-action {
        border-radius: 25px;
        padding: 12px 30px;
        font-weight: 600;
        transition: all 0.3s ease;
        margin: 5px;
    }

    .notification-status {
        background: #e3f2fd;
        border: 1px solid #2196f3;
        color: #1976d2;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
    </style>
</head>

<body>
    <div class="container status-container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="status-card">
                    <div class="status-header">
                        <?php if ($transaction['status'] === 'completed'): ?>
                        <div class="status-icon success">
                            <i class="fas fa-check"></i>
                        </div>
                        <h1 class="text-success mb-2">Payment Successful!</h1>
                        <p class="text-muted">Your donation has been processed successfully.</p>
                        <?php elseif ($transaction['status'] === 'failed'): ?>
                        <div class="status-icon failed">
                            <i class="fas fa-times"></i>
                        </div>
                        <h1 class="text-danger mb-2">Payment Failed</h1>
                        <p class="text-muted">Unfortunately, your payment could not be processed.</p>
                        <?php elseif ($transaction['status'] === 'processing'): ?>
                        <div class="status-icon processing">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h1 class="text-warning mb-2">Payment Processing</h1>
                        <p class="text-muted">Your payment is being processed. Please wait...</p>
                        <?php else: ?>
                        <div class="status-icon processing">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <h1 class="text-info mb-2">Payment Pending</h1>
                        <p class="text-muted">Your payment is pending confirmation.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Notification Status -->
                    <?php if ($notificationsSent): ?>
                    <div class="notification-status">
                        <div class="text-center">
                            <i class="fas fa-bell text-primary mr-2"></i>
                            <span class="text-primary">
                                Confirmation sent via
                                <?php if ($notificationsSent['email_sent']): ?>
                                <i class="fas fa-envelope mr-1"></i>Email
                                <?php endif; ?>
                                <?php if ($notificationsSent['email_sent'] && $notificationsSent['sms_sent']): ?>
                                and
                                <?php endif; ?>
                                <?php if ($notificationsSent['sms_sent']): ?>
                                <i class="fas fa-sms mr-1"></i>SMS
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Transaction Details -->
                    <div class="transaction-details">
                        <h3 class="text-center mb-4">Transaction Details</h3>

                        <div class="amount-display">
                            <?php 
                            $currencyConverter = new CurrencyConverter();
                            $original_currency = $transaction['original_currency'] ?? 'RWF';
                            $original_amount = $transaction['original_amount'] ?? $transaction['amount'];
                            echo $currencyConverter->formatAmount($original_amount, $original_currency);
                            
                            if ($original_currency !== 'RWF'): ?>
                                <br><small style="font-size: 0.6em; color: #6c757d;">
                                    (≈ <?php echo number_format($transaction['amount'], 0); ?> RWF)
                                </small>
                            <?php endif; ?>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">Transaction ID:</span>
                            <span
                                class="detail-value">#<?php echo str_pad($transaction['transaction_id'], 8, '0', STR_PAD_LEFT); ?></span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">Donation Reference:</span>
                            <span
                                class="detail-value"><?php echo htmlspecialchars($transaction['donation_ref']); ?></span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">Donor Name:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($transaction['fullname']); ?></span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">Payment Method:</span>
                            <span class="detail-value">
                                <i class="fas fa-mobile-alt mr-1"></i>Mobile Money (PayPack)
                            </span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">Status:</span>
                            <span class="detail-value">
                                <?php
                                $statusClass = '';
                                switch ($transaction['status']) {
                                    case 'completed':
                                        $statusClass = 'badge-success';
                                        break;
                                    case 'failed':
                                        $statusClass = 'badge-danger';
                                        break;
                                    case 'processing':
                                        $statusClass = 'badge-warning';
                                        break;
                                    default:
                                        $statusClass = 'badge-secondary';
                                }
                                ?>
                                <span class="badge <?php echo $statusClass; ?>">
                                    <?php echo ucfirst($transaction['status']); ?>
                                </span>
                            </span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">Date Created:</span>
                            <span
                                class="detail-value"><?php echo date('M d, Y h:i A', strtotime($transaction['created_at'])); ?></span>
                        </div>

                        <?php if ($transaction['updated_at'] !== $transaction['created_at']): ?>
                        <div class="detail-row">
                            <span class="detail-label">Last Updated:</span>
                            <span
                                class="detail-value"><?php echo date('M d, Y h:i A', strtotime($transaction['updated_at'])); ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if ($transaction['gateway_reference']): ?>
                        <div class="detail-row">
                            <span class="detail-label">Gateway Reference:</span>
                            <span
                                class="detail-value font-monospace"><?php echo htmlspecialchars($transaction['gateway_reference']); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Actions -->
                    <div class="text-center">
                        <?php if ($transaction['status'] === 'processing' && $transaction['payment_method'] === 'paypack'): ?>
                        <button onclick="checkStatus()" class="btn btn-primary btn-action">
                            <i class="fas fa-sync-alt mr-2"></i>
                            <span id="check-status-text">Check Status</span>
                            <span id="check-status-spinner" class="loading-spinner" style="display: none;"></span>
                        </button>
                        <?php endif; ?>

                        <?php if ($transaction['status'] === 'failed'): ?>
                        <a href="donation.php" class="btn btn-success btn-action">
                            <i class="fas fa-redo mr-2"></i>
                            Try Again
                        </a>
                        <?php endif; ?>

                        <?php if ($transaction['status'] === 'completed'): ?>
                        <a href="donation_success.php?donation_id=<?php echo $transaction['donation_id']; ?>"
                            class="btn btn-success btn-action">
                            <i class="fas fa-check-circle mr-2"></i>
                            View Receipt
                        </a>
                        <?php endif; ?>

                        <a href="donation.php" class="btn btn-outline-secondary btn-action">
                            <i class="fas fa-home mr-2"></i>
                            Back to Donations
                        </a>
                    </div>
                </div>

                <!-- Payment Instructions (for processing payments) -->
                <?php if ($transaction['status'] === 'processing' && $transaction['payment_method'] === 'paypack'): ?>
                <div class="status-card">
                    <div class="alert alert-warning">
                        <h5><i class="fas fa-info-circle mr-2"></i>Payment Instructions</h5>
                        <p class="mb-2">If you haven't received the payment prompt on your phone:</p>
                        <ol class="mb-0">
                            <li>Check your phone for any pending notifications</li>
                            <li>Dial <strong>*182*7*1#</strong> (MTN) or <strong>*500*2*1#</strong> (Airtel) to check
                                pending transactions</li>
                            <li>Contact your mobile money provider if you need assistance</li>
                            <li>Click "Check Status" above to refresh the payment status</li>
                        </ol>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
    // AJAX polling for PayPack status
    var status = '<?php echo $transaction["status"]; ?>';
    var method = '<?php echo $transaction["payment_method"]; ?>';
    var transactionId = '<?php echo $transaction["transaction_id"]; ?>';
    var pollInterval = null;

    function checkStatus() {
        var checkBtn = document.getElementById('check-status-text');
        var spinner = document.getElementById('check-status-spinner');

        checkBtn.style.display = 'none';
        spinner.style.display = 'inline-block';

        fetch('donation_payment_status.php?ajax=1&transaction_id=' + transactionId)
            .then(r => r.json())
            .then(data => {
                if (data.status && data.status !== status) {
                    // Status changed, reload the page to show updated information
                    window.location.reload();
                } else {
                    // Show message that status is still the same
                    alert('Payment status is still being processed. Please wait a moment and try again.');
                }
            })
            .catch(error => {
                console.error('Error checking status:', error);
                alert('Error checking payment status. Please try again.');
            })
            .finally(() => {
                checkBtn.style.display = 'inline';
                spinner.style.display = 'none';
            });
    }

    // Auto-polling for processing payments
    if ((status === 'processing' || status === 'pending') && method === 'paypack') {
        pollInterval = setInterval(function() {
            fetch('donation_payment_status.php?ajax=1&transaction_id=' + transactionId)
                .then(r => r.json())
                .then(data => {
                    if (data.status && data.status !== status) {
                        // Status changed, reload the page
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error polling status:', error);
                });
        }, 5000); // Check every 5 seconds

        // Stop polling after 10 minutes
        setTimeout(function() {
            if (pollInterval) {
                clearInterval(pollInterval);
            }
        }, 600000);
    }
    </script>
</body>

</html>