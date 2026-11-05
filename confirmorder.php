<!DOCTYPE html>
<html>
<head>
    
    <meta charset="UTF-8">
    <title>Checkout</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">

    <style>
/* DESIGN KEKAL */
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;

    background:
        linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.5)),
        url("img/wallpaper-citrus.jpg");

    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
}

.container {
    width: 90%;
    margin: 50px auto;
    display: grid;
    grid-template-columns: 1.2fr 0.8fr;
    gap: 30px;
}

.card-box {
    background: rgba(255, 248, 238, 0.96);
    border-radius: 20px;
    padding: 30px 35px;
    box-sizing: border-box;
}

.section-title {
    font-family: 'Playfair Display', serif;
    font-size: 28px;
    margin-bottom: 20px;
}

input, textarea {
    width: 100%;
    padding: 14px 16px;
    border-radius: 12px;
    border: 1px solid #ddd;
    margin-bottom: 18px;
    font-size: 14px;
    box-sizing: border-box;
}

textarea {
    resize: none;
    height: 100px;
}

.order-type-box {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-bottom: 20px;
}

.order-type {
    padding: 12px 25px;
    border-radius: 30px;
    border: 2px solid #ddd;
    cursor: pointer;
}

.order-type.active {
    border-color: orange;
    background: #fff3e6;
}

.payment-box {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
}

.payment-option {
    flex: 1;
    padding: 15px;
    border-radius: 12px;
    border: 2px solid #ddd;
    text-align: center;
    cursor: pointer;
}

.payment-option img {
    width: 40px;
    height: 40px;
    object-fit: contain;
    display: block;
    margin: 0 auto 8px;
}

.payment-option.active {
    border-color: orange;
    background: #fff3e6;
}

/* ✅ NOTICE STYLE (KECIL JE) */
.email-notice {
    font-size: 11px;
    color: #777;
    margin-top: -10px;
    margin-bottom: 15px;
}

.note-box {
    background: #fff3e6;
    border-left: 5px solid #ff8c00;
    padding: 12px;
    border-radius: 10px;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px dashed #ccc;
}

.total {
    font-size: 24px;
    font-weight: bold;
    color: #d35400;
    margin-top: 10px;
}

.btn {
    padding: 12px 20px;
    border-radius: 30px;
    border: none;
    cursor: pointer;
}

.btn-place {
    background: linear-gradient(to right, #ff8c00, #ff6a00);
    color: white;
}

.btn-back {
    background: #eee;
}

.btn-group {
    margin-top: 15px;
    display: flex;
    gap: 10px;
}
    </style>
</head>

<body>

<div class="container">

    <div class="card-box">
        <div class="section-title">Order Details</div>

        <input type="text" id="name" placeholder="Enter your name">
        
        <input type="email" id="email" placeholder="Enter your email">

        <!-- ✅ NOTICE TAMBAH SINI -->
        <div class="email-notice">
            *Receipt email may be delivered to your Spam/Junk folder. Please check there if not found in Inbox.
        </div>

        <div class="order-type-box">
            <div class="order-type" onclick="selectType('Dine In', this)">Dine In</div>
            <div class="order-type" onclick="selectType('Take Away', this)">Take Away</div>
        </div>

        <textarea id="remarks" placeholder="Any remarks..."></textarea>

        <div class="section-title" style="font-size:20px;">Payment Method</div>

        <div class="payment-box">

            <div class="payment-option" onclick="selectPayment('Cash', this)">
                <img src="img/cash.png">
                Cash
            </div>

            <div class="payment-option" onclick="selectPayment('Online Banking', this)">
                <img src="img/Fpx.png">
                Online Banking
            </div>

            <div class="payment-option" onclick="selectPayment('E-Wallet', this)">
                <img src="img/ewallet.png">
                E-Wallet
            </div>

        </div>

        <div class="note-box" id="payment-note">
            Please select payment method.
        </div>
    </div>

    <div class="card-box">
        <div class="section-title">Order Summary</div>

        <div id="summary"></div>
        <div class="total" id="total"></div>

        <div class="btn-group">
            <button class="btn btn-back" onclick="back()">Back</button>
            <button class="btn btn-place" onclick="placeOrder()">Place Order</button>
        </div>
    </div>

</div>

<script>
let cart = JSON.parse(localStorage.getItem("cart")) || [];
let selectedPayment = "";
let selectedType = "";

function loadSummary() {
    let total = 0;
    let summaryDiv = document.getElementById("summary");

    cart.forEach(item => {
        let subtotal = item.price * item.quantity;
        total += subtotal;

        summaryDiv.innerHTML += `
            <div class="summary-item">
                <div>${item.name}<br><small>${item.quantity} x RM ${item.price}</small></div>
                <div>RM ${subtotal.toFixed(2)}</div>
            </div>
        `;
    });

    document.getElementById("total").innerText =
        "Total: RM " + total.toFixed(2);
}

function selectType(type, el) {
    selectedType = type;
    document.querySelectorAll(".order-type").forEach(e => e.classList.remove("active"));
    el.classList.add("active");
}

function selectPayment(method, el) {
    selectedPayment = method;

    document.querySelectorAll(".payment-option").forEach(e => e.classList.remove("active"));
    el.classList.add("active");

    let note = document.getElementById("payment-note");

    if (method === "Cash") {
        note.innerText = "Pay at counter.";
    } else {
        note.innerText = "Online payment is not connected yet. Staff will confirm your payment.";
    }
}

function placeOrder() {
    let name = document.getElementById("name").value;
    let email = document.getElementById("email").value;

    if (!name || !email || !selectedType || !selectedPayment) {
        alert("Complete all fields!");
        return;
    }

    let orderData = {
        name,
        email,
        order_type: selectedType,
        payment_method: selectedPayment,
        items: cart
    };

    document.body.innerHTML = `
        <div style="display:flex;justify-content:center;align-items:center;height:100vh;font-family:Poppins;">
            <h2>Processing your order...</h2>
        </div>
    `;

    fetch("save_order.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(orderData)
    })
    .then(res => res.json())
    .then(data => {

        if (data.status !== "success") {
            alert(data.message || "We could not save your order.");
            window.location.href = "cart.php";
            return;
        }

        let orderId = data.order_id;

        fetch("send_receipt_email.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                order_id: orderId
            })
        })
.then(res => res.json())
.catch(() => null)
.then(() => {

    localStorage.removeItem("cart");
    window.location.href = "invoice.php?id=" + orderId;
});

    })
    .catch(err => {
        console.error(err);
        alert("Error placing order!");
    });
}

function back() {
    window.location.href = "cart.php";
}

loadSummary();
</script>

</body>
</html>