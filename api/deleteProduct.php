<?php
require_once __DIR__ . "/../config/auth.php";
api_require_post();
api_require_admin();
require_once __DIR__ . "/../config/database.php";
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$product_id = $data['product_id'] ?? 0;

if (!$product_id) {
    echo json_encode([
        "status" => "error",
        "message" => "Product ID is required"
    ]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Product deleted successfully"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to delete product"
    ]);
}
?>