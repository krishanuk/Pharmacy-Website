<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['pharmacist_id'])) {
    header("Location: logininvent.php");
    exit;
}

$pharmacyID = $_SESSION['pharmacy_id'];

// Fetch pending alerts for the logged-in pharmacist's pharmacy
$query = "
    SELECT 
        ea.AlertID, 
        p.ProductName, 
        i.BatchNumber, 
        i.Quantity, 
        i.MinimumStock, 
        ea.ExpiryDate
    FROM 
        expiry_alerts ea
    JOIN 
        inventory i ON ea.InventoryID = i.InventoryID
    JOIN 
        products p ON i.ProductID = p.ProductID
    WHERE 
        i.PharmacyID = ? AND ea.AlertStatus = 'pending'";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $pharmacyID);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Low Stock Alerts</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Low Stock Alerts</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Alert ID</th>
                <th>Product Name</th>
                <th>Batch Number</th>
                <th>Current Stock</th>
                <th>Minimum Stock</th>
                <th>Expiry Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['AlertID']) ?></td>
                    <td><?= htmlspecialchars($row['ProductName']) ?></td>
                    <td><?= htmlspecialchars($row['BatchNumber']) ?></td>
                    <td><?= htmlspecialchars($row['Quantity']) ?></td>
                    <td><?= htmlspecialchars($row['MinimumStock']) ?></td>
                    <td><?= htmlspecialchars($row['ExpiryDate']) ?></td>
                    <td>
                        <form method="POST" action="mark_alert_resolved.php" class="d-inline-block">
                            <input type="hidden" name="alert_id" value="<?= $row['AlertID'] ?>">
                            <button type="submit" class="btn btn-success">Mark as Resolved</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
