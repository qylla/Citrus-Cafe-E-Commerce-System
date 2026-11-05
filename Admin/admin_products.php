<?php
require_once __DIR__ . '/../config/auth.php';
require_admin_page();
require_once __DIR__ . '/../config/database.php';

$editMode = false;
$editProduct = null;

/* =========================
   SEARCH
========================= */
$search = trim($_GET['search'] ?? "");

/* =========================
   POST REQUESTS MUST CARRY THE FORM TOKEN
========================= */
if ($_SERVER["REQUEST_METHOD"] === "POST" && !csrf_valid()) {
    header("Location: admin_products.php?status=session_error");
    exit;
}

/* =========================
   DELETE
========================= */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_id"])) {
    $deleteId = (int) $_POST["delete_id"];

    if ($deleteId > 0) {
        $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
        $stmt->bind_param("i", $deleteId);

        if ($stmt->execute()) {
            header("Location: admin_products.php?status=deleted");
            exit;
        } else {
            header("Location: admin_products.php?status=delete_error");
            exit;
        }
    }
}

/* =========================
   EDIT LOAD
========================= */
if (isset($_GET["edit"])) {
    $editId = (int) $_GET["edit"];

    if ($editId > 0) {
        $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
        $stmt->bind_param("i", $editId);
        $stmt->execute();
        $resultEdit = $stmt->get_result();

        if ($resultEdit->num_rows > 0) {
            $editProduct = $resultEdit->fetch_assoc();
            $editMode = true;
        }
    }
}

/* =========================
   ADD / UPDATE
========================= */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = trim($_POST["product_id"] ?? "");
    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = trim($_POST["price"] ?? "");
    $stock_quantity = trim($_POST["stock_quantity"] ?? "");
    $status = trim($_POST["status"] ?? "");

    if ($name === "" || !is_numeric($price) || (float)$price < 0 || !ctype_digit($stock_quantity)
        || !in_array($status, ["Available", "Sold Out"], true)) {
        header("Location: admin_products.php?status=invalid");
        exit;
    }

    $imageName = "";

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE) {
        $allowedTypes = ["image/jpeg" => "jpg", "image/png" => "png", "image/webp" => "webp"];
        $tmpName = $_FILES["image"]["tmp_name"] ?? "";
        $mime = $_FILES["image"]["error"] === UPLOAD_ERR_OK ? (new finfo(FILEINFO_MIME_TYPE))->file($tmpName) : "";

        if (!isset($allowedTypes[$mime]) || $_FILES["image"]["size"] > 5 * 1024 * 1024 || getimagesize($tmpName) === false) {
            header("Location: admin_products.php?status=image_error");
            exit;
        }

        $imageName = bin2hex(random_bytes(12)) . "." . $allowedTypes[$mime];

        if (!move_uploaded_file($tmpName, __DIR__ . "/../img/" . $imageName)) {
            header("Location: admin_products.php?status=image_error");
            exit;
        }
    }

    /* UPDATE */
    if (!empty($product_id)) {
        $product_id = (int) $product_id;

        if (!empty($imageName)) {
            $stmt = $conn->prepare("UPDATE products 
                SET name = ?, description = ?, price = ?, stock_quantity = ?, status = ?, image = ?
                WHERE product_id = ?");
            $stmt->bind_param("ssdissi", $name, $description, $price, $stock_quantity, $status, $imageName, $product_id);
        } else {
            $stmt = $conn->prepare("UPDATE products 
                SET name = ?, description = ?, price = ?, stock_quantity = ?, status = ?
                WHERE product_id = ?");
            $stmt->bind_param("ssdisi", $name, $description, $price, $stock_quantity, $status, $product_id);
        }

        if ($stmt->execute()) {
            header("Location: admin_products.php?status=updated");
            exit;
        } else {
            header("Location: admin_products.php?status=update_error");
            exit;
        }
    }

    /* ADD */ else {
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock_quantity, status, image)
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdiss", $name, $description, $price, $stock_quantity, $status, $imageName);

        if ($stmt->execute()) {
            header("Location: admin_products.php?status=added");
            exit;
        } else {
            header("Location: admin_products.php?status=add_error");
            exit;
        }
    }
}

/* =========================
   DISPLAY (WITH SEARCH)
========================= */
if (!empty($search)) {
    $sql = "SELECT * FROM products 
            WHERE name LIKE ? OR description LIKE ?
            ORDER BY product_id DESC";

    $stmt = $conn->prepare($sql);
    $like = "%$search%";
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM products ORDER BY product_id ASC";
    $result = $conn->query($sql);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citrus Cafe | Manage Menu</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin-style.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <li class="active"><i class="fas fa-mug-hot"></i>Menu (CRUD)</li>
            </a>
            <a href="admin_orders.php">
                <li><i class="fas fa-receipt"></i>Orders</li>
            </a>
            <a href="../logout.php">
                <li class="logout-link"><i class="fas fa-right-from-bracket"></i>Logout</li>
            </a>
        </ul>
    </div>

    <!-- MAIN -->
    <div class="main-content">
        <div class="top-header">
            <div>
                <h1>Manage Menu</h1>
                <p>Quick overview of products, forms, and current menu list.</p>
            </div>

            <form method="GET" action="admin_products.php" class="search-box">
                <i class="fas fa-magnifying-glass"></i>
                <input type="text" name="search" placeholder="Search menu..." value="<?php echo htmlspecialchars($search); ?>">
            </form>
        </div>

        <hr class="top-line">

        <div class="content-grid">

            <!-- LEFT FORM -->
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-circle-plus"></i>
                    <span><?php echo $editMode ? "Edit Beverage" : "Add Beverage"; ?></span>
                </div>

                <form action="admin_products.php" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="product_id" value="<?php echo $editMode ? $editProduct['product_id'] : ''; ?>">

                    <div class="form-group">
                        <label>Drink Name</label>
                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="<?php echo $editMode ? htmlspecialchars($editProduct['name']) : ''; ?>"
                            placeholder="e.g. Honey Lemon"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea
                            name="description"
                            class="form-control"
                            placeholder="e.g. Refreshing cold drink"
                            required><?php echo $editMode ? htmlspecialchars($editProduct['description']) : ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Price (RM)</label>
                        <input
                            type="number"
                            step="0.01"
                            name="price"
                            class="form-control"
                            value="<?php echo $editMode ? $editProduct['price'] : ''; ?>"
                            placeholder="0.00"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Stock Quantity</label>
                        <input
                            type="number"
                            name="stock_quantity"
                            class="form-control"
                            value="<?php echo $editMode ? $editProduct['stock_quantity'] : ''; ?>"
                            placeholder="0"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="Available" <?php if ($editMode && $editProduct['status'] == "Available") echo "selected"; ?>>Available</option>
                            <option value="Sold Out" <?php if ($editMode && $editProduct['status'] == "Sold Out") echo "selected"; ?>>Sold Out</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>
                            Product Image
                            <?php echo $editMode ? "(Upload new image if want to change)" : ""; ?>
                        </label>
                        <input type="file" name="image" class="file-input" accept="image/jpeg,image/png,image/webp" <?php echo $editMode ? "" : "required"; ?>>
                    </div>

                    <?php if ($editMode && !empty($editProduct['image'])) { ?>
                        <div class="form-group">
                            <label>Current Image</label><br>
                            <img
                                src="../img/<?php echo htmlspecialchars($editProduct['image']); ?>"
                                alt="Current Product"
                                style="width:90px; height:90px; object-fit:cover; border-radius:10px;">
                        </div>
                    <?php } ?>

                    <button type="submit" class="save-btn">
                        <?php echo $editMode ? "Update Product" : "Save to Menu"; ?>
                    </button>

                    <?php if ($editMode) { ?>
                        <a href="admin_products.php"
                            class="btn-secondary"
                            style="display:block; text-align:center; text-decoration:none; padding:14px; margin-top:10px; border-radius:8px;">
                            Cancel Edit
                        </a>
                    <?php } ?>
                </form>
            </div>

            <!-- RIGHT TABLE -->
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-list-ul"></i>
                    <span>Current Menu</span>
                </div>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if ($result && $result->num_rows > 0) { ?>
                                <?php while ($row = $result->fetch_assoc()) { ?>
                                    <tr>
                                        <td>#<?php echo $row['product_id']; ?></td>
                                        <td>
                                            <img src="../img/<?php echo htmlspecialchars($row['image']); ?>" class="product-img" alt="Product Image">
                                        </td>
                                        <td>
                                            <div class="product-name"><?php echo htmlspecialchars($row['name']); ?></div>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                                        <td>
                                            <div class="price">RM <?php echo number_format($row['price'], 2); ?></div>
                                        </td>
                                        <td><?php echo $row['stock_quantity']; ?></td>
                                        <td>
                                            <?php if ($row['status'] == "Available") { ?>
                                                <span class="status-badge available">Available</span>
                                            <?php } else { ?>
                                                <span class="status-badge sold-out">Sold Out</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <div class="action-icons">
                                                <a href="admin_products.php?edit=<?php echo $row['product_id']; ?>">
                                                    <i class="fas fa-pen-to-square edit-icon"></i>
                                                </a>

                                                <a href="#"
                                                    class="delete-btn"
                                                    data-id="<?php echo (int) $row['product_id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($row['name']); ?>">
                                                    <i class="fas fa-trash delete-icon"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="8" style="text-align:center;">No products found.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const deleteButtons = document.querySelectorAll(".delete-btn");

            deleteButtons.forEach(button => {
                button.addEventListener("click", function(e) {
                    e.preventDefault();

                    const deleteId = this.getAttribute("data-id");
                    const productName = this.getAttribute("data-name") || "this product";

                    Swal.fire({
                        title: "Delete Product?",
                        text: "Are you sure you want to delete " + productName + "?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#6c757d",
                        confirmButtonText: "Yes, delete it",
                        cancelButtonText: "Cancel",
                        background: "#fffaf3",
                        color: "#2c1b10"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement("form");
                            form.method = "POST";
                            form.action = "admin_products.php";
                            [["delete_id", deleteId], ["csrf_token", <?php echo json_encode(csrf_token()); ?>]].forEach(([key, value]) => {
                                const input = document.createElement("input");
                                input.type = "hidden";
                                input.name = key;
                                input.value = value;
                                form.appendChild(input);
                            });
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });

            <?php if (isset($_GET['status'])) { ?>
                const status = <?php echo json_encode((string) $_GET['status'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

                if (status === "invalid") {
                    Swal.fire({ icon: "error", title: "Check the form", text: "Enter a name, a valid price, stock and status." });
                }

                if (status === "image_error") {
                    Swal.fire({ icon: "error", title: "Image not accepted", text: "Upload a JPG, PNG or WebP image of up to 5 MB." });
                }

                if (status === "session_error") {
                    Swal.fire({ icon: "error", title: "Session expired", text: "Refresh the page and try again." });
                }

                if (status === "added") {
                    Swal.fire({
                        icon: "success",
                        title: "Success!",
                        text: "Product successfully added!",
                        showConfirmButton: false,
                        timer: 1500
                    });
                }

                if (status === "updated") {
                    Swal.fire({
                        icon: "success",
                        title: "Updated!",
                        text: "Product successfully updated!",
                        showConfirmButton: false,
                        timer: 1500
                    });
                }

                if (status === "deleted") {
                    Swal.fire({
                        icon: "success",
                        title: "Deleted!",
                        text: "Product successfully deleted!",
                        showConfirmButton: false,
                        timer: 1500
                    });
                }

                if (status === "add_error") {
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Failed to add product."
                    });
                }

                if (status === "update_error") {
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Failed to update product."
                    });
                }

                if (status === "delete_error") {
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Failed to delete product."
                    });
                }
            <?php } ?>
        });
    </script>

</body>

</html>