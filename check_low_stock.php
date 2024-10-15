<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['pharmacist_id'])) {
    header("Location: logininvent.php");
    exit;
}

$pharmacyID = $_SESSION['pharmacy_id'];

// Check for low stock in the inventory
$query = "
    SELECT 
        i.InventoryID, 
        i.ProductID, 
        i.Quantity, 
        i.MinimumStock, 
        i.ExpiryDate, 
        p.ProductName
    FROM 
        inventory i
    JOIN 
        products p ON i.ProductID = p.ProductID
    WHERE 
        i.PharmacyID = ? AND i.Quantity < i.MinimumStock AND i.IsAvailable = 1";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $pharmacyID);
$stmt->execute();
$result = $stmt->get_result();

// Iterate through the results and create alerts for products below minimum stock
while ($row = $result->fetch_assoc()) {
    $inventoryID = $row['InventoryID'];
    $productName = $row['ProductName'];
    $quantity = $row['Quantity'];
    $minimumStock = $row['MinimumStock'];
    $expiryDate = $row['ExpiryDate'];
    
    // Check if an alert already exists for this inventory
    $checkAlertQuery = "SELECT * FROM expiry_alerts WHERE InventoryID = ? AND AlertStatus = 'pending'";
    $checkAlertStmt = $conn->prepare($checkAlertQuery);
    $checkAlertStmt->bind_param("i", $inventoryID);
    $checkAlertStmt->execute();
    $alertResult = $checkAlertStmt->get_result();

    if ($alertResult->num_rows == 0) {
        // Insert a new alert if not already present
        $insertAlertQuery = "
            INSERT INTO expiry_alerts (InventoryID, ExpiryDate, AlertStatus)
            VALUES (?, ?, 'pending')";
        $insertAlertStmt = $conn->prepare($insertAlertQuery);
        $insertAlertStmt->bind_param("is", $inventoryID, $expiryDate);
        $insertAlertStmt->execute();
        $insertAlertStmt->close();

        // You can trigger an email or notification system here
        echo "Low stock alert created for product: $productName. Current quantity: $quantity. Please restock.<br>";
    }
}

$stmt->close();
$conn->close();
?>
