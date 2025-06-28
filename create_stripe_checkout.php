<?php
require_once 'config.php';
require_once 'vendor/autoload.php';

if (!isset($_POST['donation_id'])) {
    header("Location: donation.php");
    exit;
}

$donation_id = intval($_POST['donation_id']);

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

// Get Stripe secret key
$stripe_secret_key = $_ENV['STRIPE_SECRET_KEY'] ?? 'sk_test_your_stripe_secret_key';
$stripe_key_sql = "SELECT setting_value FROM payment_settings WHERE payment_method = 'stripe' AND setting_key = 'secret_key' AND is_active = 1";
$stripe_result = $conn->query($stripe_key_sql);
if ($stripe_result->num_rows > 0) {
    $stripe_secret_key = $stripe_result->fetch_assoc()['setting_value'];
}

// Initialize Stripe
\Stripe\Stripe::setApiKey($stripe_secret_key);

// Create Stripe Checkout session
$session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [
        [
            'price_data' => [
                'currency' => 'rwf',
                'product_data' => [
                    'name' => 'Donation to Dufatanye Charity Foundation',
                    'description' => 'Thank you for your generous contribution',
                ],
                'unit_amount' => $donation['amount'] * 100, // Convert to cents
            ],
            'quantity' => 1,
        ]
    ],
    'mode' => 'payment',
    'success_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/utb/GMS/donation_success.php?donation_id=' . $donation_id . '&session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/utb/GMS/payment_stripe.php?donation_id=' . $donation_id,
    'metadata' => [
        'donation_id' => $donation_id,
        'donor_name' => $donation['fullname'],
        'donor_email' => $donation['email']
    ],
    'customer_email' => $donation['email'],
    'billing_address_collection' => 'auto',
    'locale' => 'en'
]);

// Redirect to Stripe Checkout
header("Location: " . $session->url);
exit;
?>