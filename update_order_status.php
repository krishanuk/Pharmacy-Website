<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['pharmacist_id'])) {
    header("Location: logininvent.php");
    exit;
}

$orderID = $_POST['order_id'];
$status = $_POST['status'];

// Update order status
$stmt = $conn->prepare("UPDATE Orders SET Status = ? WHERE OrderID = ?");
$stmt->bind_param("si", $status, $orderID);
$stmt->execute();
$stmt->close();

$_SESSION['message'] = "Order status updated successfully!";
header("Location: order_details.php?id=" . $orderID);
exit;
