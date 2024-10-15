<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['pharmacist_id'])) {
    echo "Unauthorized access";
    exit;
}

$pharmacyID = $_SESSION['pharmacy_id'];

if (isset($_GET['product_id']) && isset($_GET['inventory_id'])) {
    $productId = intval($_GET['product_id']);
    $inventoryId = intval($_GET['inventory_id']);

    // Fetch product details
    $stmt = $conn->prepare("SELECT * FROM products WHERE ProductID = ? AND PharmacyID = ?");
    $stmt->bind_param("ii", $productId, $pharmacyID);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if (!$product) {
        echo "Product not found.";
        exit;
    }

    // Fetch inventory details
    $batchStmt = $conn->prepare("SELECT * FROM inventory WHERE InventoryID = ? AND PharmacyID = ?");
    $batchStmt->bind_param("ii", $inventoryId, $pharmacyID);
    $batchStmt->execute();
    $batch = $batchStmt->get_result()->fetch_assoc();

    if (!$batch) {
        echo "Batch information not found.";
        exit;
    }

    // Set default values if 'IsAvailable'
    $isAvailable = isset($product['IsAvailable']) ? $product['IsAvailable'] : 0;
    ?>

    <!-- Modal Content -->
    <div class="modal-header">
        <h5 class="modal-title">Update Product - <?= htmlspecialchars($product['ProductName']) ?></h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    <div class="modal-body">
        <form method="POST" action="update_product.php" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="<?= htmlspecialchars($productId) ?>">
            <input type="hidden" name="inventory_id" value="<?= htmlspecialchars($inventoryId) ?>">

            <!-- Product Details -->
            <div class="form-group">
                <label for="product_name">Product Name:</label>
                <input type="text" class="form-control" id="product_name" name="product_name" value="<?= htmlspecialchars($product['ProductName']) ?>" required>
            </div>

            <div class="form-group">
                <label for="product_price">Product Price:</label>
                <input type="number" class="form-control" id="product_price" name="product_price" step="0.01" value="<?= htmlspecialchars($product['ProductPrice']) ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control" id="description" name="description" required><?= htmlspecialchars($product['Description']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="brand_name">Brand Name:</label>
                <input type="text" class="form-control" id="brand_name" name="brand_name" value="<?= htmlspecialchars($product['BrandName']) ?>" required>
            </div>

            <div class="form-group">
                <label for="category">Category:</label>
                <input type="text" class="form-control" id="category" name="category" value="<?= htmlspecialchars($product['Category']) ?>" required>
            </div>

            <div class="form-group">
                <label for="dosage">Dosage:</label>
                <input type="text" class="form-control" id="dosage" name="dosage" value="<?= htmlspecialchars($product['Dosage']) ?>" required>
            </div>

            <div class="form-group">
                <label for="minimum_stock">Minimum Stock:</label>
                <input type="number" class="form-control" id="minimum_stock" name="minimum_stock" value="<?= htmlspecialchars($product['MinimumStock']) ?>" required>
            </div>

            <div class="form-group">
                <label for="is_available">Is Available:</label>
                <select class="form-control" id="is_available" name="is_available" required>
                    <option value="1" <?= $isAvailable == 1 ? 'selected' : '' ?>>Yes</option>
                    <option value="0" <?= $isAvailable == 0 ? 'selected' : '' ?>>No</option>
                </select>
            </div>

            <div class="form-group">
                <label for="image">Product Image:</label>
                <input type="file" class="form-control-file" id="image" name="image" accept=".jpg, .jpeg, .png">
                <small>Leave empty if you don't want to change the image.</small>
            </div>

            <!-- Inventory Details -->
            <h3>Batch Information</h3>

            <div class="form-group">
                <label for="batch_number">Batch Number:</label>
                <input type="text" class="form-control" id="batch_number" name="batch_number" value="<?= htmlspecialchars($batch['BatchNumber']) ?>" required>
            </div>

            <div class="form-group">
                <label for="expiry_date">Expiry Date:</label>
                <input type="date" class="form-control" id="expiry_date" name="expiry_date" value="<?= htmlspecialchars($batch['ExpiryDate']) ?>" required>
            </div>

            <div class="form-group">
                <label for="measurement_unit">Measurement Unit:</label>
                <input type="text" class="form-control" id="measurement_unit" name="measurement_unit" value="<?= htmlspecialchars($batch['MeasurementUnit']) ?>" required>
            </div>

            <div class="form-group">
                <label for="stock_quantity">Stock Quantity:</label>
                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" value="<?= htmlspecialchars($batch['Quantity']) ?>" required>
            </div>

            <div class="form-group">
                <label for="unit_price">Unit Price:</label>
                <input type="number" class="form-control" id="unit_price" name="unit_price" step="0.01" value="<?= htmlspecialchars($batch['UnitPrice']) ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Product</button>
        </form>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    </div>

    <?php
} else {
    echo "Invalid product or inventory ID.";
}
?>
