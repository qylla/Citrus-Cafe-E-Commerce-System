<?php
require_once __DIR__ . '/../config/auth.php';
require_admin_page();
require_once __DIR__ . '/../config/database.php';

/* =========================
   SEARCH
========================= */
$search = trim($_GET['search'] ?? "");

/* =========================
   DISPLAY ORDERS
========================= */
if (!empty($search)) {
    $sql = "SELECT * FROM orders
            WHERE receipt_no LIKE ? 
               OR customer_name LIKE ?
               OR items LIKE ?
               OR payment_method LIKE ?
            ORDER BY order_id DESC";

    $stmt = $conn->prepare($sql);
    $like = "%$search%";
    $stmt->bind_param("ssss", $like, $like, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM orders ORDER BY order_id DESC";
    $result = $conn->query($sql);
}

/* =========================
   SUMMARY CARDS
========================= */
$totalOrders = 0;
$completePayments = 0;
$pendingPayments = 0;

$summarySql = "SELECT 
                COUNT(*) AS total_orders,
                SUM(CASE WHEN payment_status = 'Complete' THEN 1 ELSE 0 END) AS complete_payments,
                SUM(CASE WHEN payment_status = 'Pending' THEN 1 ELSE 0 END) AS pending_payments
               FROM orders";

$summaryResult = $conn->query($summarySql);

if ($summaryResult && $summaryResult->num_rows > 0) {
    $summaryRow = $summaryResult->fetch_assoc();
    $totalOrders = $summaryRow['total_orders'] ?? 0;
    $completePayments = $summaryRow['complete_payments'] ?? 0;
    $pendingPayments = $summaryRow['pending_payments'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citrus Cafe | Orders</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin-style.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .customer-link {
            color: #4b2e1f;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }

        .customer-link:hover {
            color: #ff8c00;
            text-decoration: underline;
        }
    </style>
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
                <li><i class="fas fa-house"></i>Dashboard</li>
            </a>
            <a href="admin_products.php">
                <li ><i class="fas fa-mug-hot"></i>Menu (CRUD)</li>
            </a>
            <a href="admin_orders.php">
                <li class="active"><i class="fas fa-receipt"></i>Orders</li>
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
                <h1>Customer Orders</h1>
                <p>Monitor customer order details, order type, table number, and payment status.</p>
            </div>

            <form method="GET" action="admin_orders.php" class="search-box">
                <i class="fas fa-magnifying-glass"></i>
                <input type="text" name="search" placeholder="Search receipt number or customer..." value="<?php echo htmlspecialchars($search); ?>">
            </form>
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
                <i class="fas fa-circle-check"></i>
                <h3>Payment Complete</h3>
                <p><?php echo $completePayments; ?> Complete</p>
            </div>

            <div class="card">
                <i class="fas fa-money-bill-wave"></i>
                <h3>Payment Pending</h3>
                <p><?php echo $pendingPayments; ?> Orders</p>
            </div>
        </div>

        <!-- ORDER TABLE -->
        <div class="table-container">
            <div class="table-header">
                <h2><i class="fas fa-list-ul"></i> Order List</h2>
                <a href="admin_orders.php" class="view-all-btn">View All</a>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Receipt No</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Order Type</th>
                            <th>Table No</th>
                            <th>Payment Method</th>
                            <th>Payment Status</th>
                            <th>Total</th>
                            <th>Order Status</th>
                            <th>Ready Time</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if ($result && $result->num_rows > 0) { ?>
                            <?php while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['receipt_no']); ?></td>

                                    <td>
                                        <a href="#"
                                            class="customer-link view-order"
                                            data-receipt="<?php echo htmlspecialchars($row['receipt_no']); ?>"
                                            data-name="<?php echo htmlspecialchars($row['customer_name']); ?>"
                                            data-items="<?php echo htmlspecialchars($row['items']); ?>"
                                            data-type="<?php echo htmlspecialchars($row['order_type']); ?>"
                                            data-table="<?php echo htmlspecialchars($row['table_number'] ?? ''); ?>"
                                            data-payment="<?php echo htmlspecialchars($row['payment_method']); ?>"
                                            data-paymentstatus="<?php echo htmlspecialchars($row['payment_status']); ?>"
                                            data-total="RM <?php echo number_format($row['total_amount'], 2); ?>"
                                            data-orderstatus="<?php echo htmlspecialchars($row['order_status']); ?>"
                                            data-readytime="<?php
                                                            if (!empty($row['created_at'])) {
                                                                $time = strtotime($row['created_at']);
                                                                echo date('h:i A', $time + (15 * 60));
                                                            } else {
                                                                echo '-';
                                                            }
                                                            ?>">
                                            <?php echo htmlspecialchars($row['customer_name']); ?>
                                        </a>
                                    </td>

                                    <td><?php echo htmlspecialchars($row['items']); ?></td>
                                    <td><?php echo htmlspecialchars($row['order_type']); ?></td>

                                    <td>
                                        <?php
                                        if ($row['order_type'] === 'Dine In' && !empty($row['table_number'])) {
                                            echo htmlspecialchars($row['table_number']);
                                        } else {
                                            echo "-";
                                        }
                                        ?>
                                    </td>

                                    <td><?php echo htmlspecialchars($row['payment_method']); ?></td>

                                    <td>
                                        <?php if ($row['payment_status'] === "Complete") { ?>
                                            <span class="status-badge available">Complete</span>
                                        <?php } else { ?>
                                            <span class="status-badge pending">Pending</span>
                                        <?php } ?>
                                    </td>

                                    <td>
                                        <span class="price">RM <?php echo number_format($row['total_amount'], 2); ?></span>
                                    </td>

                                    <td>
                                        <span class="status-badge completed">
                                            <?php echo htmlspecialchars($row['order_status']); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?php
                                        if (!empty($row['created_at'])) {
                                            $time = strtotime($row['created_at']);
                                            echo date("h:i A", $time + (15 * 60));
                                        } else {
                                            echo "-";
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="10" style="text-align:center;">No orders found.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ORDER INFORMATION -->
        <div class="table-container" style="margin-top: 30px;">
            <div class="table-header">
                <h2><i class="fas fa-circle-info"></i> Order Information</h2>
            </div>

            <table>
                <tbody>
                    <tr>
                        <th style="width: 25%;">Flow Summary</th>
                        <td>
                            Customer selects product from cart, chooses <strong>Take Away</strong> or <strong>Dine In</strong>,
                            enters table number for dine in, selects payment method, then confirms order.
                        </td>
                    </tr>
                    <tr>
                        <th>Cash Payment</th>
                        <td>Payment Status = <strong>Pending</strong></td>
                    </tr>
                    <tr>
                        <th>Online Banking / E-Wallet</th>
                        <td>Payment Status = <strong>Pending</strong> (no payment gateway is connected)</td>
                    </tr>
                    <tr>
                        <th>Order Status</th>
                        <td><strong>Confirmed</strong></td>
                    </tr>
                    <tr>
                        <th>Ready Time</th>
                        <td>Automatically displayed as order time + 15 minutes</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const orderLinks = document.querySelectorAll(".view-order");
            const esc = value => {
                const box = document.createElement("div");
                box.textContent = value;
                return box.innerHTML;
            };

            orderLinks.forEach(link => {
                link.addEventListener("click", function(e) {
                    e.preventDefault();

                    const receipt = this.dataset.receipt || "-";
                    const name = this.dataset.name || "-";
                    const items = this.dataset.items || "-";
                    const type = this.dataset.type || "-";
                    const tableNo = this.dataset.table ? this.dataset.table : "-";
                    const payment = this.dataset.payment || "-";
                    const paymentStatus = this.dataset.paymentstatus || "-";
                    const total = this.dataset.total || "-";
                    const orderStatus = this.dataset.orderstatus || "-";
                    const readyTime = this.dataset.readytime || "-";

                    Swal.fire({
                        title: 'Order Details',
                        html: `
                            <div style="text-align:left; line-height:1.9;">
                                <p><strong>Receipt No:</strong> ${esc(receipt)}</p>
                                <p><strong>Customer:</strong> ${esc(name)}</p>
                                <p><strong>Items:</strong> ${esc(items)}</p>
                                <p><strong>Order Type:</strong> ${esc(type)}</p>
                                <p><strong>Table No:</strong> ${esc(tableNo)}</p>
                                <p><strong>Payment Method:</strong> ${esc(payment)}</p>
                                <p><strong>Payment Status:</strong> ${esc(paymentStatus)}</p>
                                <p><strong>Total:</strong> ${esc(total)}</p>
                                <p><strong>Order Status:</strong> ${esc(orderStatus)}</p>
                                <p><strong>Ready Time:</strong> ${esc(readyTime)}</p>
                            </div>
                        `,
                        icon: 'info',
                        confirmButtonText: 'Close',
                        confirmButtonColor: '#ff8c00',
                        background: '#fffaf3',
                        color: '#2c1b10',
                        width: 550
                    });
                });
            });
        });
    </script>

</body>

</html>