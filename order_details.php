<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['pharmacist_id'])) {
    header("Location: logininvent.php");
    exit;
}

$orderID = isset($_GET['id']) ? intval($_GET['id']) : 0; // Ensure the order ID is an integer

// Check if orderID is valid
if ($orderID <= 0) {
    die("<div class='alert alert-danger'>Invalid order ID.</div>");
}

// Prepare and execute the SQL query
$query = "SELECT o.OrderID, o.OrderDate, o.Status, u.username, p.ProductName, od.Quantity 
          FROM OrderDetails od 
          JOIN Orders o ON od.OrderID = o.OrderID 
          JOIN Products p ON od.ProductID = p.ProductID 
          JOIN users u ON o.userid = u.user_id 
          WHERE o.OrderID = ?";

if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $orderID);
    $stmt->execute();
    $result = $stmt->get_result();
    $orderDetails = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    die("<div class='alert alert-danger'>Error preparing statement: " . $conn->error . "</div>");
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Order Details</h2>
    <?php if (empty($orderDetails)): ?>
        <div class="alert alert-info">No details available for this order.</div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderDetails as $detail): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($detail['OrderID']); ?></td>
                        <td><?php echo htmlspecialchars($detail['OrderDate']); ?></td>
                        <td><?php echo htmlspecialchars($detail['username']); ?></td>
                        <td><?php echo htmlspecialchars($detail['ProductName']); ?></td>
                        <td><?php echo htmlspecialchars($detail['Quantity']); ?></td>
                        <td>
                            <form method="post" action="update_order_status.php">
                                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($detail['OrderID']); ?>">
                                <select name="status" class="form-control" style="width: 200px;">
                                    <option value="Pending" <?php echo $detail['Status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Shipped" <?php echo $detail['Status'] === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="Delivered" <?php echo $detail['Status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="Cancelled" <?php echo $detail['Status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm mt-2">Update Status</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <a href="manage_orders.php" class="btn btn-secondary">Back to Orders</a>
</div>
</body>
</html>
