<?php
require_once __DIR__ . "/../config/auth.php";
api_require_post();
api_require_admin();
require_once __DIR__ . "/../config/database.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$order_id = $data['order_id'] ?? null;

if (!$order_id) {
    echo json_encode(["status"=>"error","message"=>"Order ID required"]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM orders WHERE order_id=?");
$stmt->bind_param("i", $order_id);

if ($stmt->execute()) {
    echo json_encode(["status"=>"success","message"=>"Order deleted"]);
} else {
    echo json_encode(["status"=>"error"]);
}
?>