<?php
session_start();
include 'db_connection.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Step 2: Update Order Status or Order Details 
if (isset($_POST['update'])) {
    $orderID = $_POST['OrderID'];
    $orderStatus = $_POST['orderStatus'];

    $update_sql = "UPDATE orders SET orderStatus='$orderStatus' WHERE OrderID=$orderID";
    if ($conn->query($update_sql) === TRUE) {
        echo "<div class='success-message'>Order status updated successfully.</div>";
    } else {
        echo "<div class='error-message'>Error updating order status: " . $conn->error . "</div>";
    }

    // Update order details
    foreach ($_POST['details'] as $detailID => $quantity) {
        $update_detail_sql = "UPDATE orderdetails SET ProductQuantity=$quantity WHERE DetailID=$detailID";
        $conn->query($update_detail_sql);
    }
}


$pharmacyID = $_SESSION['pharmacy_id'];

//Retrieve Orders and their details
$sql = "SELECT * FROM orders WHERE PharmacyID = $pharmacyID";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order List with Details and Update</title>
    <link rel="stylesheet" href="CSS/view_orders_admin_Style.css">
</head>
<body>
    <h2>Order List with Product Details and Update Option</h2>
    <table>
        <tr>
            <th>Order ID</th>
            <th>User ID</th>
            <th>Order Date</th>
            <th>Status</th>
            <th>Customer Name</th>
            <th>Total Amount</th>
            <th>Order Status</th>
            <th>Products</th>
            <th>Action</th>
        </tr>

        <?php
        if ($result->num_rows > 0) {
            while($order = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $order['OrderID'] . "</td>";
                echo "<td>" . $order['UserID'] . "</td>";
                echo "<td>" . $order['OrderDate'] . "</td>";
                echo "<td>" . $order['Status'] . "</td>";
                echo "<td>" . $order['CustomerName'] . "</td>";
                echo "<td>" . $order['TotalAmount'] . "</td>";
                
                // Form to update order status and product quantities
                echo "<td>
                        <form method='POST' action=''>
                            <input type='hidden' name='OrderID' value='" . $order['OrderID'] . "'>
                            <select name='orderStatus'>
                                <option value='Pending'" . ($order['orderStatus'] == 'Pending' ? ' selected' : '') . ">Pending</option>
                                <option value='Shipped'" . ($order['orderStatus'] == 'Shipped' ? ' selected' : '') . ">Shipped</option>
                                <option value='Delivered'" . ($order['orderStatus'] == 'Delivered' ? ' selected' : '') . ">Delivered</option>
                                <option value='Cancelled'" . ($order['orderStatus'] == 'Cancelled' ? ' selected' : '') . ">Cancelled</option>
                            </select>
                    </td>";

                //display product details order
                $orderID = $order['OrderID'];
                $product_sql = "SELECT orderdetails.DetailID, orderdetails.ProductID, orderdetails.ProductQuantity, products.ProductName 
                                FROM orderdetails 
                                JOIN products ON orderdetails.ProductID = products.ProductID 
                                WHERE orderdetails.OrderID=$orderID";
                $product_result = $conn->query($product_sql);

                echo "<td><table class='product-table'>";
                echo "<tr><th>Product Name</th><th>Quantity</th></tr>";
                while ($product = $product_result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $product['ProductName'] . "</td>
                            <td><input type='number' name='details[" . $product['DetailID'] . "]' value='" . $product['ProductQuantity'] . "'></td>
                          </tr>";
                }
                echo "</table></td>";

                echo "<td><input type='submit' name='update' value='Update'></form></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='9'>No orders found</td></tr>";
        }
        $conn->close();
        ?>
    </table>
</body>
</html>
