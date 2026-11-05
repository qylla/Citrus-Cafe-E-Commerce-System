<?php
require_once __DIR__ . "/../config/database.php";

header('Content-Type: application/json');

/* ========= API KEY CHECK ========= */
$headers = getallheaders();
$api_key = (string)($headers['API_KEY'] ?? $headers['X-API-Key'] ?? '');
$expected_key = env('ORDER_API_KEY');

if ($expected_key === '' || !hash_equals($expected_key, $api_key)) {
    http_response_code(401);
    echo json_encode([
        "status" => "error",
        "message" => "Unauthorized access"
    ]);
    exit;
}

/* ========= NEXT CODE ========= */
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode([
        "status" => "error",
        "message" => "No data received"
    ]);
    exit;
}

/* =========================
   GET MAIN ORDER DATA
========================= */
$receipt_no = $data['receipt_no'] ?? '';
$customer_name = $data['customer_name'] ?? '';
$order_type = $data['order_type'] ?? '';
$table_number = $data['table_number'] ?? null;
$payment_method = $data['payment_method'] ?? '';
$total_amount = $data['total_amount'] ?? 0;
$items = $data['items'] ?? [];

/* =========================
   VALIDATION
========================= */
if (
    empty($receipt_no) ||
    empty($customer_name) ||
    empty($order_type) ||
    empty($payment_method) ||
    empty($items)
) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing required fields"
    ]);
    exit;
}

/* =========================
   PAYMENT LOGIC
========================= */
if ($payment_method === "Cash") {
    $payment_status = "Pending";
} else {
    $payment_status = "Complete";
}

$order_status = "Confirmed";

/* =========================
   CONVERT ITEMS TO STRING
   (for orders table summary)
========================= */
$itemStrings = [];
foreach ($items as $item) {
    $product_name = $item['product_name'] ?? 'Unknown Product';
    $quantity = (int)($item['quantity'] ?? 1);
    $itemStrings[] = $product_name . " x" . $quantity;
}
$items_summary = implode(", ", $itemStrings);

/* =========================
   START TRANSACTION
========================= */
$conn->begin_transaction();

try {
    /* =========================
       INSERT INTO ORDERS
    ========================= */
    $stmt = $conn->prepare("INSERT INTO orders 
    (receipt_no, customer_name, items, order_type, table_number, payment_method, payment_status, total_amount, order_status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "sssssssds",
        $receipt_no,
        $customer_name,
        $items_summary,
        $order_type,
        $table_number,
        $payment_method,
        $payment_status,
        $total_amount,
        $order_status
    );

    if (!$stmt->execute()) {
        throw new Exception("Failed to insert order");
    }

    /* =========================
       GET NEW ORDER ID
    ========================= */
    $order_id = $conn->insert_id;

    /* =========================
       INSERT INTO ORDER_ITEMS
    ========================= */
    $itemStmt = $conn->prepare("INSERT INTO order_items 
    (order_id, product_id, product_name, quantity, price, subtotal)
    VALUES (?, ?, ?, ?, ?, ?)");

    foreach ($items as $item) {
        $product_id = (int)($item['product_id'] ?? 0);
        $product_name = $item['product_name'] ?? '';
        $quantity = (int)($item['quantity'] ?? 1);
        $price = (float)($item['price'] ?? 0);
        $subtotal = $quantity * $price;

        $itemStmt->bind_param(
            "iisidd",
            $order_id,
            $product_id,
            $product_name,
            $quantity,
            $price,
            $subtotal
        );

        if (!$itemStmt->execute()) {
            throw new Exception("Failed to insert order item");
        }
    }

    /* =========================
       COMMIT
    ========================= */
    $conn->commit();

    echo json_encode([
        "status" => "success",
        "message" => "Order and order items added successfully",
        "order_id" => $order_id
    ]);
} catch (Exception $e) {
    $conn->rollback();

    error_log('addOrders failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Could not save the order"
    ]);
}
