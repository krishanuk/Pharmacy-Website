<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['pharmacist_id'])) {
    header("Location: logininvent.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get product and inventory
    $productId = intval($_POST['product_id']);
    $inventoryId = intval($_POST['inventory_id']);

    // Fetch updated product data
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

    // Handle file upload
    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $imageName = basename($_FILES['image']['name']);
        $imageTmpName = $_FILES['image']['tmp_name'];
        $target_dir = __DIR__ . "/../images/";
        $target_file = $target_dir . $imageName;

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate file is an image
        $check = getimagesize($imageTmpName);
        if ($check === false) {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Validate file size
        if ($_FILES['image']['size'] > 5000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Validate file type
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            echo "Sorry, only JPG, JPEG, & PNG files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk == 1) {
            if (!move_uploaded_file($imageTmpName, $target_file)) {
                echo "Sorry, there was an error uploading your file.";
                exit;
            }
        }
    }

    // Update product details
    if ($imageName) {
        $stmt = $conn->prepare("UPDATE products SET ProductName = ?, ProductPrice = ?, Image = ?, Description = ?, BrandName = ?, Category = ?, Dosage = ? WHERE ProductID = ?");
        if (!$stmt) {
            die("Error in prepare statement: " . $conn->error);
        }
        $stmt->bind_param("sdsssssi", $productName, $productPrice, $imageName, $description, $brandName, $category, $dosage, $productId);
    } else {
        $stmt = $conn->prepare("UPDATE products SET ProductName = ?, ProductPrice = ?, Description = ?, BrandName = ?, Category = ?, Dosage = ? WHERE ProductID = ?");
        if (!$stmt) {
            die("Error in prepare statement: " . $conn->error);
        }
        $stmt->bind_param("sdssssi", $productName, $productPrice, $description, $brandName, $category, $dosage, $productId);
    }

    if (!$stmt->execute()) {
        echo "Error updating product: " . $stmt->error;
        exit;
    }

    // Update inventory details
    $inventoryStmt = $conn->prepare("UPDATE inventory SET BatchNumber = ?, Quantity = ?, ExpiryDate = ?, UnitPrice = ?, MeasurementUnit = ?, IsAvailable = ?, MinimumStock = ? WHERE InventoryID = ?");
    if (!$inventoryStmt) {
        die("Error in prepare statement: " . $conn->error);
    }
    $inventoryStmt->bind_param("sisdsiii", $batchNumber, $stockQuantity, $expiryDate, $unitPrice, $measurementUnit, $isAvailable, $minimumStock, $inventoryId);

    if (!$inventoryStmt->execute()) {
        echo "Error updating inventory: " . $inventoryStmt->error;
        exit;
    }


    header("Location: manage_products.php");
    exit;
}
?>
