<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productID = intval($_POST['product_id']);
    $inventoryID = intval($_POST['inventory_id']);  
    $batchNumber = $_POST['batch_number'];  
    $quantityToBuy = intval($_POST['quantity']);

    // Fetch the current batch's stock based 
    $fetchBatchStmt = $conn->prepare("
        SELECT Quantity 
        FROM inventory 
        WHERE InventoryID = ? AND ProductID = ? AND BatchNumber = ? AND IsAvailable = 1");
    $fetchBatchStmt->bind_param("iis", $inventoryID, $productID, $batchNumber);
    $fetchBatchStmt->execute();
    $batchResult = $fetchBatchStmt->get_result()->fetch_assoc();

    if (!$batchResult || $batchResult['Quantity'] < $quantityToBuy) {

        echo "<p>Not enough stock available in this batch!</p>";
        exit;
    }

    // Reduce the stock of the current batch
    $newBatchStock = $batchResult['Quantity'] - $quantityToBuy;

    // Update the stock for the current batch and mark it unavailable if the quantity reaches 0
    $updateBatchStmt = $conn->prepare("
        UPDATE inventory 
        SET Quantity = ?, IsAvailable = IF(Quantity = 0, 0, 1) 
        WHERE InventoryID = ? AND ProductID = ? AND BatchNumber = ?");
    $updateBatchStmt->bind_param("iiis", $newBatchStock, $inventoryID, $productID, $batchNumber);
    $updateBatchStmt->execute();
    $updateBatchStmt->close();

    // Ensure the current batch's stock is properly updated
    if ($newBatchStock >= 0) {
        echo "<p>Product added to cart successfully! Remaining stock: " . $newBatchStock . "</p>";
    } else {
        echo "<p>Failed to reduce stock! Please try again.</p>";
    }


    if ($newBatchStock == 0) {
        echo "<p>The current batch is exhausted. The next batch will now be displayed.</p>";
    }

} else {
    echo "<p>Invalid request method.</p>";
}
