<?php
require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once 'messaging_service.php'; // Add this line

class PayPackHandler
{
    private $clientId;
    private $clientSecret;
    private $apiUrl = 'https://payments.paypack.rw/api';
    private $conn;
    private $messagingService; // Add this property

    public function __construct()
    {
        error_log("=== PAYPACK HANDLER CONSTRUCTOR ===");
        $this->conn = getDBConnection();
        $this->messagingService = new MessagingService(); // Initialize messaging service
        $this->loadSettings();
        error_log("PayPack handler initialized");
    }

    // ... (keep all existing methods until initiatePayment)

    public function initiatePayment($billingId, $amount, $phoneNumber)
    {
        try {
            error_log("=== PAYPACK PAYMENT INITIATION ===");
            error_log("Billing ID: " . $billingId);
            error_log("Amount: " . $amount);
            error_log("Original phone input: '" . $phoneNumber . "'");

            // Format phone number using the working method
            $formattedPhone = $this->formatPhoneForPayPack($phoneNumber);
            error_log("Formatted phone for PayPack: " . $formattedPhone);

            // Get authentication token
            $token = $this->getPaypackToken();
            if (!$token) {
                error_log("Failed to get authentication token");
                return [
                    'success' => false,
                    'message' => 'Failed to authenticate with PayPack - check credentials and API connectivity'
                ];
            }

            error_log("Token obtained, proceeding with payment");

            // Generate unique reference (simpler approach)
            $reference = 'payment_' . $billingId . '_' . time();
            error_log("Payment reference: " . $reference);

            // Create payment transaction record BEFORE making the API call
            try {
                $stmt = $this->conn->prepare("
                    INSERT INTO payment_transactions (billing_id, client_id, payment_method, amount, currency, status, gateway_reference)
                    SELECT ?, client_id, 'paypack', ?, 'RWF', 'pending', ?
                    FROM billings WHERE billing_id = ?
                ");
                $stmt->bind_param("idsi", $billingId, $amount, $reference, $billingId);
                $stmt->execute();
                $transactionId = $this->conn->insert_id;
                error_log("Transaction record created with ID: " . $transactionId);
            } catch (Exception $dbError) {
                error_log("Database error creating transaction: " . $dbError->getMessage());
                return [
                    'success' => false,
                    'message' => 'Database error: ' . $dbError->getMessage()
                ];
            }

            // Make payment request using the exact same structure as working test
            $curl = curl_init();

            $headers = [
                "Authorization: Bearer $token",
                'Content-Type: application/json'
            ];

            $paymentData = [
                "amount" => floatval($amount),
                "number" => $formattedPhone
            ];

            $jsonPayload = json_encode($paymentData);

            error_log("=== PAYPACK API REQUEST DEBUG ===");
            error_log("Payment request URL: " . $this->apiUrl . '/transactions/cashin');
            error_log("Payment request headers: " . json_encode($headers));
            error_log("Payment request data (JSON): " . $jsonPayload);

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->apiUrl . '/transactions/cashin',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $jsonPayload,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_TIMEOUT => 30,
            ));

            $response = curl_exec($curl);
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($curl);

            error_log("=== PAYPACK API RESPONSE DEBUG ===");
            error_log("HTTP Code: " . $http_code);
            error_log("Raw Response: " . $response);
            error_log("CURL Error: " . $curl_error);

            curl_close($curl);

            // Handle response
            if ($curl_error) {
                error_log("CURL error: " . $curl_error);

                // Update transaction as failed
                $stmt = $this->conn->prepare("
                    UPDATE payment_transactions 
                    SET status = 'failed', failure_reason = ?
                    WHERE transaction_id = ?
                ");
                $stmt->bind_param("si", $curl_error, $transactionId);
                $stmt->execute();

                return [
                    'success' => false,
                    'message' => 'Network error: ' . $curl_error
                ];
            }

            $responseData = json_decode($response, true);
            error_log("Decoded response data: " . json_encode($responseData));

            // Update transaction record based on response
            if ($http_code == 200 || $http_code == 201) {
                $gatewayId = $responseData['ref'] ?? $reference;

                $stmt = $this->conn->prepare("
                    UPDATE payment_transactions 
                    SET status = 'processing', gateway_transaction_id = ?, gateway_response = ?
                    WHERE transaction_id = ?
                ");
                $stmt->bind_param("ssi", $gatewayId, $response, $transactionId);
                $stmt->execute();

                error_log("Payment successful - Transaction ID: " . $transactionId . ", Gateway Ref: " . $gatewayId);

                return [
                    'success' => true,
                    'transaction_id' => $transactionId,
                    'gateway_reference' => $gatewayId,
                    'message' => 'Payment initiated successfully. Please check your phone for the payment prompt.'
                ];
            } else {
                $failureReason = $responseData['message'] ?? 'Payment initiation failed (HTTP ' . $http_code . ')';

                $stmt = $this->conn->prepare("
                    UPDATE payment_transactions 
                    SET status = 'failed', failure_reason = ?, gateway_response = ?
                    WHERE transaction_id = ?
                ");
                $stmt->bind_param("ssi", $failureReason, $response, $transactionId);
                $stmt->execute();

                error_log("Payment failed - Reason: " . $failureReason);

                return [
                    'success' => false,
                    'message' => $failureReason,
                    'debug_info' => [
                        'http_code' => $http_code,
                        'response' => $responseData,
                        'curl_error' => $curl_error
                    ]
                ];
            }

        } catch (Exception $e) {
            error_log("PayPack Exception: " . $e->getMessage());
            error_log("Exception trace: " . $e->getTraceAsString());
            return [
                'success' => false,
                'message' => 'Payment processing error: ' . $e->getMessage()
            ];
        }
    }

    // Improved method to check payment status and send notifications
    public function checkPaymentStatus($transactionId)
    {
        try {
            error_log("=== CHECKING PAYMENT STATUS ===");
            error_log("Transaction ID: " . $transactionId);

            // Get the gateway reference from database
            $stmt = $this->conn->prepare("
                SELECT gateway_transaction_id, gateway_reference, status 
                FROM payment_transactions 
                WHERE transaction_id = ?
            ");
            $stmt->bind_param("i", $transactionId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            if (!$result) {
                error_log("Transaction not found in database");
                return ['success' => false, 'message' => 'Transaction not found'];
            }

            $reference = $result['gateway_transaction_id'] ?? $result['gateway_reference'];
            if (!$reference) {
                error_log("No gateway reference found");
                return ['success' => false, 'message' => 'No gateway reference found'];
            }

            error_log("Checking status for reference: " . $reference);

            // Get authentication token
            $token = $this->getPaypackToken();
            if (!$token) {
                error_log("Failed to get authentication token for status check");
                return ['success' => false, 'message' => 'Authentication failed'];
            }

            // Make status check request
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->apiUrl . "/transactions/find/" . $reference,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer $token"
                ),
            ));

            $response = curl_exec($curl);
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($curl);

            error_log("Status check HTTP Code: " . $http_code);
            error_log("Status check response: " . $response);

            curl_close($curl);

            if ($curl_error) {
                error_log("CURL error during status check: " . $curl_error);
                return ['success' => false, 'message' => 'Network error during status check'];
            }

            $paypackStatus = null;
            if ($http_code == 200) {
                $responseData = json_decode($response, true);

                if ($responseData && isset($responseData['status'])) {
                    $paypackStatus = $responseData['status'];
                    error_log("PayPack status: " . $paypackStatus);

                    // Map PayPack status to our system status
                    $newStatus = null;
                    switch (strtolower($paypackStatus)) {
                        case 'successful':
                        case 'completed':
                            $newStatus = 'completed';
                            break;
                        case 'failed':
                        case 'cancelled':
                            $newStatus = 'failed';
                            break;
                        case 'pending':
                        case 'processing':
                            $newStatus = 'processing';
                            break;
                    }

                    // Update transaction status if changed
                    if ($newStatus && $newStatus !== $result['status']) {
                        $stmt = $this->conn->prepare("
                            UPDATE payment_transactions 
                            SET status = ?, updated_at = CURRENT_TIMESTAMP, gateway_response = ?
                            WHERE transaction_id = ?
                        ");
                        $stmt->bind_param("ssi", $newStatus, $response, $transactionId);
                        $stmt->execute();

                        error_log("Transaction status updated to: " . $newStatus);

                        // If completed, send notifications
                        $notifications = null;
                        if ($newStatus === 'completed') {
                            $notifications = $this->messagingService->sendPaymentConfirmation($transactionId);
                            error_log("Notification result: " . json_encode($notifications));
                        }

                        return [
                            'success' => true,
                            'status_updated' => true,
                            'new_status' => $newStatus,
                            'notifications_sent' => $notifications,
                            'message' => 'Payment status updated'
                        ];
                    } else {
                        // If already completed, send notifications if not sent yet
                        $notifications = null;
                        if ($newStatus === 'completed') {
                            $notifications = $this->messagingService->sendPaymentConfirmation($transactionId);
                            error_log("Notification result: " . json_encode($notifications));
                        }
                        return [
                            'success' => true,
                            'status_updated' => false,
                            'current_status' => $result['status'],
                            'notifications_sent' => $notifications,
                            'message' => 'No status change'
                        ];
                    }
                }
            }

            // If no status or not found, try events endpoint
            if (!$paypackStatus) {
                $eventResult = $this->checkPaypackEvents($token, $reference);
                if ($eventResult['http_code'] == 200 && !empty($eventResult['decoded']['transactions'])) {
                    foreach ($eventResult['decoded']['transactions'] as $event) {
                        if ($event['event_kind'] === 'transaction:processed' && isset($event['data']['status'])) {
                            $eventStatus = strtolower($event['data']['status']);
                            $newStatus = null;
                            if ($eventStatus === 'successful') {
                                $newStatus = 'completed';
                            } elseif ($eventStatus === 'failed') {
                                $newStatus = 'failed';
                            }
                            if ($newStatus && $newStatus !== $result['status']) {
                                $stmt = $this->conn->prepare("
                                    UPDATE payment_transactions 
                                    SET status = ?, updated_at = CURRENT_TIMESTAMP, gateway_response = ?
                                    WHERE transaction_id = ?
                                ");
                                $stmt->bind_param("ssi", $newStatus, json_encode($eventResult['decoded']), $transactionId);
                                $stmt->execute();
                                // If completed, send notifications
                                $notifications = null;
                                if ($newStatus === 'completed') {
                                    $notifications = $this->messagingService->sendPaymentConfirmation($transactionId);
                                }
                                return [
                                    'success' => true,
                                    'status_updated' => true,
                                    'new_status' => $newStatus,
                                    'notifications_sent' => $notifications,
                                    'message' => 'Payment status updated from events endpoint'
                                ];
                            }
                        }
                    }
                }
            }

            error_log("Status check failed - HTTP Code: " . $http_code);
            return ['success' => false, 'message' => 'Failed to check payment status'];

        } catch (Exception $e) {
            error_log("Exception during status check: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error checking payment status: ' . $e->getMessage()];
        }
    }

    // Keep all other existing methods (formatPhoneForPayPack, etc.)
    private function formatPhoneForPayPack($phoneNumber)
    {
        error_log("=== PHONE FORMATTING FOR PAYPACK ===");
        error_log("Original input: '" . $phoneNumber . "'");

        // Use exact same logic as working test script
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber);
        error_log("After removing non-digits: '" . $phone . "'");

        if (strlen($phone) == 12 && substr($phone, 0, 3) == '250') {
            $formatted = '0' . substr($phone, 3);
        } elseif (strlen($phone) == 11 && substr($phone, 0, 2) == '25') {
            $formatted = '0' . substr($phone, 2);
        } elseif (strlen($phone) == 10 && substr($phone, 0, 1) == '0') {
            $formatted = $phone;
        } elseif (strlen($phone) == 9) {
            $formatted = '0' . $phone;
        } else {
            $formatted = $phone; // Fallback - don't try to be too smart
        }

        error_log("Final formatted number: " . $formatted);
        return $formatted;
    }

    // Keep existing private methods (loadSettings, getPaypackToken)
    private function loadSettings()
    {
        error_log("=== LOADING PAYPACK SETTINGS ===");

        $stmt = $this->conn->prepare("SELECT setting_key, setting_value FROM payment_settings WHERE gateway = 'paypack' AND is_active = 1");
        $stmt->execute();
        $result = $stmt->get_result();

        $settingsFound = 0;
        while ($row = $result->fetch_assoc()) {
            $settingsFound++;
            error_log("Found setting: " . $row['setting_key']);

            switch ($row['setting_key']) {
                case 'client_id':
                    $this->clientId = trim($row['setting_value']);
                    error_log("Client ID loaded: " . substr($this->clientId, 0, 8) . "...");
                    break;
                case 'client_secret':
                    $this->clientSecret = trim($row['setting_value']);
                    error_log("Client Secret loaded: " . substr($this->clientSecret, 0, 8) . "...");
                    break;
                case 'api_url':
                    $this->apiUrl = trim($row['setting_value']);
                    error_log("API URL loaded: " . $this->apiUrl);
                    break;
            }
        }

        // Fallback to hardcoded values if not found in database (for testing)
        if (empty($this->clientId) || empty($this->clientSecret)) {
            error_log("Using fallback credentials");
            $this->clientId = 'cb3de54e-4900-11f0-ab56-dead131a2dd9';
            $this->clientSecret = '522f8f6a27effc0a15a685e455d8a0bfda39a3ee5e6b4b0d3255bfef95601890afd80709';
        }

        error_log("Total settings found: " . $settingsFound);
        error_log("Final settings - Client ID: " . ($this->clientId ? 'SET' : 'NOT SET'));
        error_log("Final settings - Client Secret: " . ($this->clientSecret ? 'SET' : 'NOT SET'));
        error_log("Final settings - API URL: " . $this->apiUrl);
    }

    private function getPaypackToken()
    {
        error_log("=== GETTING PAYPACK TOKEN ===");

        if (empty($this->clientId) || empty($this->clientSecret)) {
            error_log("PayPack credentials not set - Client ID: " . ($this->clientId ? 'SET' : 'EMPTY') . ", Secret: " . ($this->clientSecret ? 'SET' : 'EMPTY'));
            return false;
        }

        $curl = curl_init();

        $postData = json_encode([
            "client_id" => $this->clientId,
            "client_secret" => $this->clientSecret
        ]);

        error_log("Auth request URL: " . $this->apiUrl . '/auth/agents/authorize');
        error_log("Auth request data: " . $postData);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->apiUrl . '/auth/agents/authorize',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($curl);

        error_log("Auth response HTTP Code: " . $http_code);
        error_log("Auth response body: " . $response);
        if ($curl_error) {
            error_log("Auth CURL Error: " . $curl_error);
        }

        curl_close($curl);

        if ($curl_error) {
            error_log("CURL error occurred: " . $curl_error);
            return false;
        }

        if ($http_code == 200) {
            $data = json_decode($response, true);
            if ($data && isset($data['access'])) {
                error_log("Token obtained successfully");
                return $data['access'];
            } else {
                error_log("No access token in response: " . $response);
                return false;
            }
        }

        error_log("Auth failed - HTTP Code: " . $http_code . ", Response: " . $response);
        return false;
    }

    private function checkPaypackEvents($token, $reference)
    {
        $curl = curl_init();
        $url = $this->apiUrl . "/events/transactions?ref=" . urlencode($reference);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array("Authorization: Bearer $token", "Accept: application/json"),
            CURLOPT_TIMEOUT => 30,
        ));
        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $decoded = json_decode($response, true);
        return [
            'http_code' => $http_code,
            'response' => $response,
            'decoded' => $decoded
        ];
    }
}
?>