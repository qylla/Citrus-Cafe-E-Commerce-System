<?php
require_once __DIR__ . '/../config/auth.php';
require_admin_page();
require_once __DIR__ . '/../config/database.php';

/* =========================
   SUMMARY CARDS
========================= */
$totalOrders = 0;
$totalSales = 0;
$totalTakeAway = 0;
$totalDineIn = 0;

$summarySql = "SELECT 
                COUNT(*) AS total_orders,
                COALESCE(SUM(total_amount), 0) AS total_sales,
                SUM(CASE WHEN order_type = 'Take Away' THEN 1 ELSE 0 END) AS total_takeaway,
                SUM(CASE WHEN order_type = 'Dine In' THEN 1 ELSE 0 END) AS total_dinein
               FROM orders";

$summaryResult = $conn->query($summarySql);

if ($summaryResult && $summaryResult->num_rows > 0) {
    $summaryRow = $summaryResult->fetch_assoc();
    $totalOrders = $summaryRow["total_orders"] ?? 0;
    $totalSales = $summaryRow["total_sales"] ?? 0;
    $totalTakeAway = $summaryRow["total_takeaway"] ?? 0;
    $totalDineIn = $summaryRow["total_dinein"] ?? 0;
}

/* =========================
   RECENT ORDERS
========================= */
$recentOrdersSql = "SELECT receipt_no, customer_name, order_type, total_amount, payment_method, order_status
                    FROM orders
                    ORDER BY order_id DESC
                    LIMIT 5";
$recentOrdersResult = $conn->query($recentOrdersSql);

/* =========================
   PAYMENT SUMMARY
========================= */
$cashCount = 0;
$ewalletCount = 0;
$onlineCount = 0;

$paymentSql = "SELECT 
                SUM(CASE WHEN payment_method = 'Cash' THEN 1 ELSE 0 END) AS cash_count,
                SUM(CASE WHEN payment_method = 'E-Wallet' THEN 1 ELSE 0 END) AS ewallet_count,
                SUM(CASE WHEN payment_method = 'Online Banking' THEN 1 ELSE 0 END) AS online_count
               FROM orders";
$paymentResult = $conn->query($paymentSql);

if ($paymentResult && $paymentResult->num_rows > 0) {
    $paymentRow = $paymentResult->fetch_assoc();
    $cashCount = $paymentRow["cash_count"] ?? 0;
    $ewalletCount = $paymentRow["ewallet_count"] ?? 0;
    $onlineCount = $paymentRow["online_count"] ?? 0;
}



/* =========================
   TOP SELLER (simple based on items column)
   Example items: "Fresh Orange Juice x2"
========================= */
$topSellerName = "No data";
$topSellerQty = 0;

$itemSql = "SELECT items FROM orders";
$itemResult = $conn->query($itemSql);

$productSales = [];

if ($itemResult && $itemResult->num_rows > 0) {
    while ($row = $itemResult->fetch_assoc()) {
        $itemText = $row["items"];

        // split kalau ada multiple items separated by comma
        $parts = explode(",", $itemText);

        foreach ($parts as $part) {
            $part = trim($part);

            if (preg_match('/^(.*)\sx(\d+)$/i', $part, $matches)) {
                $productName = trim($matches[1]);
                $qty = (int)$matches[2];
            } else {
                $productName = $part;
                $qty = 1;
            }

            if (!isset($productSales[$productName])) {
                $productSales[$productName] = 0;
            }

            $productSales[$productName] += $qty;
        }
    }

    if (!empty($productSales)) {
        arsort($productSales);
        $topSellerName = array_key_first($productSales);
        $topSellerQty = $productSales[$topSellerName];
    }
}

$currentDate = date("d F Y");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citrus Cafe | Admin Dashboard</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin-style.css">
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo-box">
                <img src="../img/logonav.png" alt="Citrus Cafe Logo" class="logo">
            </div>
            <div class="brand">Citrus Cafe Admin</div>
        </div>

        <ul class="sidebar-menu">
            <a href="dashboard.php">
                <li class="active"><i class="fas fa-house"></i>Dashboard</li>
            </a>
            <a href="admin_products.php">
                <li><i class="fas fa-mug-hot"></i>Menu (CRUD)</li>
            </a>
            <a href="admin_orders.php">
                <li><i class="fas fa-receipt"></i>Orders</li>
            </a>
            <a href="../logout.php">
                <li class="logout-link"><i class="fas fa-right-from-bracket"></i>Logout</li>
            </a>
        </ul>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <div class="top-header">
            <div>
                <h1>Admin Dashboard</h1>
                <p>Quick overview of orders, sales, and customer activity.</p>
            </div>

            <div class="date-box">
                <i class="far fa-calendar-alt"></i> <strong><?php echo $currentDate; ?></strong>
            </div>
        </div>

        <hr class="top-line">

        <!-- SUMMARY CARDS -->
        <div class="stats-container">
            <div class="card">
                <i class="fas fa-receipt"></i>
                <h3>Total Orders</h3>
                <p><?php echo $totalOrders; ?> Orders</p>
            </div>

            <div class="card">
                <i class="fas fa-wallet"></i>
                <h3>Total Sales</h3>
                <p>RM <?php echo number_format($totalSales, 2); ?></p>
            </div>

            <div class="card">
                <i class="fas fa-bag-shopping"></i>
                <h3>Take Away</h3>
                <p><?php echo $totalTakeAway; ?> Orders</p>
            </div>

            <div class="card">
                <i class="fas fa-utensils"></i>
                <h3>Dine In</h3>
                <p><?php echo $totalDineIn; ?> Orders</p>
            </div>
        </div>


        <!-- LOWER SECTION -->
        <div class="dashboard-grid">

            <!-- LEFT: RECENT ORDERS -->
            <div class="left-section">
                <div class="table-container">
                    <div class="table-header">
                        <h2><i class="fas fa-clock"></i> Recent Orders</h2>
                        <a href="admin_orders.php" class="view-all-btn">View All</a>
                    </div>

                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Receipt No</th>
                                    <th>Customer</th>
                                    <th>Order Type</th>
                                    <th>Total</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if ($recentOrdersResult && $recentOrdersResult->num_rows > 0) { ?>
                                    <?php while ($row = $recentOrdersResult->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row["receipt_no"]); ?></td>
                                            <td><?php echo htmlspecialchars($row["customer_name"]); ?></td>
                                            <td><?php echo htmlspecialchars($row["order_type"]); ?></td>
                                            <td><span class="price">RM <?php echo number_format($row["total_amount"], 2); ?></span></td>
                                            <td><?php echo htmlspecialchars($row["payment_method"]); ?></td>
                                            <td><span class="status-badge completed"><?php echo htmlspecialchars($row["order_status"]); ?></span></td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="6" style="text-align:center;">No recent orders found.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- RIGHT: TOP SELLER + PAYMENT SUMMARY -->
            <div class="right-section">

                <div class="table-container top-seller-box">
                    <h3 style="margin-bottom: 15px; font-size: 18px;">
                        <i class="fas fa-crown" style="color:#f1c40f;"></i> Top Seller
                    </h3>

                    <div class="top-seller-content">
                        <span><?php echo htmlspecialchars($topSellerName); ?></span>
                        <span class="sold-count"><?php echo $topSellerQty; ?> Sold</span>
                    </div>
                </div>

                <div class="table-container">
                    <h3 style="margin-bottom: 18px; font-size: 18px; color:#4b2e1f;">
                        <i class="fas fa-credit-card" style="color:#ff8c00;"></i> Payment Summary
                    </h3>

                    <table>
                        <tbody>
                            <tr>
                                <th style="width: 60%;">Cash</th>
                                <td><?php echo $cashCount; ?> Orders</td>
                            </tr>
                            <tr>
                                <th>E-Wallet</th>
                                <td><?php echo $ewalletCount; ?> Orders</td>
                            </tr>
                            <tr>
                                <th>Online Banking</th>
                                <td><?php echo $onlineCount; ?> Orders</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="table-container">
                    <h3 style="margin-bottom: 18px; font-size: 18px; color:#4b2e1f;">
                        <i class="fas fa-circle-info" style="color:#ff8c00;"></i> Quick Notes
                    </h3>

                    <div class="stock-alert">Orders are automatically confirmed after customer checkout.</div>
                    <div class="stock-alert">Confirmation email is sent immediately after order submission.</div>
                    <div class="stock-alert">Estimated ready time for pickup or dine in is 15 minutes.</div>
                </div>

            </div>
        </div>

    </div>

</body>

</html>