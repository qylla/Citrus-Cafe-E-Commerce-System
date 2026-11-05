<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/email_template.php';

secure_session_start();
header('Content-Type: application/json');

function receipt_reply($code, $status, $message)
{
    http_response_code($code);
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    receipt_reply(405, 'error', 'Invalid request method');
}

$data = json_decode(file_get_contents('php://input'), true);
$orderId = (int)($data['order_id'] ?? 0);

// A visitor can only trigger the receipt for their own order, and only once.
if ($orderId <= 0 || !in_array($orderId, $_SESSION['my_orders'] ?? [], true)) {
    receipt_reply(403, 'error', 'Not allowed');
}
if (in_array($orderId, $_SESSION['receipts_sent'] ?? [], true)) {
    receipt_reply(409, 'error', 'Receipt already sent');
}

$stmt = $conn->prepare('SELECT * FROM orders WHERE order_id = ?');
$stmt->bind_param('i', $orderId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    receipt_reply(404, 'error', 'Order not found');
}

$apiKey = env('SENDGRID_API_KEY');
$fromEmail = env('MAIL_FROM_ADDRESS');

if ($apiKey === '' || $fromEmail === '') {
    receipt_reply(200, 'skipped', 'Email is not configured');
}

$body = [
    'personalizations' => [[
        'to' => [['email' => $order['email'], 'name' => $order['customer_name']]],
    ]],
    'from' => ['email' => $fromEmail, 'name' => env('MAIL_FROM_NAME', 'Citrus Cafe')],
    'subject' => 'Your Citrus Cafe receipt',
    'content' => [[
        'type' => 'text/html',
        'value' => generateEmailTemplate($order, $order['items']),
    ]],
];

$ch = curl_init('https://api.sendgrid.com/v3/mail/send');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($body),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json',
    ],
]);
curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode >= 200 && $httpCode < 300) {
    $_SESSION['receipts_sent'][] = $orderId;
    receipt_reply(200, 'success', 'Receipt sent');
}

error_log('SendGrid returned HTTP ' . $httpCode);
receipt_reply(502, 'error', 'The receipt email could not be sent');
