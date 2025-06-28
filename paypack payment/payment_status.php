<?php
// Include necessary files
require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';
require_once 'paypack_handler.php';

// Check if user is logged in and is a client
requireLogin();
requireUserType('client');

// Get client ID from session
$clientId = $_SESSION['client_id'];

// Check if transaction ID is provided
if (!isset($_GET['transaction_id']) || empty($_GET['transaction_id'])) {
    redirectWithMessage('index.php', 'Invalid transaction ID', 'error');
    exit;
}

$transactionId = (int) $_GET['transaction_id'];

// Get database connection
$conn = getDBConnection();

// Get transaction details
$stmt = $conn->prepare("
    SELECT pt.*, b.billing_id, b.amount, b.description, u.full_name as client_name
    FROM payment_transactions pt
    JOIN billings b ON pt.billing_id = b.billing_id
    JOIN client_profiles cp ON pt.client_id = cp.client_id
    JOIN users u ON cp.user_id = u.user_id
    WHERE pt.transaction_id = ? AND pt.client_id = ?
");
$stmt->bind_param("ii", $transactionId, $clientId);
$stmt->execute();
$transaction = $stmt->get_result()->fetch_assoc();

if (!$transaction) {
    redirectWithMessage('index.php', 'Transaction not found or access denied', 'error');
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

        // If payment is completed, update billing status and record payment
        if ($transaction['status'] === 'completed') {
            // Use a transaction to ensure all database updates succeed or fail together
            $conn->begin_transaction();
            try {
                // Check if a payment record already exists for this transaction to prevent duplicates
                $checkStmt = $conn->prepare("SELECT payment_id FROM payments WHERE transaction_id = ?");
                $checkStmt->bind_param("i", $transactionId);
                $checkStmt->execute();
                $paymentExists = $checkStmt->get_result()->fetch_assoc();

                if (!$paymentExists) {
                    // Insert into the main payments table. This will trigger fee calculation and summary updates.
                    $insertPaymentStmt = $conn->prepare("
                        INSERT INTO payments (billing_id, transaction_id, amount, payment_date, payment_method, notes, created_by)
                        VALUES (?, ?, ?, CURDATE(), 'paypack', ?, ?)
                    ");
                    $notes = "PayPack mobile money payment. Gateway Reference: " . ($transaction['gateway_reference'] ?? 'N/A');
                    $insertPaymentStmt->bind_param("iidsi", $transaction['billing_id'], $transactionId, $transaction['amount'], $notes, $_SESSION['user_id']);
                    $insertPaymentStmt->execute();

                    // Update billing status
                    $updateBillingStmt = $conn->prepare("
                        UPDATE billings 
                        SET status = 'paid', payment_method = 'paypack', payment_date = CURDATE()
                        WHERE billing_id = ?
                    ");
                    $updateBillingStmt->bind_param("i", $transaction['billing_id']);
                    $updateBillingStmt->execute();
                } else {
                    // Payment already recorded, just update billing status if needed
                    $updateBillingStmt = $conn->prepare("
                        UPDATE billings 
                        SET status = 'paid', payment_method = 'paypack', payment_date = CURDATE()
                        WHERE billing_id = ? AND status != 'paid'
                    ");
                    $updateBillingStmt->bind_param("i", $transaction['billing_id']);
                    $updateBillingStmt->execute();
                }

                // Commit the transaction
                $conn->commit();

                // Check if notifications were sent
                if (isset($statusResult['notifications_sent'])) {
                    $notificationsSent = $statusResult['notifications_sent'];
                    error_log("Notifications sent result: " . json_encode($notificationsSent));
                }
            } catch (Exception $e) {
                $conn->rollback();
                error_log("Error processing completed PayPack payment: " . $e->getMessage());
                // Continue to show the page but log the error
            }
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
    
    // If status changed to completed, handle payment recording
    if (isset($result['status_changed']) && $result['status_changed'] && $result['new_status'] === 'completed') {
        $conn->begin_transaction();
        try {
            // Check if payment record exists
            $checkStmt = $conn->prepare("SELECT payment_id FROM payments WHERE transaction_id = ?");
            $checkStmt->bind_param("i", $transactionId);
            $checkStmt->execute();
            $paymentExists = $checkStmt->get_result()->fetch_assoc();

            if (!$paymentExists) {
                // Get transaction details for payment recording
                $transStmt = $conn->prepare("SELECT billing_id, amount, gateway_reference FROM payment_transactions WHERE transaction_id = ?");
                $transStmt->bind_param("i", $transactionId);
                $transStmt->execute();
                $transData = $transStmt->get_result()->fetch_assoc();

                if ($transData) {
                    // Record payment
                    $insertPaymentStmt = $conn->prepare("
                        INSERT INTO payments (billing_id, transaction_id, amount, payment_date, payment_method, notes, created_by)
                        VALUES (?, ?, ?, CURDATE(), 'paypack', ?, ?)
                    ");
                    $notes = "PayPack mobile money payment. Gateway Reference: " . ($transData['gateway_reference'] ?? 'N/A');
                    $insertPaymentStmt->bind_param("iidsi", $transData['billing_id'], $transactionId, $transData['amount'], $notes, $_SESSION['user_id']);
                    $insertPaymentStmt->execute();

                    // Update billing status
                    $updateBillingStmt = $conn->prepare("
                        UPDATE billings 
                        SET status = 'paid', payment_method = 'paypack', payment_date = CURDATE()
                        WHERE billing_id = ?
                    ");
                    $updateBillingStmt->bind_param("i", $transData['billing_id']);
                    $updateBillingStmt->execute();
                }
            }
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Error in AJAX payment recording: " . $e->getMessage());
        }
    }
    
    // Fetch updated transaction from DB
    $stmt = $conn->prepare("SELECT status FROM payment_transactions WHERE transaction_id = ?");
    $stmt->bind_param("i", $transactionId);
    $stmt->execute();
    $status = $stmt->get_result()->fetch_assoc()['status'] ?? 'unknown';
    $conn->close();
    header('Content-Type: application/json');
    echo json_encode([
        'status' => $status,
        'result' => $result
    ]);
    exit;
}

// Set page title
$pageTitle = "Payment Status";

// Include header
include_once '../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="view.php?id=<?php echo $transaction['billing_id']; ?>"
                class="inline-flex items-center text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Invoice
            </a>
        </div>

        <!-- Payment Status -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-center mb-6">
                <?php if ($transaction['status'] === 'completed'): ?>
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check text-2xl text-green-600"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-green-600 mb-2">Payment Successful!</h1>
                    <p class="text-gray-600">Your payment has been processed successfully.</p>

                    <!-- Notification Status -->
                    <?php if ($notificationsSent): ?>
                        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-bell text-blue-500 mr-2"></i>
                                <span class="text-blue-700 text-sm">
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

                <?php elseif ($transaction['status'] === 'failed'): ?>
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-times text-2xl text-red-600"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-red-600 mb-2">Payment Failed</h1>
                    <p class="text-gray-600">Unfortunately, your payment could not be processed.</p>
                <?php elseif ($transaction['status'] === 'processing'): ?>
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-2xl text-yellow-600"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-yellow-600 mb-2">Payment Processing</h1>
                    <p class="text-gray-600">Your payment is being processed. Please wait...</p>
                <?php else: ?>
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-hourglass-half text-2xl text-gray-600"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-600 mb-2">Payment Pending</h1>
                    <p class="text-gray-600">Your payment is pending confirmation.</p>
                <?php endif; ?>
            </div>

            <!-- Transaction Details -->
            <div class="border-t pt-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Transaction Details</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Transaction ID</p>
                        <p class="font-medium">
                            #<?php echo str_pad($transaction['transaction_id'], 8, '0', STR_PAD_LEFT); ?></p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Amount</p>
                        <p class="font-medium text-lg"><?php echo formatCurrency($transaction['amount']); ?></p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Payment Method</p>
                        <p class="font-medium capitalize">
                            <?php if ($transaction['payment_method'] === 'stripe'): ?>
                                <i class="fab fa-stripe mr-1"></i>Credit/Debit Card
                            <?php elseif ($transaction['payment_method'] === 'paypack'): ?>
                                <i class="fas fa-mobile-alt mr-1"></i>Mobile Money
                            <?php else: ?>
                                <?php echo ucfirst($transaction['payment_method']); ?>
                            <?php endif; ?>
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Status</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            <?php
                            switch ($transaction['status']) {
                                case 'completed':
                                    echo 'bg-green-100 text-green-800';
                                    break;
                                case 'failed':
                                    echo 'bg-red-100 text-red-800';
                                    break;
                                case 'processing':
                                    echo 'bg-yellow-100 text-yellow-800';
                                    break;
                                default:
                                    echo 'bg-gray-100 text-gray-800';
                            }
                            ?>">
                            <?php echo ucfirst($transaction['status']); ?>
                        </span>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Date Created</p>
                        <p class="font-medium">
                            <?php echo date('M d, Y h:i A', strtotime($transaction['created_at'])); ?>
                        </p>
                    </div>
                    <?php if ($transaction['updated_at'] !== $transaction['created_at']): ?>
                        <div>
                            <p class="text-sm text-gray-600">Last Updated</p>
                            <p class="font-medium">
                                <?php echo date('M d, Y h:i A', strtotime($transaction['updated_at'])); ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($transaction['gateway_reference']): ?>
                    <div class="mt-4">
                        <p class="text-sm text-gray-600">Gateway Reference</p>
                        <p class="font-medium font-mono text-sm">
                            <?php echo htmlspecialchars($transaction['gateway_reference']); ?>
                        </p>
                    </div>
                <?php endif; ?>

                <?php if ($transaction['failure_reason']): ?>
                    <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-md">
                        <p class="text-sm text-gray-600 mb-1">Failure Reason</p>
                        <p class="text-red-700"><?php echo htmlspecialchars($transaction['failure_reason']); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Actions -->
            <div class="border-t pt-6 mt-6">
                <div class="flex flex-col sm:flex-row gap-4">
                    <?php if ($transaction['status'] === 'processing' && $transaction['payment_method'] === 'paypack'): ?>
                        <a href="?transaction_id=<?php echo $transaction['transaction_id']; ?>"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg text-center">
                            <i class="fas fa-sync-alt mr-2"></i>
                            Check Status
                        </a>
                    <?php endif; ?>

                    <?php if ($transaction['status'] === 'failed'): ?>
                        <a href="pay.php?id=<?php echo $transaction['billing_id']; ?>"
                            class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg text-center">
                            <i class="fas fa-redo mr-2"></i>
                            Try Again
                        </a>
                    <?php endif; ?>

                    <?php if ($transaction['status'] === 'completed'): ?>
                        <a href="print-receipt.php?transaction_id=<?php echo $transaction['transaction_id']; ?>"
                            class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg text-center"
                            target="_blank">
                            <i class="fas fa-print mr-2"></i>
                            Print Receipt
                        </a>
                    <?php endif; ?>

                    <a href="view.php?id=<?php echo $transaction['billing_id']; ?>"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg text-center">
                        <i class="fas fa-file-invoice mr-2"></i>
                        View Invoice
                    </a>

                    <a href="index.php"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg text-center">
                        <i class="fas fa-list mr-2"></i>
                        All Invoices
                    </a>
                </div>
            </div>
        </div>

        <!-- Payment Instructions (for processing payments) -->
        <?php if ($transaction['status'] === 'processing' && $transaction['payment_method'] === 'paypack'): ?>
            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Payment Instructions</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>If you haven't received the payment prompt on your phone:</p>
                            <ol class="list-decimal list-inside mt-2 space-y-1">
                                <li>Check your phone for any pending notifications</li>
                                <li>Dial *182*7*1# (MTN) or *500*2*1# (Airtel) to check pending transactions</li>
                                <li>Contact your mobile money provider if you need assistance</li>
                                <li>Click "Check Status" above to refresh the payment status</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // AJAX polling for PayPack status
    (function () {
        var status = '<?php echo $transaction["status"]; ?>';
        var method = '<?php echo $transaction["payment_method"]; ?>';
        var transactionId = '<?php echo $transaction["transaction_id"]; ?>';
        var pollInterval = null;
        
        function pollStatus() {
            fetch('payment_status.php?ajax=1&transaction_id=' + transactionId)
                .then(r => r.json())
                .then(data => {
                    if (data.status && data.status !== status) {
                        // Status changed, reload the page to show updated information
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error polling status:', error);
                });
        }
        
        if ((status === 'processing' || status === 'pending') && method === 'paypack') {
            pollInterval = setInterval(pollStatus, 5000);
            
            // Stop polling after 10 minutes
            setTimeout(function() {
                if (pollInterval) {
                    clearInterval(pollInterval);
                }
            }, 600000);
        }
    })();
</script>

<?php include_once '../includes/footer.php'; ?>
