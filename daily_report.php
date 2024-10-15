<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in and is a pharmacist
if (!isset($_SESSION['pharmacist_id'])) {
    header("Location: logininvent.php");
    exit;
}


$pharmacyID = $_SESSION['pharmacy_id']; 


$query = "
    SELECT 
        o.OrderID,
        o.OrderDate,
        u.Username,
        p.ProductName,
        SUM(od.Quantity) AS TotalQuantity,
        SUM(od.Quantity * p.ProductPrice) AS TotalAmount
    FROM 
        orders o
    JOIN 
        orderdetails od ON o.OrderID = od.OrderID
    JOIN 
        products p ON od.ProductID = p.ProductID
    JOIN 
        users u ON o.UserID = u.user_id
    WHERE 
        o.OrderDate >= CURDATE() AND o.OrderDate < CURDATE() + INTERVAL 1 DAY
        AND o.PharmacyID = ?
    GROUP BY 
        o.OrderID, o.OrderDate, p.ProductName, u.Username
    ORDER BY 
        o.OrderDate DESC
";

if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $pharmacyID);
    $stmt->execute();
    $result = $stmt->get_result();
    $reportData = $result->fetch_all(MYSQLI_ASSOC);
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
    <title>Daily Order Report</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Daily Order Report</h2>
    <?php if (empty($reportData)): ?>
        <div class="alert alert-info">No orders found for today.</div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Total Quantity</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportData as $data): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($data['OrderID']); ?></td>
                        <td><?php echo htmlspecialchars($data['OrderDate']); ?></td>
                        <td><?php echo htmlspecialchars($data['Username']); ?></td>
                        <td><?php echo htmlspecialchars($data['ProductName']); ?></td>
                        <td><?php echo htmlspecialchars($data['TotalQuantity']); ?></td>
                        <td><?php echo htmlspecialchars(number_format($data['TotalAmount'], 2)); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>
</body>
</html>
