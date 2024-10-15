<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "
    SELECT
        c.CartID,
        p.ProductID,
        c.InventoryID,
        c.PharmacyID,
        p.ProductName,
        i.UnitPrice,
        c.Quantity,
        i.Quantity AS StockQuantity,  
        p.Image,
        ph.PharmacyName,
        (i.UnitPrice * c.Quantity) AS TotalPrice
    FROM Cart c
    INNER JOIN Products p ON c.ProductID = p.ProductID
    INNER JOIN inventory i ON c.InventoryID = i.InventoryID
    INNER JOIN pharmacies ph ON c.PharmacyID = ph.PharmacyID -- Get pharmacy name
    WHERE c.UserID = ?
    ORDER BY c.PharmacyID";  

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);

$total_cart_value = [];
$cart_by_pharmacy = [];

foreach ($cart_items as $item) {
    $pharmacy_id = $item['PharmacyID'];
    if (!isset($cart_by_pharmacy[$pharmacy_id])) {
        $cart_by_pharmacy[$pharmacy_id] = [
            'PharmacyName' => $item['PharmacyName'],
            'Items' => [],
            'TotalValue' => 0
        ];
    }
    $cart_by_pharmacy[$pharmacy_id]['Items'][] = $item;
    $cart_by_pharmacy[$pharmacy_id]['TotalValue'] += $item['TotalPrice'];
}

$similar_products_query = "
    SELECT
        p.ProductID,
        p.ProductName,
        p.Image,
        i.UnitPrice,
        i.Quantity,
        p.Category,
        i.InventoryID,
        i.PharmacyID,
        ph.PharmacyName
    FROM products p
    JOIN inventory i ON p.ProductID = i.ProductID
    JOIN pharmacies ph ON i.PharmacyID = ph.PharmacyID
    WHERE i.Quantity > 0
    AND i.IsAvailable = 1
    AND p.Category IN (
        SELECT Category
        FROM products
        WHERE ProductID IN (SELECT ProductID FROM Cart WHERE UserID = ?)
    )
    LIMIT 5";

$similar_stmt = $conn->prepare($similar_products_query);
$similar_stmt->bind_param("i", $user_id);
$similar_stmt->execute();
$similar_result = $similar_stmt->get_result();

// Fetch similar products
$similar_products = $similar_result->fetch_all(MYSQLI_ASSOC);
$similar_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <link rel="stylesheet" href="CSS/cart.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            /* Light grey */
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h2,
        h3 {
            text-align: center;
            color: #4a90e2;
            /* Soft blue */
        }

        .alert {
            color: #155724;
            background-color: #d4edda;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
        }

        .cart-table th,
        .cart-table td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        .cart-table th {
            background-color: #4a90e2;
            /* Soft blue */
            color: #ffffff;
        }

        .cart-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin-right: 15px;
            border-radius: 5px;
        }

        .quantity-input {
            width: 60px;
            padding: 5px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }

        .update-button,
        .remove-button {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background-color 0.3s;
        }

        .update-button {
            background-color: #5bc0de;
            /* Soft cyan */
            color: #ffffff;
        }

        .remove-button {
            background-color: #d9534f;
            /* Soft red */
            color: #ffffff;
        }

        .update-button:hover {
            background-color: #31b0d5;
            /* Slightly darker cyan */
        }

        .remove-button:hover {
            background-color: #c9302c;
            /* Slightly darker red */
        }

        .total-section {
            text-align: right;
            margin-top: 20px;
        }

        .total-section h4 {
            font-size: 1.5em;
            color: #4a90e2;
            /* Soft blue */
        }

        .checkout-button {
            background-color: #5cb85c;
            /* Soft green */
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s;
        }

        .checkout-button:hover {
            background-color: #4cae4c;
            /* Slightly darker green */
        }

        .similar-products h4 {
            color: #4a90e2;
            /* Soft blue */
            margin-bottom: 20px;
        }

        .similar-products .product-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s;
            max-width: 280px;
            margin: 10px;
            display: inline-block;
            vertical-align: top;
        }

        .similar-products .product-card:hover {
            transform: scale(1.05);
        }

        .similar-products img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            margin-bottom: 15px;
            border-radius: 8px;
        }

        .similar-products .product-card h5 {
            margin: 0;
            font-size: 1.2em;
            font-weight: bold;
            color: #333333;
        }

        .view-product-button {
            background-color: #4a90e2;
            /* Soft blue */
            color: #ffffff;
            padding: 5px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s;
        }

        .view-product-button:hover {
            background-color: #357ab8;
            /* Slightly darker blue */
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Your Cart</h2>

        <?php if (!empty($cart_by_pharmacy)): ?>
            <?php foreach ($cart_by_pharmacy as $pharmacy_id => $cart): ?>
                <h3><?php echo htmlspecialchars($cart['PharmacyName']); ?> (Pharmacy ID: <?php echo $pharmacy_id; ?>)</h3>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product Image</th>
                            <th>Product Name</th>
                            <th>Product ID</th>
                            <th>Unit Price</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                            <th>Stock Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart['Items'] as $item): ?>
                            <tr>
                                <td>
                                    <img src="../images/<?php echo htmlspecialchars($item['Image']); ?>" alt="<?php echo htmlspecialchars($item['ProductName']); ?>" width="80" height="80">
                                </td>
                                <td><?php echo htmlspecialchars($item['ProductName']); ?></td>
                                <td><?php echo htmlspecialchars($item['ProductID']); ?></td>
                                <td>$<?php echo number_format($item['UnitPrice'], 2); ?></td>
                                <td>
                                    <form action="update_cart.php" method="POST">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['CartID']; ?>">
                                        <input type="number" name="quantity" value="<?php echo $item['Quantity']; ?>" min="1" max="<?php echo $item['StockQuantity']; ?>" class="quantity-input">
                                        <button type="submit" class="update-button">Update</button>
                                    </form>
                                </td>
                                <td>$<?php echo number_format($item['TotalPrice'], 2); ?></td>
                                <td><?php echo ($item['StockQuantity'] >= $item['Quantity']) ? 'In Stock' : 'Limited Stock'; ?></td>
                                <td>
                                    <form action="remove_from_cart.php" method="POST">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['CartID']; ?>">
                                        <button type="submit" class="remove-button">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="total-section">
    <h4>Total: $<?php echo number_format($cart['TotalValue'], 2); ?></h4>
    <?php if ($cart['TotalValue'] >= 50): ?>
        <a href="checkout.php?pharmacy_id=<?php echo $pharmacy_id; ?>" class="checkout-button">
            Proceed to Checkout for <?php echo htmlspecialchars($cart['PharmacyName']); ?>
        </a>
    <?php else: ?>
        <div class="checkout-restriction">
            <p class="message">
                Add $<?php echo number_format(50 - $cart['TotalValue'], 2); ?> more to reach the minimum order value of $50 for checkout.
            </p>
            <button class="checkout-button" disabled title="Your order must be at least $50 to proceed to checkout">
                Checkout (Minimum $50 required)
            </button>
        </div>
    <?php endif; ?>
</div>

                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert">Your cart is empty.</div>
        <?php endif; ?>

        <!-- Similar Products Section -->
        <?php if (!empty($similar_products)): ?>
            <div class="similar-products">
                <h4>Similar Products You Might Like</h4>
                <?php foreach ($similar_products as $similar_product): ?>
                    <div class="product-card">
                        <img src="../images/<?php echo htmlspecialchars($similar_product['Image']); ?>" alt="<?php echo htmlspecialchars($similar_product['ProductName']); ?>">
                        <h5><?php echo htmlspecialchars($similar_product['ProductName']); ?></h5>
                        <p>$<?php echo htmlspecialchars(number_format($similar_product['UnitPrice'], 2)); ?></p>
                        <p><strong>Pharmacy:</strong> <?php echo htmlspecialchars($similar_product['PharmacyName']); ?></p>
                        <a href="branch_page.php?id=<?php echo htmlspecialchars($similar_product['PharmacyID']); ?>&product_id=<?php echo htmlspecialchars($similar_product['ProductID']); ?>" class="view-product-button">View Product</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>