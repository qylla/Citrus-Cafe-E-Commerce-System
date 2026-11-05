<?php

function generateEmailTemplate($order, $items_string)
{
    $rows = "";
    $order = array_map(function ($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES);
    }, $order);

    // 🔥 STEP 1: CONVERT STRING → ARRAY
    $items_array = explode(",", $items_string);

    foreach ($items_array as $item_raw) {

        // buang space
        $item_raw = trim($item_raw);

        // 🔥 extract data guna regex
        preg_match('/(.*) x(\d+) \(RM ([0-9.]+)\)/', $item_raw, $matches);

        if ($matches) {
            $name = htmlspecialchars($matches[1], ENT_QUOTES);
            $qty = (int)$matches[2];
            $subtotal = (float)$matches[3];
            $price = $subtotal / $qty;
        } else {
            continue;
        }

        $rows .= "
        <tr>
            <td style='padding:12px 0; border-bottom:1px solid #eee;'>{$name}</td>
            <td style='text-align:center; border-bottom:1px solid #eee;'>{$qty}</td>
            <td style='text-align:right; border-bottom:1px solid #eee;'>RM " . number_format($price, 2) . "</td>
            <td style='text-align:right; border-bottom:1px solid #eee;'>RM " . number_format($subtotal, 2) . "</td>
        </tr>
        ";
    }

    return "
    <div style='font-family:Poppins, Arial, sans-serif; background:#f4f6f8; padding:30px;'>

        <div style='max-width:600px; margin:auto; background:white; border-radius:12px; overflow:hidden; box-shadow:0 8px 20px rgba(0,0,0,0.08);'>

            <!-- HEADER -->
            <div style='background:#f59e0b; padding:20px; color:white; text-align:center;'>
                <h2 style='margin:0;'>🍋 Citrus Cafe</h2>
                <p style='margin:5px 0 0;'>Order Receipt</p>
            </div>

            <!-- BODY -->
            <div style='padding:25px;'>

                <p style='margin:0;'>Hi <b>{$order['customer_name']}</b>,</p>
                <p style='margin:10px 0 20px;'>Thanks for your order! Here’s your receipt:</p>

                <!-- ORDER INFO -->
                <table width='100%' style='margin-bottom:20px; font-size:14px;'>
                    <tr>
                        <td><b>Receipt No:</b></td>
                        <td style='text-align:right;'>{$order['receipt_no']}</td>
                    </tr>
                    <tr>
                        <td><b>Order Type:</b></td>
                        <td style='text-align:right;'>{$order['order_type']}</td>
                    </tr>
                    <tr>
                        <td><b>Payment:</b></td>
                        <td style='text-align:right;'>{$order['payment_method']}</td>
                    </tr>
                </table>

                <!-- ITEMS TABLE -->
                <table width='100%' style='border-collapse:collapse; font-size:14px;'>

                    <tr style='background:#f9fafb;'>
                        <th align='left' style='padding:10px;'>Item</th>
                        <th style='padding:10px;'>Qty</th>
                        <th align='right' style='padding:10px;'>Price</th>
                        <th align='right' style='padding:10px;'>Subtotal</th>
                    </tr>

                    $rows

                </table>

                <!-- TOTAL -->
                <div style='margin-top:20px; text-align:right;'>
                    <h3 style='margin:0;'>Total: RM {$order['total_amount']}</h3>
                </div>

            </div>

            <!-- FOOTER -->
            <div style='background:#f1f1f1; padding:15px; text-align:center; font-size:12px; color:#555;'>
                Thank you for ordering with Citrus Cafe 🍊<br>
                <strong>Please collect your order in 10–15 minutes.</strong>
            </div>

        </div>

    </div>
    ";
}