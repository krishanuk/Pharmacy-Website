<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['pharmacist_id'])) {
    header("Location: logininvent.php");
    exit;
}

$pharmacyID = $_SESSION['pharmacy_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //validate input
    $productName = htmlspecialchars(trim($_POST['product_name']));
    $productPrice = floatval($_POST['product_price']);
    $description = htmlspecialchars(trim($_POST['description']));
    $brandName = htmlspecialchars(trim($_POST['brand_name']));
    $category = htmlspecialchars(trim($_POST['category']));
    $dosage = htmlspecialchars(trim($_POST['dosage']));
    $batchNumber = htmlspecialchars(trim($_POST['batch_number']));
    $expiryDate = $_POST['expiry_date'];
    $measurementUnit = htmlspecialchars(trim($_POST['measurement_unit']));
    $stockQuantity = intval($_POST['stock_quantity']);
    $unitPrice = floatval($_POST['unit_price']);
    $isAvailable = intval($_POST['is_available']);
    $minimumStock = intval($_POST['minimum_stock']); 

    // Validate fields
    if (
        empty($productName) || empty($productPrice) || empty($description) || empty($brandName) ||
        empty($category) || empty($dosage) || empty($batchNumber) || empty($expiryDate) ||
        empty($measurementUnit) || $stockQuantity < 0 || $unitPrice < 0 || $minimumStock < 0
    ) {
        echo "All fields are required, and values must be non-negative.";
        exit;
    }

    if ($minimumStock > $stockQuantity) {
        echo "Minimum stock cannot be greater than the stock quantity.";
        exit;
    }

    // Handle file upload for product image
    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $imageName = basename($_FILES['image']['name']);
        $imageTmpName = $_FILES['image']['tmp_name'];
        $target_dir = __DIR__ . "/../images/";
        $target_file = $target_dir . $imageName;

        // Check if the directory exists, create it if not
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        // Validate image upload
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file is an image
        $check = getimagesize($imageTmpName);
        if ($check === false) {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES['image']['size'] > 5000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow only JPG, JPEG, and PNG files
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            echo "Sorry, only JPG, JPEG, and PNG files are allowed.";
            $uploadOk = 0;
        }

        // Try to upload the file
        if ($uploadOk == 1) {
            if (!move_uploaded_file($imageTmpName, $target_file)) {
                echo "Sorry, there was an error uploading your file.";
                exit;
            }
        } else {
            exit;
        }
    }

    // Insert product into database
    $stmt = $conn->prepare("INSERT INTO products (ProductName, ProductPrice, PharmacyID, Image, Description, BrandName, Category, Dosage) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("SQL error: " . $conn->error);
    }
    $stmt->bind_param("sdisssss", $productName, $productPrice, $pharmacyID, $imageName, $description, $brandName, $category, $dosage);
    $stmt->execute();

    // Get the last inserted product ID
    $productId = $conn->insert_id;

    // Insert batch information into the inventory table
    $inventoryStmt = $conn->prepare("INSERT INTO inventory (ProductID, PharmacyID, BatchNumber, Quantity, UnitPrice, ExpiryDate, MeasurementUnit, IsAvailable, MinimumStock) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$inventoryStmt) {
        die("SQL error: " . $conn->error);
    }
    $inventoryStmt->bind_param("iisisssii", $productId, $pharmacyID, $batchNumber, $stockQuantity, $unitPrice, $expiryDate, $measurementUnit, $isAvailable, $minimumStock);
    $inventoryStmt->execute();

    // Redirect after successful insertion
    header("Location: manage_products.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function validateForm() {
            // Image validation
            const image = document.getElementById('image');
            if (image.files.length > 0) {
                const fileSize = image.files[0].size;
                const maxSize = 5 * 1024 * 1024; // 5 MB
                const fileType = image.files[0].type;

                if (fileSize > maxSize) {
                    alert('The image file size must be less than 5MB.');
                    return false;
                }
                if (fileType !== 'image/jpeg' && fileType !== 'image/png') {
                    alert('Only JPG, JPEG, and PNG formats are allowed.');
                    return false;
                }
            }

            // Stock validation
            const stockQuantity = parseInt(document.getElementById('stock_quantity').value);
            const minimumStock = parseInt(document.getElementById('minimum_stock').value);
            if (stockQuantity < 0 || minimumStock < 0) {
                alert('Stock quantity and minimum stock must be non-negative numbers.');
                return false;
            }
            if (minimumStock > stockQuantity) {
                alert('Minimum stock cannot be greater than stock quantity.');
                return false;
            }

            return true;
        }
    </script>
</head>

<body>
    <div class="container mt-5">
        <h2>Add New Product</h2>

        <form method="POST" action="" enctype="multipart/form-data" onsubmit="return validateForm()">
            <!-- Product Details -->
            <div class="form-group">
                <label for="product_name">Product Name:</label>
                <input type="text" class="form-control" id="product_name" name="product_name" required>
            </div>

            <div class="form-group">
                <label for="product_price">Product Price:</label>
                <input type="number" class="form-control" id="product_price" name="product_price" step="0.01" required min="0">
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control" id="description" name="description" required></textarea>
            </div>

            <div class="form-group">
                <label for="brand_name">Brand Name:</label>
                <input type="text" class="form-control" id="brand_name" name="brand_name" required>
            </div>

            <div class="form-group">
                <label for="category">Category:</label>
                <input type="text" class="form-control" id="category" name="category" required>
            </div>

            <div class="form-group">
                <label for="dosage">Dosage:</label>
                <input type="text" class="form-control" id="dosage" name="dosage" required>
            </div>

            <div class="form-group">
                <label for="is_available">Is Available:</label>
                <select class="form-control" id="is_available" name="is_available">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>

            <div class="form-group">
                <label for="image">Product Image:</label>
                <input type="file" class="form-control-file" id="image" name="image" accept=".jpg, .jpeg, .png">
            </div>

            <!-- Inventory/Batch Details -->
            <h3>Batch Information</h3>

            <div class="form-group">
                <label for="batch_number">Batch Number:</label>
                <input type="text" class="form-control" id="batch_number" name="batch_number" required>
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
                <label for="stock_quantity">Stock Quantity:</label>
                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" required min="0">
            </div>

            <div class="form-group">
                <label for="unit_price">Unit Price:</label>
                <input type="number" class="form-control" id="unit_price" name="unit_price" step="0.01" required min="0">
            </div>

            <div class="form-group">
                <label for="minimum_stock">Minimum Stock:</label>
                <input type="number" class="form-control" id="minimum_stock" name="minimum_stock" required min="0">
            </div>

            <button type="submit" class="btn btn-primary">Add Product</button>
        </form>
    </div>

</body>

</html>