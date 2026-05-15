<?php
include 'auth.php'; // session + $conn


// Enable MySQLi error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Start session for flash messages if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$message = '';
$action = $_POST['action'] ?? '';

// Handle Add, Edit, Delete actions
if ($action === 'add' || $action === 'edit') {
    $id = intval($_POST['id'] ?? 0);
    $brand_name = trim($_POST['brand_name'] ?? '');
    $product_name = trim($_POST['product_name'] ?? '');
    $original_price = floatval($_POST['original_price'] ?? 0);
    $discount_price = floatval($_POST['discount_price'] ?? 0);
    $discount_percent = floatval($_POST['discount_percent'] ?? 0);

    // Handle image upload
    $image_name = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['image']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        // Validate image extension
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($ext, $allowed_ext)) {
            $_SESSION['toast_message'] = "Invalid image file type.";
            $_SESSION['toast_type'] = "danger";
            header('Location: admin.php?data-page=product.php');
            exit;
        } else {
            $image_name = uniqid('img_') . '.' . $ext;
            move_uploaded_file($tmp_name, __DIR__ . "/product-images/$image_name");
        }
    }

    if ($action === 'add') {
        $stmt = $conn->prepare("INSERT INTO products (brand_name, product_name, original_price, discount_price, discount_percent, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssddds", $brand_name, $product_name, $original_price, $discount_price, $discount_percent, $image_name);

        if ($stmt->execute()) {
            $_SESSION['toast_message'] = "Product added successfully.";
            $_SESSION['toast_type'] = "success";
        } else {
            $_SESSION['toast_message'] = "Error adding product: " . $stmt->error;
            $_SESSION['toast_type'] = "danger";
        }
        $stmt->close();

    } elseif ($action === 'edit' && $id > 0) {
        // Keep old image if no new image uploaded
        if (!$image_name) {
            $result = $conn->query("SELECT image FROM products WHERE id = $id");
            $row = $result->fetch_assoc();
            $image_name = $row['image'] ?? null;
        }

        $stmt = $conn->prepare("UPDATE products SET brand_name=?, product_name=?, original_price=?, discount_price=?, discount_percent=?, image=? WHERE id=?");
        $stmt->bind_param("ssdddsi", $brand_name, $product_name, $original_price, $discount_price, $discount_percent, $image_name, $id);

        if ($stmt->execute()) {
            $_SESSION['toast_message'] = "Product updated successfully.";
            $_SESSION['toast_type'] = "success";
        } else {
            $_SESSION['toast_message'] = "Error updating product: " . $stmt->error;
            $_SESSION['toast_type'] = "danger";
        }
        $stmt->close();
    }

    header('Location: admin.php?data-page=product.php');
    exit;

} elseif ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    if ($id > 0) {
        // Get product_name and image before deletion
        $result = $conn->query("SELECT product_name, image FROM products WHERE id = $id");
        $row = $result->fetch_assoc();
        if ($row) {
            $product_name = $conn->real_escape_string($row['product_name']); // escape for safety

            // Delete image file if exists
            if ($row['image']) {
                $img_path = __DIR__ . "/product-images/" . $row['image'];
                if (file_exists($img_path)) unlink($img_path);
            }

            // Delete product from products table
            $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                // Delete related cart items by product_name
                $stmtCart = $conn->prepare("DELETE FROM carttt WHERE product_name = ?");
                $stmtCart->bind_param("s", $product_name);
                $stmtCart->execute();
                $stmtCart->close();

                $_SESSION['toast_message'] = "Product deleted successfully.";
                $_SESSION['toast_type'] = "success";
            } else {
                $_SESSION['toast_message'] = "Error deleting product: " . $stmt->error;
                $_SESSION['toast_type'] = "danger";
            }
            $stmt->close();
        } else {
            $_SESSION['toast_message'] = "Product not found.";
            $_SESSION['toast_type'] = "warning";
        }
    }
    header('Location: admin.php?data-page=product.php');
    exit;
}

// Fetch all products for display
$products = [];
$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Toast variables
$toast_message = $_SESSION['toast_message'] ?? "";
$toast_type = $_SESSION['toast_type'] ?? "info";
if ($toast_message) {
    unset($_SESSION['toast_message'], $_SESSION['toast_type']);
}
?>

<style>
    img.product-img { max-width: 80px; max-height: 80px; }
</style>

<!-- Toast Message -->
<div id="adminToast" class="position-fixed start-50 translate-middle-x mt-4 <?php echo $toast_message ? '' : 'd-none'; ?>" style="top: 20px; z-index: 9999; min-width: 300px;">
    <div id="toastAlert" class="alert alert-<?php echo htmlspecialchars($toast_type); ?> text-center fade show" role="alert">
        <strong id="toastMessage"><?php echo htmlspecialchars($toast_message); ?></strong>
    </div>
</div>

<body class="p-5 bg-light">
<div class="card p-5 shadow mb-4">
<div id="products-list" class="container">
    <h3 class="text-center mb-4"><i class="bi bi-box-seam-fill text-success"></i> Products Management</h3>

    <div class="text-center">
    <button type="button" class="btn btn-success mb-4" data-bs-toggle="modal" data-bs-target="#addProductModal">
        Add New Product
    </button>
</div>


    <!-- Products Table -->
    <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Existing Products</h4>
    <span class="col-md-6 text-end"> <b>Total Products: <?= count($products) ?> </b></span>
</div>

    <table class="table table-bordered table-striped align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Brand</th>
                <th>Product</th>
                <th>Original Price</th>
                <th>Discount Price</th>
                <th>Image</th>
                <th>Edit/Delete</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($products as $prod): ?>
            <tr>
                <td><?= $prod['id'] ?></td>
                <td><?= htmlspecialchars($prod['brand_name']) ?></td>
                <td><?= htmlspecialchars($prod['product_name']) ?></td>
                <td><?= number_format($prod['original_price'], 2) ?></td>
                <td><?= number_format($prod['discount_price'], 2) ?></td>
                <td>
                    <?php if ($prod['image'] && file_exists(__DIR__ . "/product-images/" . $prod['image'])): ?>
                        <img src="product-images/<?= htmlspecialchars($prod['image']) ?>" class="product-img" alt="product image" />
                    <?php else: ?>
                        <em>No image</em>
                    <?php endif; ?>
                </td>
                <td>
				<div class="d-flex flex-column gap-2">
                    <!-- Edit button triggers modal -->
                    <button class="btn btn-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#editModal<?= $prod['id'] ?>">Edit</button>

                    <!-- Delete form -->
                     <form action="products.php" method="post" onsubmit="return confirm('Delete this product?');">
            <input type="hidden" name="action" value="delete" />
            <input type="hidden" name="id" value="<?= $prod['id'] ?>" />
            <button type="submit" class="btn btn-danger btn-sm w-100">Delete</button>
        </form>
		</div>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?= $prod['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $prod['id'] ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form action="products.php" method="post" enctype="multipart/form-data">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel<?= $prod['id'] ?>">Edit Product #<?= $prod['id'] ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
										<input type="hidden" name="action" value="edit" />
										<input type="hidden" name="id" value="<?= $prod['id'] ?>" />

										<div class="row">
											<div class="mb-3 col-md-6">
												<label>Brand Name</label>
												<input type="text" name="brand_name" class="form-control" required value="<?= htmlspecialchars($prod['brand_name']) ?>">
											</div>
											<div class="mb-3 col-md-6">
												<label>Product Name</label>
												<input type="text" name="product_name" class="form-control" required value="<?= htmlspecialchars($prod['product_name']) ?>">
											</div>
										</div>

										<div class="row">
											<div class="mb-3 col-md-6">
												<label>Original Price</label>
												<input type="number" step="0.01" name="original_price" class="form-control" required value="<?= $prod['original_price'] ?>">
											</div>
											<div class="mb-3 col-md-6">
												<label>Discount Price</label>
												<input type="number" step="0.01" name="discount_price" class="form-control" required value="<?= $prod['discount_price'] ?>">
											</div>
										</div>

										<div class="row">
											<div class="mb-3 col-md-6">
												<label>Discount Percent</label>
								<input type="number" step="0.01" name="discount_percent" class="form-control" disabled  placeholder="auto"> 
											</div>
											<div class="mb-3 col-md-6">
												<label>Image (upload to replace current)</label>
												<input type="file" name="image" accept="image/*" class="form-control" />
											</div>
										</div>

										<?php if ($prod['image'] && file_exists(__DIR__ . "/product-images/" . $prod['image'])): ?>
											<div class="mb-3">
												<label>Current Image:</label><br>
												<img src="product-images/<?= htmlspecialchars($prod['image']) ?>" class="product-img" alt="current image" />
											</div>
										<?php endif; ?>
									</div>

                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="products.php" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProductLabel">Add New Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
						<input type="hidden" name="action" value="add" />
						
						<div class="row">
							<div class="mb-3 col-md-6">
								<label>Brand Name</label>
								<input type="text" name="brand_name" class="form-control" required>
							</div>
							<div class="mb-3 col-md-6">
								<label>Product Name</label>
								<input type="text" name="product_name" class="form-control" required>
							</div>
						</div>

						<div class="row">
							<div class="mb-3 col-md-6">
								<label>Original Price</label>
								<input type="number" step="0.01" name="original_price" class="form-control" required>
							</div>
							<div class="mb-3 col-md-6">
								<label>Discount Price</label>
								<input type="number" step="0.01" name="discount_price" class="form-control">
							</div>
						</div>

						<div class="row">
							<div class="mb-3 col-md-6">
								<label>Discount Percent</label>
								<input type="number" step="0.01" name="discount_percent" class="form-control" disabled  placeholder="auto"> 
							</div>
							<div class="mb-3 col-md-6">
								<label>Product Image</label>
								<input type="file" name="image" accept="image/*" class="form-control" required>
							</div>
						</div>
					</div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Add Product</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Bootstrap 5 JS (make sure you load Bootstrap 5 JS and CSS in your admin.php or layout) -->
<script>
    // Hide toast after 3 seconds
    setTimeout(() => {
        const toastEl = document.getElementById('adminToast');
        if (toastEl) {
            toastEl.classList.add('d-none');
        }
    }, 3000);
</script>
</body>