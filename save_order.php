<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/database.php';

secure_session_start();
header('Content-Type: application/json');

function order_fail($message, $code = 400)
{
    http_response_code($code);
    echo json_encode(['status' => 'error', 'message' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    order_fail('Invalid request method', 405);
}

$data = json_decode(file_get_contents('php://input'), true);

if (!is_array($data)) {
    order_fail('Invalid order data');
}

$name = trim((string)($data['name'] ?? ''));
$email = trim((string)($data['email'] ?? ''));
$type = (string)($data['order_type'] ?? '');
$payment = (string)($data['payment_method'] ?? '');
$items = $data['items'] ?? [];

if ($name === '' || mb_strlen($name) > 100) {
    order_fail('Please enter your name.');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    order_fail('Please enter a valid email address.');
}
if (!in_array($type, ['Dine In', 'Take Away'], true)) {
    order_fail('Please choose an order type.');
}
if (!in_array($payment, ['Cash', 'Online Banking', 'E-Wallet'], true)) {
    order_fail('Please choose a payment method.');
}
if (!is_array($items) || count($items) === 0 || count($items) > 50) {
    order_fail('Your cart is empty.');
}

// Prices and availability always come from the database, never from the browser.
$lookup = $conn->prepare('SELECT product_id, name, price, stock_quantity, status FROM products WHERE product_id = ?');
$lines = [];
$total = 0.0;
$summary = [];

foreach ($items as $item) {
    $productId = (int)($item['id'] ?? $item['product_id'] ?? 0);
    $quantity = (int)($item['quantity'] ?? 0);

    if ($productId <= 0 || $quantity <= 0 || $quantity > 99) {
        order_fail('Your cart has an invalid item.');
    }

    $lookup->bind_param('i', $productId);
    $lookup->execute();
    $product = $lookup->get_result()->fetch_assoc();

    if (!$product) {
        order_fail('An item in your cart is no longer on the menu.');
    }
    if ($product['status'] !== 'Available' || (int)$product['stock_quantity'] < $quantity) {
        order_fail($product['name'] . ' is not available in that quantity.');
    }

    $price = (float)$product['price'];
    $subtotal = $price * $quantity;
    $total += $subtotal;
    $lines[] = [$productId, $product['name'], $quantity, $price, $subtotal];
    $summary[] = $product['name'] . ' x' . $quantity . ' (RM ' . number_format($subtotal, 2) . ')';
}

$itemsText = implode(', ', $summary);

// No payment gateway is connected, so no order can be marked as paid automatically.
$paymentStatus = 'Pending';

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("INSERT INTO orders
        (receipt_no, customer_name, email, items, order_type, table_number, payment_method, payment_status, total_amount, order_status)
        VALUES ('', ?, ?, ?, ?, NULL, ?, ?, ?, 'Confirmed')");
    $stmt->bind_param('ssssssd', $name, $email, $itemsText, $type, $payment, $paymentStatus, $total);
    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }
    $orderId = $stmt->insert_id;

    $itemStmt = $conn->prepare('INSERT INTO order_items
        (order_id, product_id, product_name, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?, ?)');
    foreach ($lines as [$productId, $productName, $quantity, $price, $subtotal]) {
        $itemStmt->bind_param('iisidd', $orderId, $productId, $productName, $quantity, $price, $subtotal);
        if (!$itemStmt->execute()) {
            throw new Exception($itemStmt->error);
        }
    }

    $payStmt = $conn->prepare('INSERT INTO payments (order_id, payment_method, payment_status, amount) VALUES (?, ?, ?, ?)');
    $payStmt->bind_param('issd', $orderId, $payment, $paymentStatus, $total);
    if (!$payStmt->execute()) {
        throw new Exception($payStmt->error);
    }

    $receiptNo = 'RC' . (1000 + $orderId);
    $receiptStmt = $conn->prepare('UPDATE orders SET receipt_no = ? WHERE order_id = ?');
    $receiptStmt->bind_param('si', $receiptNo, $orderId);
    if (!$receiptStmt->execute()) {
        throw new Exception($receiptStmt->error);
    }

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    error_log('Order failed: ' . $e->getMessage());
    order_fail('We could not save your order. Please try again.', 500);
}

// Only this visitor (or an admin) can open this order's receipt.
$_SESSION['my_orders'][] = $orderId;

echo json_encode([
    'status' => 'success',
    'order_id' => $orderId,
    'receipt_no' => $receiptNo,
    'message' => 'Order saved successfully',
]);
