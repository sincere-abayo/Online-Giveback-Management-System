<?php
// Include necessary files
require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';
require_once 'stripe_handler.php';
require_once 'paypack_handler.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in and is a client
requireLogin();
requireUserType('client');

// Get client ID from session
$clientId = $_SESSION['client_id'];

// Check if billing ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirectWithMessage('index.php', 'Invalid invoice ID', 'error');
    exit;
}

$billingId = (int) $_GET['id'];

// Get database connection
$conn = getDBConnection();

// Get invoice details
$stmt = $conn->prepare("
    SELECT b.*, c.case_number, c.title as case_title, u.full_name as client_name, u.phone
    FROM billings b
    LEFT JOIN cases c ON b.case_id = c.case_id
    JOIN client_profiles cp ON b.client_id = cp.client_id
    JOIN users u ON cp.user_id = u.user_id
    WHERE b.billing_id = ? AND b.client_id = ?
");
$stmt->bind_param("ii", $billingId, $clientId);
$stmt->execute();
$invoice = $stmt->get_result()->fetch_assoc();

if (!$invoice) {
    redirectWithMessage('index.php', 'Invoice not found or access denied', 'error');
    exit;
}

// Check if invoice is already paid
if ($invoice['status'] === 'paid') {
    redirectWithMessage('view.php?id=' . $billingId, 'This invoice has already been paid', 'info');
    exit;
}

// Initialize handlers with better error handling
$stripeHandler = null;
$paypackHandler = null;

try {
    $stripeHandler = new StripeHandler();
    error_log("Stripe handler initialized successfully");
} catch (Exception $e) {
    error_log("Failed to initialize Stripe handler: " . $e->getMessage());
}

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
    $paymentMethod = $_POST['payment_method'] ?? '';

    // Debug logging
    error_log("=== PAYMENT FORM DEBUG ===");
    error_log("Payment method: " . $paymentMethod);
    error_log("POST data: " . json_encode($_POST));
    error_log("Session data: " . json_encode($_SESSION));
    error_log("Invoice data: " . json_encode($invoice));

    if ($paymentMethod === 'stripe') {
        if (!$stripeHandler) {
            $errors[] = 'Stripe payment is currently unavailable';
        } else {
            // Create Stripe checkout session
            $paymentResult = $stripeHandler->createCheckoutSession($billingId, $invoice['amount']);
            if ($paymentResult['success']) {
                // Redirect to Stripe checkout
                header('Location: ' . $paymentResult['checkout_url']);
                exit;
            } else {
                $errors[] = $paymentResult['message'];
            }
        }
    } elseif ($paymentMethod === 'paypack') {
        if (!$paypackHandler) {
            $errors[] = 'PayPack payment is currently unavailable';
        } else {
            // Handle PayPack payment
            $phoneNumber = trim($_POST['phone_number'] ?? '');
            error_log("Phone number received: '" . $phoneNumber . "'");
            error_log("Phone number length: " . strlen($phoneNumber));
            error_log("Phone number type: " . gettype($phoneNumber));

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
                    error_log("Billing ID: " . $billingId);
                    error_log("Amount: " . $invoice['amount']);

                    try {
                        $paymentResult = $paypackHandler->initiatePayment($billingId, $invoice['amount'], $phoneNumber);
                        error_log("PayPack result: " . json_encode($paymentResult));

                        if ($paymentResult['success']) {
                            $_SESSION['payment_transaction_id'] = $paymentResult['transaction_id'];
                            error_log("Payment successful, redirecting to status page");
                            header('Location: payment_status.php?transaction_id=' . $paymentResult['transaction_id']);
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
    } else {
        error_log("Unknown payment method: " . $paymentMethod);
        $errors[] = 'Invalid payment method selected';
    }
}

// Set page title
$pageTitle = "Pay Invoice";

// Include header
include_once '../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Debug Information (only show in development) -->
        <?php if (defined('DEBUG_MODE') && DEBUG_MODE): ?>
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6">
                <h4 class="font-bold mb-2">Debug Information:</h4>
                <p><strong>Billing ID:</strong> <?php echo $billingId; ?></p>
                <p><strong>Client ID:</strong> <?php echo $clientId; ?></p>
                <p><strong>Amount:</strong> <?php echo $invoice['amount']; ?></p>
                <p><strong>Request Method:</strong> <?php echo $_SERVER['REQUEST_METHOD']; ?></p>
                <p><strong>PayPack Handler:</strong> <?php echo $paypackHandler ? 'Available' : 'Not Available'; ?></p>
                <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                    <p><strong>POST Data:</strong></p>
                    <pre><?php echo htmlspecialchars(json_encode($_POST, JSON_PRETTY_PRINT)); ?></pre>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Invoice Summary -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Pay Invoice</h1>
            <p class="text-sm text-gray-500 mb-4">You are paying an invoice for <span
                    class="font-medium text-gray-700"><?php echo htmlspecialchars($invoice['description']); ?></span>.
            </p>


            <?php
            // Calculate system fee
            $feePercent = 4.00; // System fee percentage
            $feeAmount = round(($invoice['amount'] * $feePercent) / 100, 2);
            $totalAmount = $invoice['amount']; // The client pays the invoice amount
            $netAmount = $totalAmount - $feeAmount;
            ?>

            <div class="border-t border-b border-gray-200 py-4 my-4">
                <div class="flex justify-between items-center text-lg">
                    <span class="text-gray-600">Total Amount Due:</span>
                    <span class="font-bold text-green-600"><?php echo formatCurrency($totalAmount); ?></span>
                </div>
            </div>


            <div class="mb-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Payment Breakdown</h3>
                <div class="text-xs space-y-1">
                    <div class="flex justify-between">
                        <span>Invoice Amount:</span>
                        <span><?php echo formatCurrency($invoice['amount']); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>System Fee (<?php echo number_format($feePercent, 2); ?>%):</span>
                        <span>- <?php echo formatCurrency($feeAmount); ?></span>
                    </div>
                    <div class="flex justify-between font-semibold border-t pt-1 mt-1">
                        <span>Amount Advocate Receives:</span>
                        <span class="text-green-700"><?php echo formatCurrency($netAmount); ?></span>
                    </div>
                </div>
                <div class="text-xs text-gray-500 mt-2">
                    The system fee is deducted from the invoice amount to cover processing and platform costs.
                </div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                    <h4 class="font-bold mb-2">Payment Error:</h4>
                    <ul class="list-disc list-inside space-y-1">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if (isset($paymentResult['debug_info'])): ?>
                        <div class="mt-4 p-3 bg-gray-100 rounded text-sm">
                            <h5 class="font-bold">Debug Information:</h5>
                            <pre><?php echo htmlspecialchars(json_encode($paymentResult['debug_info'], JSON_PRETTY_PRINT)); ?></pre>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Payment Methods -->
            <div class="space-y-4">
                <!-- Payment Method Selection -->
                <form method="POST" action="" class="border-t pt-4" id="payment-method-form">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Payment Method</label>
                        <div class="flex items-center space-x-6">
                            <label class="inline-flex items-center">
                                <input type="radio" name="payment_method" value="stripe" checked>
                                <span class="ml-2">Pay with Card (Stripe)</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="payment_method" value="paypack">
                                <span class="ml-2">Pay with Mobile Money (PayPack)</span>
                            </label>
                        </div>
                    </div>
                    <div id="paypack-phone-section" style="display:none;">
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number for Mobile Money
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">+250</span>
                            </div>
                            <input type="tel" id="phone_number" name="phone_number" class="form-input w-full pl-12"
                                placeholder="0786745698"
                                value="<?php echo isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : ''; ?>"
                                pattern="[0-9]{10}" maxlength="10">
                        </div>
                        <div class="mt-2 text-xs">
                            <div class="text-gray-600 mb-2">
                                <p><strong>Valid formats:</strong></p>
                                <p>• <span class="font-mono bg-green-100 px-1">0781234567</span> - MTN Rwanda (078X or
                                    079X)</p>
                                <p>• <span class="font-mono bg-blue-100 px-1">0721234567</span> - Airtel Rwanda (072X or
                                    073X)</p>
                            </div>
                            <div class="bg-yellow-50 border border-yellow-200 rounded p-2">
                                <p class="text-yellow-800"><strong>Note:</strong> Enter your full 10-digit number
                                    starting with 0</p>
                                <p class="text-yellow-800">Examples: <span
                                        class="font-mono bg-green-100 px-1">0781234567</span> (MTN) or <span
                                        class="font-mono bg-blue-100 px-1">0721234567</span> (Airtel)</p>
                            </div>
                        </div>
                    </div>
                    <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center mt-4">
                        <i class="fas fa-credit-card mr-2"></i>
                        Pay Now
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add this script before closing </body> tag -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const methodForm = document.getElementById('payment-method-form');
        const paypackSection = document.getElementById('paypack-phone-section');
        methodForm.addEventListener('change', function (e) {
            if (e.target.name === 'payment_method') {
                if (e.target.value === 'paypack') {
                    paypackSection.style.display = '';
                } else {
                    paypackSection.style.display = 'none';
                }
            }
        });
        // Set initial state
        if (methodForm.querySelector('input[name="payment_method"]:checked').value === 'paypack') {
            paypackSection.style.display = '';
        }
    });
</script>

<?php include_once '../includes/footer.php'; ?>