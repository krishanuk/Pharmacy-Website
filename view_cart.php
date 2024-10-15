<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

$sql = "
    SELECT 
        c.CartID, 
        p.ProductName, 
        p.ProductPrice, 
        c.Quantity, 
        p.Image, 
        (p.ProductPrice * c.Quantity) AS TotalPrice
    FROM Cart c
    INNER JOIN Products p ON c.ProductID = p.ProductID
    WHERE c.UserID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);

// Calculate the total cart value
$total_cart_value = 0;
foreach ($cart_items as $item) {
    $total_cart_value += $item['TotalPrice'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Your Cart</h2>
    
    <?php if (!empty($cart_items)): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Product Image</th>
                    <th>Product Name</th>
                    <th>Unit Price</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td>
                            <img src="images/<?php echo htmlspecialchars($item['Image']); ?>" alt="<?php echo htmlspecialchars($item['ProductName']); ?>" width="80" height="80">
                        </td>
                        <td><?php echo htmlspecialchars($item['ProductName']); ?></td>
                        <td>$<?php echo number_format($item['ProductPrice'], 2); ?></td>
                        <td>
                            <form action="update_cart.php" method="POST" class="form-inline">
                                <input type="hidden" name="cart_id" value="<?php echo $item['CartID']; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['Quantity']; ?>" min="1" class="form-control" style="width: 60px;">
                                <button type="submit" class="btn btn-sm btn-primary ml-2">Update</button>
                            </form>
                        </td>
                        <td>$<?php echo number_format($item['TotalPrice'], 2); ?></td>
                        <td>
                            <form action="remove_from_cart.php" method="POST">
                                <input type="hidden" name="cart_id" value="<?php echo $item['CartID']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="text-right">
            <h4>Total: $<?php echo number_format($total_cart_value, 2); ?></h4>
            <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Your cart is empty.</div>
    <?php endif; ?>
</div>
</body>
</html>
