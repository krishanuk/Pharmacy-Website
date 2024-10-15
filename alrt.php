<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Stock Alerts</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <style>
        .alert-counter {
            position: fixed;
            top: 10px;
            right: 10px;
            background-color: red;
            color: white;
            padding: 10px;
            border-radius: 50%;
            display: none;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2>Dashboard</h2>

        <div id="alert-list" class="mt-4"></div>

        <div id="alert-counter" class="alert-counter">0</div>
    </div>

    <script>
        // Fetch alerts every 10 seconds
        setInterval(fetchAlerts, 10000);

        function fetchAlerts() {
            $.ajax({
                url: 'fetch_alerts.php',
                method: 'GET',
                success: function(response) {
                    const alerts = JSON.parse(response);
                    displayAlerts(alerts);
                }
            });
        }

        function displayAlerts(alerts) {
            const alertList = $('#alert-list');
            const alertCounter = $('#alert-counter');
            alertList.empty();

            if (alerts.length > 0) {
                alertCounter.text(alerts.length).show();
                alerts.forEach(alert => {
                    alertList.append(`
                    <div class="alert alert-warning">
                        <strong>Low Stock!</strong> ${alert.ProductName} (Batch: ${alert.BatchNumber}) has only ${alert.Quantity} left. Minimum stock required is ${alert.MinimumStock}.
                        <p>Created At: ${alert.CreatedAt}</p>
                        <button class="btn btn-success" onclick="resolveAlert(${alert.AlertID})">Mark as Resolved</button>
                    </div>
                `);
                });
            } else {
                alertCounter.hide();
                alertList.append('<div class="alert alert-info">No low stock alerts.</div>');
            }
        }

        function resolveAlert(alertID) {
            $.ajax({
                url: 'mark_alert_resolved.php',
                method: 'POST',
                data: {
                    alert_id: alertID
                },
                success: function() {
                    fetchAlerts(); // Refresh the alerts after resolving
                }
            });
        }

        
        fetchAlerts();
    </script>
</body>

</html>