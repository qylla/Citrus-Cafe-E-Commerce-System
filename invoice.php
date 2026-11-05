<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/database.php';

secure_session_start();

$id = (int)($_GET['id'] ?? 0);
$isOwner = in_array($id, $_SESSION['my_orders'] ?? [], true);
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';

$order = null;

if ($id > 0 && ($isOwner || $isAdmin)) {
    $stmt = $conn->prepare('SELECT * FROM orders WHERE order_id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
}

if (!$order) {
    http_response_code(404);
    die('Receipt not found.');
}

date_default_timezone_set("Asia/Kuala_Lumpur");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>

    <style>
        body {
            margin: 0;
            font-family: 'Courier New', monospace;

            background: url("img/wallpaper-citrus.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;

            display: flex;
            justify-content: center;
            align-items: flex-start;

            min-height: 100vh;
            padding: 60px 0;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.4);
            z-index: 0;
        }

        .receipt {
            background: #fff;
            width: 240px;
            padding: 30px 20px;
            border-radius: 4px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            position: relative;
            z-index: 1;
        }

        .logo {
            display: block;
            margin: 0 auto -30px auto;
            max-width: 100px;
        }

        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .small {
            text-align: center;
            font-size: 11px;
            color: #555;
            margin-bottom: 10px;
        }

        .center {
            text-align: center;
            font-size: 12px;
            margin: 15px 0;
        }

        .line {
            border-top: 1px dashed #aaa;
            margin: 18px 0;
        }

        .row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin: 10px 0;
        }

        .total {
            font-weight: bold;
            font-size: 14px;
        }

        .footer {
            text-align: center;
            margin-top: 25px;
            font-size: 12px;
            color: #555;
        }

        .spacer {
            height: 10px;
        }

        @media print {
            body {
                background: #fff !important;
                display: block !important;
                padding: 0;
                margin: 0;
            }

            .overlay {
                display: none !important;
            }

            .receipt {
                width: 80mm;
                margin: 0 auto;
                padding: 10px;
                box-shadow: none;
                border-radius: 0;
            }

            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
    </style>
</head>

<body>

<div class="overlay"></div>

<div class="receipt">

    <img src="img/Logo2.png" class="logo">

    <div class="title">CITRUS CAFE</div>
    <div class="small">Fresh Juice & Beverages</div>

    <div class="center"><?= date("D, M d, Y h:i:s A") ?></div>

    <div class="line"></div>

    <div class="row"><span>Receipt</span><span><?= htmlspecialchars((string)$order['receipt_no']) ?></span></div>
    <div class="row"><span>Order ID</span><span><?= htmlspecialchars((string)$order['order_id']) ?></span></div>
    <div class="row"><span>Name</span><span><?= htmlspecialchars((string)$order['customer_name']) ?></span></div>
    <div class="row"><span>Type</span><span><?= htmlspecialchars((string)$order['order_type']) ?></span></div>
    <div class="row"><span>Payment</span><span><?= htmlspecialchars((string)$order['payment_method']) ?></span></div>

    <div class="line"></div>

    <!-- 🔥 ITEMS FIX BETUL-BETUL -->
<?php 
$items_raw = $order['items'];

// ✅ FORMAT BARU
if (strpos($items_raw, "|") !== false) {

    $items = explode(";", $items_raw);

    foreach ($items as $item) {
        if (empty(trim($item))) continue;

        list($name, $qty, $subtotal) = explode("|", $item);
?>

        <div style="font-size:12px; margin-top:8px;">
            <?= htmlspecialchars($name) ?>
        </div>

        <div class="row">
            <span>x<?= (int)$qty ?></span>
            <span>RM <?= number_format((float)$subtotal,2) ?></span>
        </div>

<?php
    }

} 
// ✅ FORMAT LAMA (AUTO PECAHKAN JADI LIST)
else {

    $items = explode(",", $items_raw);

    foreach ($items as $item) {
        $item = trim($item);
        if (empty($item)) continue;
?>

        <div style="font-size:12px; margin-top:8px;">
            <?= htmlspecialchars($item) ?>
        </div>

<?php
    }

}
?>

    <div class="line"></div>

    <div class="row total">
        <span>TOTAL</span>
        <span>RM <?= number_format($order['total_amount'],2) ?></span>
    </div>

    <div class="line"></div>

    <div class="footer">
        Thank you for your order 🍊<br>
        Please take your order in 10–15 minutes
    </div>

    <div class="spacer"></div>

</div>

</body>
</html>