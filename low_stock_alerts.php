<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['pharmacist_id'])) {
    header("Location: logininvent.php");
    exit;
}

$pharmacyID = $_SESSION['pharmacy_id'];

function getLowStockAlerts($pharmacyID) {
    global $conn;

    try {
        $alertsQuery = "
            SELECT lsa.AlertID, p.ProductName, lsa.CurrentStock, lsa.Threshold, lsa.CreatedAt
            FROM LowStockAlerts lsa
            JOIN Products p ON lsa.ProductID = p.ProductID
            WHERE lsa.PharmacyID = ?
            ORDER BY lsa.CreatedAt DESC
        ";

        $stmt = $conn->prepare($alertsQuery);
        $stmt->bind_param("i", $pharmacyID);
        $stmt->execute();
        $result = $stmt->get_result();
        $alerts = $result->fetch_all(MYSQLI_ASSOC);

        return $alerts;

    } catch (Exception $e) {
        echo "Failed to retrieve low stock alerts: " . $e->getMessage();
        return [];
    }
}

$alerts = getLowStockAlerts($pharmacyID);

// Set a flag to indicate if there are any alerts
$hasAlerts = !empty($alerts);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Low Stock Alerts</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">


    <style>
        /* Notification icon style */
        .notification-icon {
            position: relative;
            display: inline-block;
        }

        .notification-icon .badge {
            position: absolute;
            top: -10px;
            right: -10px;
            padding: 5px 10px;
            border-radius: 50%;
            background: red;
            color: white;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Low Stock Alerts</h2>

    <!-- Notification Icon -->
    <div style="position: relative; display: inline-block;">
    <i class="fas fa-bell" style="font-size: 40px;"></i>
    <?php if ($hasAlerts): ?>
        <span class="badge" style="position: absolute; top: -10px; right: -10px; background-color: red; color: white; border-radius: 50%; padding: 5px;">
            <?php echo count($alerts); ?>
        </span>
    <?php endif; ?>
</div>

    <!-- Alerts Table -->
    <?php if ($hasAlerts): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Alert ID</th>
                    <th>Product Name</th>
                    <th>Current Stock</th>
                    <th>Threshold</th>
                    <th>Alert Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alerts as $alert): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($alert['AlertID']); ?></td>
                        <td><?php echo htmlspecialchars($alert['ProductName']); ?></td>
                        <td><?php echo htmlspecialchars($alert['CurrentStock']); ?></td>
                        <td><?php echo htmlspecialchars($alert['Threshold']); ?></td>
                        <td><?php echo htmlspecialchars($alert['CreatedAt']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No low stock alerts found.</p>
    <?php endif; ?>
</div>

<!-- Optional JavaScript to Show Notification Dynamically -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var hasAlerts = <?php echo json_encode($hasAlerts); ?>;
        if (hasAlerts) {
            // Show notification in case of alerts
            alert('You have low stock alerts!');
        }
    });
</script>

</body>
</html>
