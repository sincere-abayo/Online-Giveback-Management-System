<?php
require_once 'config.php';
require_once 'vendor/autoload.php';
require_once 'classes/PaymentProcessor.php';

// Get the webhook secret from environment or database
$webhook_secret = $_ENV['STRIPE_WEBHOOK_SECRET'] ?? 'whsec_your_webhook_secret';
$webhook_sql = "SELECT setting_value FROM payment_settings WHERE payment_method = 'stripe' AND setting_key = 'webhook_secret' AND is_active = 1";
$webhook_result = $conn->query($webhook_sql);
if ($webhook_result->num_rows > 0) {
    $webhook_secret = $webhook_result->fetch_assoc()['setting_value'];
}

// Get the Stripe secret key
$stripe_secret_key = $_ENV['STRIPE_SECRET_KEY'] ?? 'sk_test_your_stripe_secret_key';
$stripe_key_sql = "SELECT setting_value FROM payment_settings WHERE payment_method = 'stripe' AND setting_key = 'secret_key' AND is_active = 1";
$stripe_result = $conn->query($stripe_key_sql);
if ($stripe_result->num_rows > 0) {
    $stripe_secret_key = $stripe_result->fetch_assoc()['setting_value'];
}

// Set Stripe API key
\Stripe\Stripe::setApiKey($stripe_secret_key);

// Get the webhook payload
$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

try {
    // Verify the webhook signature
    $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $webhook_secret);
} catch (\UnexpectedValueException $e) {
    // Invalid payload
    http_response_code(400);
    exit();
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    // Invalid signature
    http_response_code(400);
    exit();
}

// Handle the event
switch ($event->type) {
    case 'checkout.session.completed':
        $session = $event->data->object;

        // Get donation ID from metadata
        $donation_id = $session->metadata->donation_id ?? null;

        if ($donation_id) {
            // Update donation status
            $paymentProcessor = new PaymentProcessor();

            // Process the payment completion
            $result = $paymentProcessor->processWebhookPayment($donation_id, $session->payment_intent);

            // Log the successful payment
            error_log("Stripe Checkout payment completed for donation ID: " . $donation_id . ", Payment Intent: " . $session->payment_intent);
        }
        break;

    case 'payment_intent.payment_failed':
        $paymentIntent = $event->data->object;

        // Get donation ID from metadata
        $donation_id = $paymentIntent->metadata->donation_id ?? null;

        if ($donation_id) {
            // Update donation status to failed
            $sql = "UPDATE donations SET status = 'failed', updated_at = NOW() WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $donation_id);
            $stmt->execute();

            error_log("Stripe payment failed for donation ID: " . $donation_id . ", Payment Intent: " . $paymentIntent->id);
        }
        break;

    default:
        // Unexpected event type
        error_log('Received unknown event type ' . $event->type);
}

http_response_code(200);
?>