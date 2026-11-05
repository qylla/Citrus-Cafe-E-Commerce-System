<?php
require_once __DIR__ . "/../config/auth.php";
api_require_post();
api_require_admin();
require_once __DIR__ . "/../config/database.php";
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$product_id = $data['product_id'] ?? 0;
$name = $data['name'] ?? '';
$description = $data['description'] ?? '';
$price = $data['price'] ?? 0;
$stock_quantity = $data['stock_quantity'] ?? 0;
$status = $data['status'] ?? '';
$image = $data['image'] ?? '';

$image = basename((string)$image);

if (!in_array($status, ["Available", "Sold Out"], true) || !is_numeric($price) || $price < 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid product data"
    ]);
    exit;
}

if (!$product_id) {
    echo json_encode([
        "status" => "error",
        "message" => "Product ID is required"
    ]);
    exit;
}

$stmt = $conn->prepare("UPDATE products 
SET name = ?, description = ?, price = ?, stock_quantity = ?, status = ?, image = ?
WHERE product_id = ?");
$stmt->bind_param("ssdissi", $name, $description, $price, $stock_quantity, $status, $image, $product_id);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Product updated successfully"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to update product"
    ]);
}
?>