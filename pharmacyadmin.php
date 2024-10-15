<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f8ff;
        }
        .dashboard-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin-top: 30px;
        }
        .dashboard-card {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            width: 100%;
            max-width: 300px;
            margin: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .dashboard-card:hover {
            transform: scale(1.05);
        }
        .dashboard-card h5 {
            color: #0066cc;
        }
        .dashboard-card a {
            text-decoration: none;
            color: white;
        }
        .card-body {
            padding: 20px;
            text-align: center;
        }
        .btn-blue {
            background-color: #0066cc;
            border: none;
            color: white;
        }
        .btn-green {
            background-color: #28a745;
            border: none;
            color: white;
        }
    </style>
</head>
<body>

<div class="container text-center">
    <h2 class="my-5">Admin Dashboard</h2>
    <div class="dashboard-container">
        <div class="dashboard-card">
            <div class="card-body">
                <h5>Pharmacy & Pharmacists Management</h5>
                <p>Access the Pharmacy & Pharmacists Management</p>
                <a href="admin_dashboard.php" class="btn btn-blue btn-block">Go to Admin Dashboard</a>
            </div>
        </div>
        <div class="dashboard-card">
            <div class="card-body">
                <h5>Content Management</h5>
                <p>Manage and update site content</p>
                <a href="content_admin.html" class="btn btn-green btn-block">Go to Content Management</a>
            </div>
        </div>
        <div class="dashboard-card">
            <div class="card-body">
                <h5>Add Admin</h5>
                <p>add admin</p>
                <a href="add_admin.php" class="btn btn-blue btn-block"> Admin </a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
