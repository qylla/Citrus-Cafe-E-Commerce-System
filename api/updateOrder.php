<?php
require_once __DIR__ . "/../config/auth.php";
api_require_post();
api_require_admin();
require_once __DIR__ . "/../config/database.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$order_id = $data['order_id'] ?? null;
$order_status = $data['order_status'] ?? '';

if (!in_array($order_status, ["Confirmed", "Preparing", "Ready", "Completed", "Cancelled"], true)) {
    echo json_encode(["status"=>"error","message"=>"Invalid order status"]);
    exit;
}

if (!$order_id) {
    echo json_encode(["status"=>"error","message"=>"Order ID required"]);
    exit;
}

$stmt = $conn->prepare("UPDATE orders SET order_status=? WHERE order_id=?");
$stmt->bind_param("si", $order_status, $order_id);

if ($stmt->execute()) {
    echo json_encode(["status"=>"success","message"=>"Order updated"]);
} else {
    echo json_encode(["status"=>"error"]);
}
?>