<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['pharmacist_id'])) {
    header("Location: login.php");
    exit;
}

$pharmacyID = $_SESSION['pharmacy_id'];

// Fetch products and inventory details
$query = "
    SELECT 
        p.ProductID, 
        p.ProductName, 
        p.Image, 
        p.Description, 
        p.BrandName, 
        p.Category, 
        p.Dosage, 
        i.InventoryID, 
        i.BatchNumber, 
        i.Quantity AS StockQuantity, 
        i.UnitPrice,  -- Fetching UnitPrice from the inventory table
        i.ExpiryDate, 
        i.MeasurementUnit, 
        i.IsAvailable, 
        i.MinimumStock  -- Fetching MinimumStock from the inventory table
    FROM 
        products p 
    LEFT JOIN 
        inventory i 
    ON 
        p.ProductID = i.ProductID 
    WHERE 
        p.PharmacyID = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $pharmacyID);
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Manage Products</h2>

    <a href="add_product.php" class="btn btn-primary mb-4">Add New Product</a>

    <h3>Existing Products</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Inventory ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Unit Price</th>  
                <th>Stock Quantity</th>
                <th>Batch Number</th>
                <th>Expiry Date</th>
                <th>Measurement Unit</th>
                <th>Minimum Stock</th>  
                <th>Brand</th>
                <th>Category</th>
                <th>Dosage</th>
                <th>Is Available</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['ProductID']) ?></td>
                    <td><?= htmlspecialchars($product['InventoryID']) ?></td>
                    <td>
                        <!-- Display product image -->
                        <?php if (!empty($product['Image'])): ?>
                            <img src="../images/<?= htmlspecialchars($product['Image']) ?>" alt="<?= htmlspecialchars($product['ProductName']) ?>" width="50" height="50">
                        <?php else: ?>
                            <img src="../images/default.png" alt="No Image" width="50" height="50">
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($product['ProductName']) ?></td>
                    <td>$<?= number_format($product['UnitPrice'], 2) ?></td>  
                    <td><?= htmlspecialchars($product['StockQuantity']) ?></td>
                    <td><?= htmlspecialchars($product['BatchNumber']) ?></td>
                    <td><?= htmlspecialchars($product['ExpiryDate']) ?></td>
                    <td><?= htmlspecialchars($product['MeasurementUnit']) ?></td>
                    <td><?= htmlspecialchars($product['MinimumStock']) ?></td>  
                    <td><?= htmlspecialchars($product['BrandName']) ?></td>
                    <td><?= htmlspecialchars($product['Category']) ?></td>
                    <td><?= htmlspecialchars($product['Dosage']) ?></td>
                    <td><?= $product['IsAvailable'] ? 'Yes' : 'No' ?></td>
                    <td>
                        <!-- Update button -->
                        <button class="btn btn-info update-product-btn" data-id="<?= $product['ProductID'] ?>" data-inventory-id="<?= $product['InventoryID'] ?>" data-toggle="modal" data-target="#updateProductModal">Update</button>
                        
                        <!-- Delete button-->
                        <form method="POST" action="delete_product.php" class="d-inline-block">
                            <input type="hidden" name="product_id" value="<?= $product['ProductID'] ?>">
                            <input type="hidden" name="inventory_id" value="<?= $product['InventoryID'] ?>">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Update Product Modal -->
<div class="modal fade" id="updateProductModal" tabindex="-1" role="dialog" aria-labelledby="updateProductModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content"></div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // Handle update button click
    $(document).on('click', '.update-product-btn', function() {
        var productId = $(this).data('id');
        var inventoryId = $(this).data('inventory-id');
        
        // AJAX request to load the product details into the modal
        $.ajax({
            url: 'get_product_details.php',
            method: 'GET',
            data: { product_id: productId, inventory_id: inventoryId },
            success: function(response) {
                $('#updateProductModal .modal-content').html(response);
            },
            error: function() {
                alert('Error loading product details');
            }
        });
    });
});
</script>
</body>
</html>
