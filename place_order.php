<?php
session_start();
include 'db_connection.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if pharmacy_id is set
if (!isset($_POST['pharmacy_id'])) {
    $_SESSION['error'] = "Pharmacy ID is missing.";
    header("Location: view_cart.php");
    exit;
}

// Get user ID and pharmacy ID
$userId = $_SESSION['user_id'];
$pharmacyID = intval($_POST['pharmacy_id']); // Ensure it's an integer
$orderDate = date("Y-m-d H:i:s");

// Begin transaction
$conn->begin_transaction();

try {
    // Create order
    $stmt = $conn->prepare("INSERT INTO Orders (userid, PharmacyID, OrderDate, Status) VALUES (?, ?, ?, 'Pending')");
    if (!$stmt) {
        throw new Exception("Error preparing order insertion: " . $conn->error);
    }
    $stmt->bind_param("iis", $userId, $pharmacyID, $orderDate);
    $stmt->execute();
    $orderId = $stmt->insert_id;
    $stmt->close();

    // Fetch cart items
    $stmt = $conn->prepare("SELECT ProductID, Quantity FROM Cart WHERE UserID = ?");
    if (!$stmt) {
        throw new Exception("Error preparing cart selection: " . $conn->error);
    }
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $cartItems = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Insert order details and update stock
    foreach ($cartItems as $item) {
        $productId = $item['ProductID'];
        $quantity = $item['Quantity'];

        // Insert into OrderDetails
        $stmt = $conn->prepare("INSERT INTO OrderDetails (OrderID, ProductID, Quantity) VALUES (?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Error preparing order details insertion: " . $conn->error);
        }
        $stmt->bind_param("iii", $orderId, $productId, $quantity);
        $stmt->execute();
        $stmt->close();

        // Update stock
        $stmt = $conn->prepare("UPDATE Products SET StockQuantity = StockQuantity - ? WHERE ProductID = ? AND PharmacyID = ?");
        if (!$stmt) {
            throw new Exception("Error preparing stock update: " . $conn->error);
        }
        $stmt->bind_param("iii", $quantity, $productId, $pharmacyID);
        $stmt->execute();
        $stmt->close();
    }

    // Clear cart
    $stmt = $conn->prepare("DELETE FROM Cart WHERE UserID = ?");
    if (!$stmt) {
        throw new Exception("Error preparing cart clearance: " . $conn->error);
    }
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();

    // Commit transaction
    $conn->commit();

    $_SESSION['message'] = "Order placed successfully!";
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    $_SESSION['error'] = "Error placing order: " . $e->getMessage();
}

header("Location: view_cart.php");
exit;
?>
