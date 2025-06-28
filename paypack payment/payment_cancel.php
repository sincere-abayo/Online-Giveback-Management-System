<?php
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireLogin();
requireUserType('client');

$billingId = $_GET['billing_id'] ?? '';

$pageTitle = "Payment Cancelled";
include_once '../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto text-center">
        <div class="bg-yellow-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-times text-2xl text-yellow-600"></i>
        </div>
        <h1 class="text-2xl font-bold text-yellow-600 mb-4">Payment Cancelled</h1>
        <p class="text-gray-600 mb-6">Your payment was cancelled. You can try again anytime.</p>
        <a href="pay.php?id=<?php echo $billingId; ?>" 
           class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">
            Try Again
        </a>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>