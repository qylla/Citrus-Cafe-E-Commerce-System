<?php
require_once __DIR__ . "/../config/auth.php";
api_require_admin();
require_once __DIR__ . "/../config/database.php";

header('Content-Type: application/json');

$sql = "SELECT * FROM orders ORDER BY order_id ASC";
$result = $conn->query($sql);

$orders = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

echo json_encode($orders);
?>