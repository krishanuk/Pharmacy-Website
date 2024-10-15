<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['pharmacist_id'])) {
    header("Location: loginorderp.php");
    exit;
}

$pharmacyID = $_SESSION['pharmacy_id'];

// Fetch orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE PharmacyID = ? AND OrderStatus = 'PENDING'");
$stmt->bind_param("i", $pharmacyID);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacist Orders</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Pending Orders</h2>
    <?php if (!empty($orders)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Total Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['OrderID']); ?></td>
                        <td><?php echo htmlspecialchars($order['UserID']); ?></td>
                        <td>$<?php echo number_format($order['TotalAmount'], 2); ?></td>
                        <td>
                            <a href="process_order.php?order_id=<?php echo htmlspecialchars($order['OrderID']); ?>&action=confirm" class="btn btn-success">Confirm</a>
                            <a href="process_order.php?order_id=<?php echo htmlspecialchars($order['OrderID']); ?>&action=cancel" class="btn btn-danger">Cancel</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No pending orders at this time.</div>
    <?php endif; ?>
    <a href="logout.php" class="btn btn-primary mt-3">Logout</a>
</div>
</body>
</html>
