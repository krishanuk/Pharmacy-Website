<?php
session_start();
include 'db_connection.php';

// Check if the request is POST and alert_id is present
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['alert_id'])) {
    $alertID = intval($_POST['alert_id']);

    // Check if the alert exists and is still pending
    $checkAlertQuery = "SELECT * FROM expiry_alerts WHERE AlertID = ? AND AlertStatus = 'pending'";
    $checkAlertStmt = $conn->prepare($checkAlertQuery);
    $checkAlertStmt->bind_param("i", $alertID);
    $checkAlertStmt->execute();
    $alertResult = $checkAlertStmt->get_result();

    if ($alertResult->num_rows > 0) {
        // Update the alert status to 'resolved'
        $updateAlertQuery = "UPDATE expiry_alerts SET AlertStatus = 'resolved' WHERE AlertID = ?";
        $updateAlertStmt = $conn->prepare($updateAlertQuery);
        $updateAlertStmt->bind_param("i", $alertID);
        $updateAlertStmt->execute();

        if ($updateAlertStmt->affected_rows > 0) {
            // Successfully updated, redirect to the alerts page with a success message
            header("Location: view_alerts.php?status=resolved");
            exit;
        } else {
            echo "Error: Unable to update the alert status.";
        }

        $updateAlertStmt->close();
    } else {
        echo "Error: Alert not found or already resolved.";
    }

    $checkAlertStmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
