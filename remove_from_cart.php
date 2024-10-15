<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cart_id'])) {
    $cart_id = intval($_POST['cart_id']);
    
    $stmt = $conn->prepare("DELETE FROM Cart WHERE CartID = ?");
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $stmt->close();
    header('Location: cart.php');
}
?>
