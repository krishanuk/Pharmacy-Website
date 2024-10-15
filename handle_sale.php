<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['pharmacist_id'])) {
    header("Location: logininvent.php");
    exit;
}

$pharmacyID = $_SESSION['pharmacy_id'];

function handleSale($productId, $quantitySold) {
    global $conn, $pharmacyID;

    try {
        $conn->begin_transaction();

       
        $stmt = $conn->prepare("SELECT StockQuantity, MinimumStock FROM Products WHERE ProductID = ? AND PharmacyID = ?");
        $stmt->bind_param("ii", $productId, $pharmacyID);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();

        if (!$product) {
            throw new Exception("Product not found in this pharmacy's inventory.");
        }

        if ($product['StockQuantity'] < $quantitySold) {
            throw new Exception("Insufficient stock to complete the sale. Available stock: " . $product['StockQuantity']);
        }

       
        $stmt = $conn->prepare("UPDATE Products SET StockQuantity = StockQuantity - ? WHERE ProductID = ? AND PharmacyID = ?");
        $stmt->bind_param("iii", $quantitySold, $productId, $pharmacyID);
        $stmt->execute();

        
        $stmt = $conn->prepare("INSERT INTO StockMovements (ProductID, PharmacyID, Quantity, MovementType) VALUES (?, ?, ?, 'OUT')");
        $stmt->bind_param("iii", $productId, $pharmacyID, $quantitySold);
        $stmt->execute();

      
        $newStockQuantity = $product['StockQuantity'] - $quantitySold;
        if ($newStockQuantity < $product['MinimumStock']) {
            $stmt = $conn->prepare("INSERT INTO LowStockAlerts (ProductID, PharmacyID, CurrentStock, Threshold) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiii", $productId, $pharmacyID, $newStockQuantity, $product['MinimumStock']);
            $stmt->execute();
        }

        $conn->commit();
        echo "Sale processed successfully. Remaining stock: " . $newStockQuantity;
    } catch (Exception $e) {
        $conn->rollback();
        echo "Failed to handle sale: " . $e->getMessage();
    }
}



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productId = $_POST['product_id'];
    $quantitySold = $_POST['quantity_sold'];

    if ($quantitySold > 0) {
        handleSale($productId, $quantitySold);
    } else {
        echo "Quantity sold must be greater than zero.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Handle Sale</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Handle Sale</h2>

    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($error)): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="product_id">Product ID:</label>
            <input type="number" class="form-control" id="product_id" name="product_id" required>
        </div>
        <div class="form-group">
            <label for="quantity_sold">Quantity Sold:</label>
            <input type="number" class="form-control" id="quantity_sold" name="quantity_sold" required min="1">
        </div>
        <button type="submit" class="btn btn-primary">Submit Sale</button>
    </form>
</div>
</body>
</html>
