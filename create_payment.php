<?php

/*
require_once 'config.php';

use Xendit\PaymentRequests\PaymentRequestsApi;

header('Content-Type: application/json');

// Initialize API
$paymentRequestsApi = new PaymentRequestsApi();

// Payment data
$request_body = [
    'reference_id' => 'order-' . time(), // unique ID
    'currency' => 'PHP',
    'request_amount' => 5000,           // amount
    'payer_email' => 'customer@example.com',
    'description' => 'Test Payment',
    'channel_code' => 'PH_BANK_TRANSFER', // example channel
    'channel_properties' => [
        'success_return_url' => 'http://localhost/PSI/success.html',
        'failure_return_url' => 'http://localhost/PSI/failure.html'
    ]
];

try {
    $payment = $paymentRequestsApi->createPaymentRequest($request_body);
    echo json_encode([
        'success' => true,
        'payment' => $payment
    ]);
} catch (\Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

*/



$secretKey = "YOUR_XENDIT_SECRET_KEY"; // from dashboard

$data = json_decode(file_get_contents("php://input"), true);

$external_id = "ORDER-" . time();
$amount = $data['amount'];

$payload = [
  "external_id" => $external_id,
  "amount" => $amount,
  "payer_email" => "customer@email.com",
  "description" => "Website Payment"
];

$ch = curl_init();

curl_setopt_array($ch, [
  CURLOPT_URL => "https://api.xendit.co/v2/invoices",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => json_encode($payload),
  CURLOPT_HTTPHEADER => [
    "Content-Type: application/json"
  ],
  CURLOPT_USERPWD => $secretKey . ":" // Xendit uses Basic Auth
]);

$response = curl_exec($ch);
curl_close($ch);

echo $response;
