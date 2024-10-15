<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['pharmacist_id']) || !isset($_GET['order_id']) || !isset($_GET['action'])) {
    header("Location: loginorderp.php");
    exit;
}

$orderID = $_GET['order_id'];
$action = $_GET['action'];

if ($action === 'confirm') {
    $stmt = $conn->prepare("UPDATE orders SET OrderStatus = 'CONFIRMED' WHERE OrderID = ?");
} elseif ($action === 'cancel') {
    $stmt = $conn->prepare("UPDATE orders SET OrderStatus = 'CANCELLED' WHERE OrderID = ?");
}

if ($stmt) {
    $stmt->bind_param("i", $orderID);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

header("Location: pharmacist_orders.php");
exit;
?>
