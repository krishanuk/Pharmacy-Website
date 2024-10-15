<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productID = intval($_POST['product_id']);
    $quantityToAdd = intval($_POST['quantity']);
    $pharmacyID = intval($_POST['pharmacy_id']);  

    // Fetch all available batches ordered by ExpiryDate
    $query = "SELECT * FROM inventory WHERE ProductID = ? AND PharmacyID = ? AND Quantity > 0 ORDER BY ExpiryDate ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $productID, $pharmacyID);
    $stmt->execute();
    $batches = $stmt->get_result();

    $remainingQuantity = $quantityToAdd;

    if ($batches->num_rows > 0) {
        while ($batch = $batches->fetch_assoc()) {
            $batchID = $batch['InventoryID'];
            $availableQuantity = $batch['Quantity'];

            if ($remainingQuantity <= 0) {
                break; 
            }

            // Determine how much can be taken from this batch
            if ($availableQuantity >= $remainingQuantity) {

                $newQuantity = $availableQuantity - $remainingQuantity;
                $updateStmt = $conn->prepare("UPDATE inventory SET Quantity = ? WHERE InventoryID = ?");
                $updateStmt->bind_param("ii", $newQuantity, $batchID);
                $updateStmt->execute();
                $remainingQuantity = 0;  

                echo "<div class='alert-success'>Added to cart! Stock updated.</div>";
            } else {
                // Reduce the entire stock in this batch and move to the next batch
                $newQuantity = 0; 
                $updateStmt = $conn->prepare("UPDATE inventory SET Quantity = ? WHERE InventoryID = ?");
                $updateStmt->bind_param("ii", $newQuantity, $batchID);
                $updateStmt->execute();
                
                $remainingQuantity -= $availableQuantity;

                echo "<div class='alert-warning'>Batch $batchID depleted. Moving to the next batch.</div>";
            }

            // Mark batch as unavailable if it's fully depleted
            if ($newQuantity == 0) {
                $updateStmt = $conn->prepare("UPDATE inventory SET IsAvailable = 0 WHERE InventoryID = ?");
                $updateStmt->bind_param("i", $batchID);
                $updateStmt->execute();
            }
        }

        if ($remainingQuantity > 0) {
            echo "<div class='alert-danger'>Insufficient stock across all batches. Unable to fulfill the total quantity.</div>";
        }
    } else {
        echo "<div class='alert-danger'>No stock available for this product.</div>";
    }
    
    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add to Cart</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Add Product to Cart</h2>
    
    <!-- Temporary form to simulate adding to cart and reducing stock -->
    <form method="POST" action="process_cart.php">
        <div class="form-group">
            <label for="product_id">Product ID:</label>
            <input type="number" class="form-control" id="product_id" name="product_id" required>
        </div>
        
        <div class="form-group">
            <label for="pharmacy_id">Pharmacy ID:</label>
            <input type="number" class="form-control" id="pharmacy_id" name="pharmacy_id" required>
        </div>

        <div class="form-group">
            <label for="quantity">Quantity to Add:</label>
            <input type="number" class="form-control" id="quantity" name="quantity" required>
        </div>

        <button type="submit" class="btn btn-primary">Add to Cart</button>
    </form>
</div>
</body>
</html>
