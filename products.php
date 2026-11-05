<?php
include("config/database.php");

$sql = "SELECT * FROM products ORDER BY product_id ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Products - The Citrus Kitchen</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="nav-wrapper">
        <div class="custom-nav">
            <div class="nav-left">
                <img src="img/logonav.png" class="logo" alt="Logo">
                <span class="brand">The Citrus Cafe</span>
            </div>

            <div class="nav-center">
                <a href="home.php">Home</a>
                <a href="products.php" class="active">Products</a>
                <a href="cart.php">Cart</a>
                <a href="about-us.php">About Us</a>
                <a href="profile.php">Profile</a>
            </div>

            <div class="nav-right">
                <a href="logout.php" class="register-btn">Logout</a>
            </div>
        </div>
    </div>

    <div class="container mt-5 mb-5">

        <div class="search-box">
            <input type="text"
                   id="searchInput"
                   class="search-input"
                   placeholder="Search product..."
                   onkeyup="searchProduct()">
        </div>

        <div class="row g-4">
            <?php if ($result && $result->num_rows > 0) { ?>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <div class="col-md-4 product-item">
                        <div class="product-card text-center">
                            <img src="img/<?php echo htmlspecialchars($row['image']); ?>" class="product-img" alt="Product Image">

                            <div class="product-title">
                                <?php echo htmlspecialchars($row['name']); ?>
                            </div>

                            <p class="text-muted">
                                <?php echo htmlspecialchars($row['description']); ?>
                            </p>

                            <?php if ($row['status'] == 'Available' && $row['stock_quantity'] > 0) { ?>
                                <p class="available">Available</p>

                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="price">RM <?php echo number_format($row['price'], 2); ?></span>

                                    <!-- ✅ UPDATED BUTTON -->
                                    <button class="btn btn-orange"
                                        onclick="addToCart(
                                            <?php echo $row['product_id']; ?>,
                                            <?php echo htmlspecialchars(json_encode($row['name']), ENT_QUOTES); ?>,
                                            <?php echo $row['price']; ?>
                                        )">
                                        Add to Cart
                                    </button>

                                </div>
                            <?php } else { ?>
                                <p class="sold-out">Sold Out</p>

                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="price">RM <?php echo number_format($row['price'], 2); ?></span>
                                    <button type="button" class="btn btn-secondary rounded-pill px-4" disabled>Unavailable</button>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p style="text-align:center;">No products available.</p>
            <?php } ?>
        </div>
    </div>

    <script>
        function searchProduct() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let products = document.getElementsByClassName("product-item");

            for (let i = 0; i < products.length; i++) {
                let title = products[i]
                    .getElementsByClassName("product-title")[0]
                    .innerText.toLowerCase();

                if (title.includes(input)) {
                    products[i].style.display = "";
                } else {
                    products[i].style.display = "none";
                }
            }
        }

        // ✅ ADD TO CART FUNCTION
        function addToCart(id, name, price) {
            let cart = JSON.parse(localStorage.getItem("cart")) || [];

            let existing = cart.find(item => item.id === id);

            if (existing) {
                existing.quantity++;
            } else {
                cart.push({
                    id: id,
                    name: name,
                    price: price,
                    quantity: 1
                });
            }

            localStorage.setItem("cart", JSON.stringify(cart));

            alert("Added to cart!");
        }
    </script>

</body>
</html>