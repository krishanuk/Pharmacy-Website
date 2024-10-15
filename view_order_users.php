<?php
session_start();
$connection = mysqli_connect('localhost', 'root', '', 'pharmacy_db');

if (!$connection) {
    die('Connection failed: ' . mysqli_connect_error());
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit(); 
}

$user_id = $_SESSION['user_id'];
$message = [];


$query = "SELECT orders.OrderID, orders.OrderDate, orders.orderStatus, orders.TotalAmount, orderdetails.ProductID, 
                orderdetails.ProductQuantity, products.ProductName
          FROM orders
          JOIN orderdetails ON orders.OrderID = orderdetails.OrderID
          JOIN products ON orderdetails.ProductID = products.ProductID
          WHERE orders.UserID = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the user has orders
if ($result->num_rows > 0) {
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        if (!isset($orders[$row['OrderID']])) {
            
            $orders[$row['OrderID']] = [
                'OrderDate' => $row['OrderDate'],
                'orderStatus' => $row['orderStatus'],
                'TotalAmount' => $row['TotalAmount'],
                'Products' => []
            ];
        }

        // Check if the product already exists in the Products 
        $found = false;
        foreach ($orders[$row['OrderID']]['Products'] as &$product) {
            if ($product['ProductName'] == $row['ProductName']) {
                
                $product['ProductQuantity'] += $row['ProductQuantity'];
                $found = true;
                break;
            }
        }
        if (!$found) {
            
            $orders[$row['OrderID']]['Products'][] = [
                'ProductName' => $row['ProductName'],
                'ProductQuantity' => $row['ProductQuantity']
            ];
        }
    }
} else {
    $message[] = "You have no orders.";
}

mysqli_close($connection);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/view_order_usersStyle.css">
    
</head>
<body>
<div class="container">
    <h2>My Orders</h2>

    <!-- Display Messages -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-info">
            <?php foreach ($message as $msg): ?>
                <p><?php echo $msg; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Display Orders -->
    <?php if (!empty($orders)): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th>Total Amount</th>
                    <th>Products</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $orderID => $orderDetails): ?>
                    <tr>
                        <td><?php echo $orderID; ?></td>
                        <td><?php echo $orderDetails['OrderDate']; ?></td>
                        <td><?php echo $orderDetails['orderStatus']; ?></td>
                        <td>$<?php echo number_format($orderDetails['TotalAmount'], 2); ?></td>
                        <td>
                            <ul>
                                <?php foreach ($orderDetails['Products'] as $product): ?>
                                    <li><?php echo $product['ProductName']; ?> - Quantity: <?php echo $product['ProductQuantity']; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- Back to Profile Button -->
    <div class="text-center mt-3">
        <a href="updateuser.php" class="btn">Back to Profile</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
