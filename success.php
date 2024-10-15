<?php 
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET["amount"]) && !empty($_GET["amount"])) {
    $user_id = $_SESSION['user_id'];
    $total_amount = $_GET["amount"];
    $status = "Completed"; 
    $order_date = date('Y-m-d H:i:s');

    $customer_name = $_SESSION['customer_name'];
    $customer_address = $_SESSION['customer_address'];
    $contact_number = $_SESSION['contact_number'];

    
    $conn->begin_transaction();

    try {
        
        $sql = "SELECT p.PharmacyID
                FROM Cart c
                JOIN Products p ON c.ProductID = p.ProductID
                WHERE c.UserID = ? LIMIT 1";  
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($pharmacy_id);
        $stmt->fetch();
        $stmt->close();

        $insert_main_order = $conn->prepare("
            INSERT INTO Orders (UserID, PharmacyID, OrderDate, Status, CustomerName, CustomerAddress, ContactNumber, TotalAmount) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
    
        $insert_main_order->bind_param("iisssssd", $user_id, $pharmacy_id, $order_date, $status, $customer_name, $customer_address, $contact_number, $total_amount);
        $insert_main_order->execute();
        
        // Get the generated OrderID
        $order_id = $insert_main_order->insert_id; 
        $insert_main_order->close();

        // Step 3: Fetch all items in the user's cart
        $sql = "SELECT c.ProductID, c.Quantity, i.InventoryID, i.BatchNumber, i.Quantity AS InventoryQuantity 
                FROM Cart c 
                JOIN Products p ON c.ProductID = p.ProductID 
                JOIN Inventory i ON c.ProductID = i.ProductID 
                WHERE c.UserID = ? 
                AND i.Quantity > 0 
                ORDER BY i.ExpiryDate ASC";  
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Step 4: Loop through the cart items and insert them into OrderDetails table
        while ($row = $result->fetch_assoc()) {
            $product_id = $row['ProductID'];
            $quantity_purchased = $row['Quantity'];
            $inventory_id = $row['InventoryID'];
            $batch_number = $row['BatchNumber'];
            $inventory_quantity = $row['InventoryQuantity'];
            $remaining_quantity = $inventory_quantity - $quantity_purchased;

            // Insert details into the OrderDetails table
            $insert_order_item = $conn->prepare("
                INSERT INTO OrderDetails (OrderID, ProductID, ProductQuantity) 
                VALUES (?, ?, ?)");

            $insert_order_item->bind_param("iii", $order_id, $product_id, $quantity_purchased);
            $insert_order_item->execute();
            $insert_order_item->close();

            // Update stock Inventory table
            $is_available = ($remaining_quantity <= 0) ? 0 : 1;  

            $update_inventory = $conn->prepare("UPDATE Inventory SET Quantity = ?, IsAvailable = ? WHERE InventoryID = ?");
            $update_inventory->bind_param("iii", $remaining_quantity, $is_available, $inventory_id);
            $update_inventory->execute();
            $update_inventory->close();
        }

        //Clear the cart 
        $delete_cart = $conn->prepare("DELETE FROM Cart WHERE UserID = ?");
        $delete_cart->bind_param("i", $user_id);
        $delete_cart->execute();
        $delete_cart->close();

        $conn->commit();

    } catch (Exception $e) {
        // If there is an error, rollback the transaction
        $conn->rollback();
        echo "Error processing the order: " . $e->getMessage();
        exit();
    }

    $stmt->close();
    $conn->close();

} else {
    echo "Invalid transaction.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success</title>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300&display=swap");
        .success-container {
            width: 50%;
            position: absolute;
            top: 30%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #2ecc71;
            font-weight: bold;
            font-family: "Poppins", sans-serif;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <h3>Your transaction has been successfully completed!</h3>
        <p>Thank you for your purchase.</p>
        <p><strong>Amount Paid: </strong>$<?php echo htmlspecialchars($_GET["amount"]); ?></p>
        <a href="index.php" style="color: #3498db;">Return to Home</a>
    </div>  
</body>
</html>
