<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['pharmacist_id'])) {
    header("Location: logininvent.php");
    exit;
}

$pharmacyID = $_SESSION['pharmacy_id'];

function handleRestock($productId, $quantityRestocked) {
    global $conn, $pharmacyID;

    try {
        $conn->begin_transaction();

        // Update stock quantity
        $stmt = $conn->prepare("UPDATE Products SET StockQuantity = StockQuantity + ? WHERE ProductID = ? AND PharmacyID = ?");
        $stmt->bind_param("iii", $quantityRestocked, $productId, $pharmacyID);
        $stmt->execute();

        // Log stock movement
        $stmt = $conn->prepare("INSERT INTO StockMovements (ProductID, PharmacyID, Quantity, MovementType) VALUES (?, ?, ?, 'IN')");
        $stmt->bind_param("iii", $productId, $pharmacyID, $quantityRestocked);
        $stmt->execute();

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "Failed to handle restock: " . $e->getMessage();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productId = $_POST['product_id'];
    $quantityRestocked = $_POST['quantity_restocked'];

    handleRestock($productId, $quantityRestocked);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restock Order</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Restock Order</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="product_id">Product ID:</label>
            <input type="number" class="form-control" id="product_id" name="product_id" required>
        </div>
        <div class="form-group">
            <label for="quantity_restocked">Quantity Restocked:</label>
            <input type="number" class="form-control" id="quantity_restocked" name="quantity_restocked" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit Restock</button>
    </form>
</div>
</body>
</html>
