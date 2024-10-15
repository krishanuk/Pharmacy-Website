<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['pharmacist_id'])) {
    header('Location: login.php');
    exit;
}

$pharmacyID = $_SESSION['pharmacy_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacist Dashboard</title>
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
    <h2 class="my-5">Pharmacist Dashboard</h2>
    <div class="dashboard-container">
        <div class="dashboard-card">
            <div class="card-body">
                <h5>Manage Products</h5>
                <p>View, add, or update product information</p>
                <a href="manage_products.php" class="btn btn-blue btn-block">Go to Manage Products</a>
            </div>
        </div>
        <div class="dashboard-card">
            <div class="card-body">
                <h5>View Orders</h5>
                <p>Check customer orders <br> and statuses</p>
                <a href="view_orders_admin.php" class="btn btn-green btn-block">Go to View Orders</a>
            </div>
        </div>
        <div class="dashboard-card">
            <div class="card-body">
                <h5>Sales Report</h5>
                <p>View sales data and generate reports</p>
                <a href="sales_report.php" class="btn btn-blue btn-block">Go to Sales Report</a>
            </div>
        </div>
        
        <div class="dashboard-card">
            <div class="card-body">
                <h5>Add Batch</h5>
                <p>Add new batch</p>
                <a href="add_batch.php" class="btn btn-green btn-block">Add Batch</a>
            </div>
        </div>
        <div class="dashboard-card">
            <div class="card-body">
                <h5>View Prescription</h5>
                <p>View Prescription Details</p>
                <a href="/Dexcare1/ChatC/colombo.html" class="btn btn-blue btn-block">View Prescription</a>
            </div>
        </div>
        
    </div>
</div>

</body>
</html>
