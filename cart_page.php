<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: logincustomer.php");
    exit;
}

$userID = $_SESSION['user_id'];

// Fetch cart items
$stmt = $conn->prepare("SELECT cart.*, products.ProductName, products.ProductPrice FROM cart JOIN products ON cart.ProductID = products.ProductID WHERE cart.UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$cartItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$totalAmount = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pharmacyID = $_POST['pharmacy_id'];

    // Insert order
    $orderStmt = $conn->prepare("INSERT INTO orders (UserID, PharmacyID, TotalAmount) VALUES (?, ?, ?)");
    $orderStmt->bind_param("iid", $userID, $pharmacyID, $totalAmount);
    $orderStmt->execute();
    $orderID = $orderStmt->insert_id;
    $orderStmt->close();

    // Insert order items and update stock
    foreach ($cartItems as $item) {
        $productID = $item['ProductID'];
        $quantity = $item['Quantity'];
        $price = $item['ProductPrice'];

        // Insert order item
        $orderItemStmt = $conn->prepare("INSERT INTO order_items (OrderID, ProductID, Quantity, Price) VALUES (?, ?, ?, ?)");
        $orderItemStmt->bind_param("iiid", $orderID, $productID, $quantity, $price);
        $orderItemStmt->execute();
        $orderItemStmt->close();

        // Update product stock
        $stockUpdateStmt = $conn->prepare("UPDATE products SET StockQuantity = StockQuantity - ? WHERE ProductID = ?");
        $stockUpdateStmt->bind_param("ii", $quantity, $productID);
        $stockUpdateStmt->execute();
        $stockUpdateStmt->close();
    }

    // Clear cart
    $clearCartStmt = $conn->prepare("DELETE FROM cart WHERE UserID = ?");
    $clearCartStmt->bind_param("i", $userID);
    $clearCartStmt->execute();
    $clearCartStmt->close();

    header("Location: order_confirmation.php?order_id=" . $orderID);
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Your Cart</h2>
    <?php if (!empty($cartItems)): ?>
        <form method="POST" action="">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <?php $itemTotal = $item['ProductPrice'] * $item['Quantity']; ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['ProductName']); ?></td>
                            <td>$<?php echo number_format($item['ProductPrice'], 2); ?></td>
                            <td><?php echo $item['Quantity']; ?></td>
                            <td>$<?php echo number_format($itemTotal, 2); ?></td>
                        </tr>
                        <?php $totalAmount += $itemTotal; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h3>Total: $<?php echo number_format($totalAmount, 2); ?></h3>
            <input type="hidden" name="pharmacy_id" value="<?php echo htmlspecialchars($pharmacyID); ?>">
            <button type="submit" class="btn btn-success">Place Order</button>
        </form>
    <?php else: ?>
        <div class="alert alert-info">Your cart is empty.</div>
    <?php endif; ?>
    <a href="branch_page.php?id=<?php echo htmlspecialchars($pharmacyID); ?>" class="btn btn-primary mt-3">Back to Products</a>
</div>
</body>
</html>
