<?php
session_start();
include('db_connection.php'); 

if (!isset($_SESSION['pharmacist_id'])) {
    header("Location: login.php");
    exit;
}

$pharmacyID = $_SESSION['pharmacy_id'];

//date range
$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : '';

$sql = "SELECT o.OrderID, o.OrderDate, o.CustomerName, o.CustomerAddress, o.ContactNumber, o.TotalAmount, 
        SUM(od.ProductQuantity) AS TotalProducts
        FROM orders o
        LEFT JOIN orderdetails od ON o.OrderID = od.OrderID
        WHERE o.Status = 'Completed'
        AND o.PharmacyID = '$pharmacyID'"; 

if (!empty($startDate) && !empty($endDate)) {
    $sql .= " AND o.OrderDate BETWEEN '$startDate' AND '$endDate'";
}
$sql .= " GROUP BY o.OrderID ORDER BY o.OrderDate DESC";

$result = $conn->query($sql);

// Calculate total
$totalSales = 0;
if ($result && $result->num_rows > 0) {
    while ($sale = $result->fetch_assoc()) {
        $totalSales += $sale['TotalAmount']; 
    }
    
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <link rel="stylesheet" href="CSS/sales_report_style.css">
    <script>
        function printReport() {
            window.print();
        }
    </script>
</head>
<body>
    <h1>Sales Report</h1>

    <!-- Date Filter Form -->
    <form method="post" action="">
        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" value="<?php echo $startDate; ?>" required>
        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" value="<?php echo $endDate; ?>" required>
        <input type="submit" value="Filter">
    </form>

    <button class="print-button" onclick="printReport()">Print Report</button>

    <br>

    <!-- Sales Report Table -->
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Order Date</th>
                <th>Customer Name</th>
                <th>Customer Address</th>
                <th>Contact Number</th>
                <th>Total Amount</th>
                <th>Total Products</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($sale = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $sale['OrderID']; ?></td>
                    <td><?php echo $sale['OrderDate']; ?></td>
                    <td><?php echo $sale['CustomerName']; ?></td>
                    <td><?php echo $sale['CustomerAddress']; ?></td>
                    <td><?php echo $sale['ContactNumber']; ?></td>
                    <td><?php echo $sale['TotalAmount']; ?></td>
                    <td><?php echo $sale['TotalProducts']; ?></td>
                </tr>
                <?php } ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No sales found for this period.</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5"></td>
                <td>Total Sales</td>
                <td><?php echo number_format($totalSales, 2); ?></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
