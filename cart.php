<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Cart - The Citrus Cafe</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    background:
        linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.45)),
        url("img/wallpaper-citrus.jpg") no-repeat center center fixed;
    background-size: cover;
}

.cart-wrapper {
    width: 80%;
    margin: 60px auto;
    background: #fff7ec;
    border-radius: 25px;
    padding: 40px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.08);
}

.cart-title {
    font-family: 'Playfair Display', serif;
    font-size: 30px;
    color: #5a341c;
    margin-bottom: 25px;
}

.cart-header {
    display: grid;
    grid-template-columns: 3fr 1fr 1fr 1fr 0.8fr;
    padding: 15px 20px;
    background: #f0d9bc;
    border-radius: 12px;
    font-weight: 600;
}

.cart-item {
    display: grid;
    grid-template-columns: 3fr 1fr 1fr 1fr 0.8fr;
    padding: 25px 20px;
    align-items: center;
    border-bottom: 1px solid #ead2b6;
}

.product-name {
    font-weight: 500;
    color: #5a341c;
}

.qty-box {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.qty-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 1px solid #d7b893;
    background: white;
    font-weight: 600;
}

.price {
    text-align: center;
    font-weight: 500;
}

.remove-btn {
    background: #e74c3c;
    color: white;
    border: none;
    border-radius: 15px;
    padding: 6px 14px;
    font-size: 13px;
}

.total-section {
    margin-top: 40px;
    padding-top: 25px;
    border-top: 2px solid #ead2b6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.total-text {
    font-size: 22px;
    font-weight: 600;
    color: #5a341c;
}

.btn-group-custom {
    display: flex;
    gap: 15px;
}

.cart-btn {
    height: 48px;
    padding: 0 28px;
    border-radius: 30px;
    font-weight: 500;
    border: none;
}

.btn-continue {
    background: #6b3f24;
    color: white;
}

.btn-clear {
    background: #e8c9a7;
    color: #5a341c;
}

.btn-checkout {
    background: linear-gradient(to right, #ff8c00, #ff6a00);
    color: white;
}
</style>
</head>

<body>

<div class="cart-wrapper">

<div class="cart-title">Cart</div>

<div id="cart-container"></div>

<div class="total-section" id="total-section">

    <div class="total-text" id="total"></div>

    <div class="btn-group-custom">

        <button class="cart-btn btn-continue" onclick="addMore()">
            Continue Shopping
        </button>

        <button class="cart-btn btn-clear" onclick="clearCart()">
            Clear Cart
        </button>

        <button class="cart-btn btn-checkout" onclick="proceed()">
            Proceed to Checkout
        </button>

    </div>

</div>

</div>

<script>
let cart = JSON.parse(localStorage.getItem("cart")) || [];

function renderCart() {
    let container = document.getElementById("cart-container");
    let total = 0;
    let totalSection = document.getElementById("total-section");

    container.innerHTML = "";

    if (cart.length === 0) {
        container.innerHTML = "<p>Your cart is empty.</p>";
        totalSection.style.display = "none";
        return;
    }

    totalSection.style.display = "flex";

    container.innerHTML += `
        <div class="cart-header">
            <div>Select Products</div>
            <div style="text-align:center;">Quantity</div>
            <div style="text-align:center;">Price</div>
            <div style="text-align:center;">Subtotal</div>
            <div style="text-align:center;">Action</div>
        </div>
    `;

    cart.forEach((item, index) => {
        let subtotal = item.price * item.quantity;
        total += subtotal;

        container.innerHTML += `
            <div class="cart-item">

                <div class="product-name">${item.name}</div>

                <div class="qty-box">
                    <button class="qty-btn" onclick="changeQty(${index}, -1)">-</button>
                    ${item.quantity}
                    <button class="qty-btn" onclick="changeQty(${index}, 1)">+</button>
                </div>

                <div class="price">RM ${item.price.toFixed(2)}</div>

                <div class="price">RM ${subtotal.toFixed(2)}</div>

                <div style="text-align:center;">
                    <button class="remove-btn" onclick="removeItem(${index})">Remove</button>
                </div>

            </div>
        `;
    });

    document.getElementById("total").innerHTML =
        "Total: RM " + total.toFixed(2);
}

function changeQty(index, change) {
    cart[index].quantity += change;

    if (cart[index].quantity <= 0) {
        cart.splice(index, 1);
    }

    localStorage.setItem("cart", JSON.stringify(cart));
    renderCart();
}

function removeItem(index) {
    cart.splice(index, 1);
    localStorage.setItem("cart", JSON.stringify(cart));
    renderCart();
}

function clearCart() {
    if (confirm("Clear all items from cart?")) {
        cart = [];
        localStorage.removeItem("cart");
        renderCart();
    }
}

function addMore() {
    window.location.href = "products.php";
}

function proceed() {
    window.location.href = "confirmorder.php";
}

renderCart();
</script>

</body>
</html>