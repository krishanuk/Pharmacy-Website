<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['pharmacist_id'])) {
    header("Location: logininvent.php");
    exit;
}

// Fetch products for selection
$productStmt = $conn->prepare("SELECT ProductID, ProductName FROM products WHERE PharmacyID = ?");
$productStmt->bind_param("i", $_SESSION['pharmacy_id']);
$productStmt->execute();
$products = $productStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$productStmt->close();

// Handle form submission to add a new batch
$formReset = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productID = intval($_POST['product_id']);
    $batchNumber = htmlspecialchars(trim($_POST['batch_number']));
    $quantity = intval($_POST['quantity']);
    $unitPrice = floatval($_POST['unit_price']);
    $expiryDate = $_POST['expiry_date'];
    $measurementUnit = htmlspecialchars(trim($_POST['measurement_unit']));
    $minimumStock = intval($_POST['minimum_stock']); 

    // Insert the new batch into the inventory table
    $batchStmt = $conn->prepare("INSERT INTO inventory (ProductID, PharmacyID, BatchNumber, Quantity, UnitPrice, ExpiryDate, MeasurementUnit, MinimumStock, IsAvailable) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
    $batchStmt->bind_param("iisiissi", $productID, $_SESSION['pharmacy_id'], $batchNumber, $quantity, $unitPrice, $expiryDate, $measurementUnit, $minimumStock);

    if ($batchStmt->execute()) {
        echo "<div class='alert-success'>Batch added successfully!</div>";
        $formReset = true; 
    } else {
        echo "<div class='alert-danger'>Error adding batch: " . $conn->error . "</div>";
    }

    $batchStmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add New Batch</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function resetForm() {
            document.getElementById('batchForm').reset();
        }
    </script>
</head>

<body>
    <div class="container mt-5">
        <h2>Add New Batch to Existing Product</h2>

        <form method="POST" id="batchForm" action="">
            <div class="form-group">
                <label for="product_id">Select Product:</label>
                <select class="form-control" id="product_id" name="product_id" required>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= htmlspecialchars($product['ProductID']) ?>"><?= htmlspecialchars($product['ProductName']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="batch_number">Batch Number:</label>
                <input type="text" class="form-control" id="batch_number" name="batch_number" required>
            </div>

            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
            </div>

            <div class="form-group">
                <label for="unit_price">Unit Price:</label>
                <input type="number" class="form-control" id="unit_price" name="unit_price" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="expiry_date">Expiry Date:</label>
                <input type="date" class="form-control" id="expiry_date" name="expiry_date" required>
            </div>

            <div class="form-group">
                <label for="measurement_unit">Measurement Unit (e.g., mg, ml):</label>
                <input type="text" class="form-control" id="measurement_unit" name="measurement_unit" required>
            </div>

            <div class="form-group">
                <label for="minimum_stock">Minimum Stock:</label>
                <input type="number" class="form-control" id="minimum_stock" name="minimum_stock" min="1" required>
            </div>

            <button type="submit" class="btn btn-primary">Add Batch</button>
        </form>
    </div>

    <?php if ($formReset): ?>
        <script>
            resetForm(); // clear form
        </script>
    <?php endif; ?>

</body>

</html>