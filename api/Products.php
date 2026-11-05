<?php
include("../config/database.php");

header('Content-Type: application/json');

$sql = "SELECT * FROM products ORDER BY product_id ASC";
$result = $conn->query($sql);

$products = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

echo json_encode($products);
?>