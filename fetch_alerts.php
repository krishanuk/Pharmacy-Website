<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['pharmacist_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$pharmacyID = $_SESSION['pharmacy_id'];

// Fetch pending alerts for the pharmacist
$query = "
    SELECT 
        ea.AlertID, 
        p.ProductName, 
        i.BatchNumber, 
        i.Quantity, 
        i.MinimumStock, 
        ea.AlertMessage, 
        ea.CreatedAt
    FROM 
        expiry_alerts ea
    JOIN 
        inventory i ON ea.InventoryID = i.InventoryID
    JOIN 
        products p ON i.ProductID = p.ProductID
    WHERE 
        i.PharmacyID = ? AND ea.AlertStatus = 'pending'";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $pharmacyID);
$stmt->execute();
$result = $stmt->get_result();

$alerts = [];
while ($row = $result->fetch_assoc()) {
    $alerts[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($alerts);
?>
