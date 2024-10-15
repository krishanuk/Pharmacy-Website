<?php
session_start();
include 'db_connection.php';

// Ensure that the user is logged in
if (!isset($_SESSION['pharmacist_id'])) {
    header("Location: logininvent.php");
    exit;
}

$pharmacyId = $_SESSION['pharmacy_id']; // Get the pharmacy ID from the session


$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'); 
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

function generateStockReport($pharmacyId, $startDate, $endDate) {
    global $conn;

   
    $reportQuery = "
        SELECT p.ProductName, p.StockQuantity, sm.MovementType, sm.Quantity, sm.MovementDate, p.ProductPrice
        FROM products p
        LEFT JOIN stockmovements sm ON p.ProductID = sm.ProductID
        WHERE p.PharmacyID = ?
        AND sm.MovementDate BETWEEN ? AND ?
        ORDER BY p.ProductName, sm.MovementDate DESC
    ";

  
    $stmt = $conn->prepare($reportQuery);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameter and execute the statement
    $stmt->bind_param('iss', $pharmacyId, $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    $reportData = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $reportData;
}

// Generate the report data
$reportData = generateStockReport($pharmacyId, $startDate, $endDate);

// Calculate totals
$totalPurchases = 0;
$totalSales = 0;

foreach ($reportData as $data) {
    if ($data['MovementType'] == 'in') {
        $totalPurchases += $data['Quantity'] * $data['ProductPrice'];
    } elseif ($data['MovementType'] == 'out') {
        $totalSales += $data['Quantity'] * $data['ProductPrice'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Report</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Stock Report</h2>

    <!-- Date Range Form -->
    <form method="GET" action="generate_report.php" class="mb-4">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($startDate); ?>" class="form-control" required>
            </div>
            <div class="form-group col-md-6">
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($endDate); ?>" class="form-control" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>


    <?php if (!empty($reportData)): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Stock Quantity</th>
                    <th>Movement Type</th>
                    <th>Quantity</th>
                    <th>Movement Date</th>
                    <th>Unit Price</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportData as $data): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($data['ProductName']); ?></td>
                        <td><?php echo htmlspecialchars($data['StockQuantity']); ?></td>
                        <td><?php echo htmlspecialchars($data['MovementType']); ?></td>
                        <td><?php echo htmlspecialchars($data['Quantity']); ?></td>
                        <td><?php echo htmlspecialchars($data['MovementDate']); ?></td>
                        <td><?php echo htmlspecialchars(number_format($data['ProductPrice'], 2)); ?></td>
                        <td><?php echo htmlspecialchars(number_format($data['Quantity'] * $data['ProductPrice'], 2)); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="6"><strong>Total Purchases:</strong></td>
                    <td><?php echo htmlspecialchars(number_format($totalPurchases, 2)); ?></td>
                </tr>
                <tr>
                    <td colspan="6"><strong>Total Sales:</strong></td>
                    <td><?php echo htmlspecialchars(number_format($totalSales, 2)); ?></td>
                </tr>
            </tbody>
        </table>
    <?php else: ?>
        <p>No stock movements found for the selected date range.</p>
    <?php endif; ?>
</div>
</body>
</html>
