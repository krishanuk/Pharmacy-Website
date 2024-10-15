<?php
session_start();
include 'db_connection.php';

// Check if pharmacist is logged in
if (!isset($_SESSION['pharmacist_id'])) {
    header("Location: logininvent.php");
    exit;
}

$pharmacyID = $_SESSION['pharmacy_id'];


$orderQuery = "SELECT o.OrderID, u.user_name, p.ProductName, o.Quantity, o.OrderDate 
                FROM Orders o 
                JOIN Products p ON o.ProductID = p.ProductID 
                JOIN Users u ON o.ID = u.ID
                WHERE p.PharmacyID = ? 
                ORDER BY o.OrderDate DESC";
$stmt = $conn->prepare($orderQuery);
$stmt->bind_param("i", $pharmacyID);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_order'])) {
    $orderId = $_POST['order_id'];

    try {
        $conn->begin_transaction();

        // Mark order as confirmed
        $stmt = $conn->prepare("UPDATE Orders SET Status = 'Confirmed' WHERE OrderID = ?");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();

        $conn->commit();
        echo "Order confirmed.";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Failed to confirm order: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Manage Orders</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>User Name</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Order Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['OrderID']); ?></td>
                    <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($order['ProductName']); ?></td>
                    <td><?php echo htmlspecialchars($order['Quantity']); ?></td>
                    <td><?php echo htmlspecialchars($order['OrderDate']); ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['OrderID']); ?>">
                            <button type="submit" name="confirm_order" class="btn btn-success">Confirm</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
