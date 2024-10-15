<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['pharmacist_id'])) {
    header("Location: logininvent.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = intval($_POST['product_id']);
    $inventoryId = intval($_POST['inventory_id']);

    // Delete inventory
    $stmt = $conn->prepare("DELETE FROM inventory WHERE InventoryID = ?");
    $stmt->bind_param("i", $inventoryId);
    $stmt->execute();

    // Delete product record
    $productStmt = $conn->prepare("DELETE FROM products WHERE ProductID = ?");
    $productStmt->bind_param("i", $productId);
    $productStmt->execute();

    // Redirect after deletion
    header("Location: manage_products.php");
    exit;
}
?>
