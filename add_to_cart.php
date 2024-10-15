<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productID = intval($_POST['product_id']);
    $inventoryID = intval($_POST['inventory_id']);
    $quantity = intval($_POST['quantity']);
    $userID = $_SESSION['user_id'];

    if (!isset($_POST['pharmacy_id']) || empty($_POST['pharmacy_id'])) {
        die("Pharmacy ID is missing from the request.");
    }

    $pharmacyID = intval($_POST['pharmacy_id']);

    // Check if the user already has a cart for this pharmacy
    $stmt = $conn->prepare("SELECT * FROM Cart WHERE UserID = ? AND PharmacyID = ? AND InventoryID = ?");
    $stmt->bind_param("iii", $userID, $pharmacyID, $inventoryID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Cart exists, update the quantity of the existing item
        $stmt = $conn->prepare("UPDATE Cart SET Quantity = Quantity + ? WHERE UserID = ? AND PharmacyID = ? AND InventoryID = ?");
        $stmt->bind_param("iiii", $quantity, $userID, $pharmacyID, $inventoryID);
    } else {
        // Cart for this pharmacy does not exist, create a new cart entry
        $stmt = $conn->prepare("INSERT INTO Cart (UserID, ProductID, InventoryID, PharmacyID, Quantity) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiii", $userID, $productID, $inventoryID, $pharmacyID, $quantity);
    }

    if ($stmt->execute()) {
        // Redirect back to the branch_page.php after adding the item
        header("Location: branch_page.php?id=$pharmacyID&success=1");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
    $conn->close();
}
