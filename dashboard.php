<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['pharmacist_id'])) {
    header("Location: login.php");
    exit;
}

$pharmacyID = $_SESSION['pharmacy_id'];

// Define categories and dosages
$categories = [
    'Analgesics', 'Antibiotics', 'Antiseptics', 'Cardiovascular',
    'Dermatological', 'Gastrointestinal', 'Hormones', 'Vaccines'
];
$dosages = [
    '100mg', '200mg', '500mg', '1g', '5mg', '10mg', '20mg', '50mg', '100'
];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addProduct'])) {
    $productName = htmlspecialchars($_POST['productName']);
    $productPrice = floatval($_POST['productPrice']);
    $description = htmlspecialchars($_POST['description']);
    $brandName = htmlspecialchars($_POST['brandName']);
    $category = htmlspecialchars($_POST['category']);
    $dosage = htmlspecialchars($_POST['dosage']);
    $stockQuantity = intval($_POST['stockQuantity']);
    $isAvailable = isset($_POST['isAvailable']) ? 1 : 0;

    // Handle file upload
    $imageName = $_FILES['image']['name'];
    $imageTmpName = $_FILES['image']['tmp_name'];
    $target_dir = __DIR__ . "/../images/";

   
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $target_file = $target_dir . basename($imageName);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($imageTmpName);
    if ($check === false) {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    if ($_FILES['image']['size'] > 5000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        echo "Sorry, only JPG, JPEG, & PNG files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($imageTmpName, $target_file)) {
            

            $sql = "INSERT INTO Products (ProductName, ProductPrice, Description, BrandName, Image, IsAvailable, Category, Dosage, StockQuantity, PharmacyID) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                die("Error preparing insert statement: " . $conn->error);
            }
            $stmt->bind_param("sdsdssssii", $productName, $productPrice, $description, $brandName, $imageName, $isAvailable, $category, $dosage, $stockQuantity, $pharmacyID);
            if (!$stmt->execute()) {
                die("Error executing insert statement: " . $stmt->error);
            }
            echo "Product added successfully!";
            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacist Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Dashboard - Pharmacy <?php echo htmlspecialchars($pharmacyID); ?></h2>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="productName">Product Name:</label>
            <input type="text" class="form-control" id="productName" name="productName" required>
        </div>
        <div class="form-group">
            <label for="productPrice">Product Price:</label>
            <input type="number" step="0.01" class="form-control" id="productPrice" name="productPrice" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" required></textarea>
        </div>
        <div class="form-group">
            <label for="brandName">Brand Name:</label>
            <input type="text" class="form-control" id="brandName" name="brandName" required>
        </div>
        <div class="form-group">
            <label for="category">Category:</label>
            <select class="form-control" id="category" name="category" required>
                <option value="" disabled selected>Select a category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>">
                        <?php echo htmlspecialchars($cat); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="dosage">Dosage:</label>
            <select class="form-control" id="dosage" name="dosage" required>
                <option value="" disabled selected>Select a dosage</option>
                <?php foreach ($dosages as $dos): ?>
                    <option value="<?php echo htmlspecialchars($dos); ?>">
                        <?php echo htmlspecialchars($dos); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="stockQuantity">Stock Quantity:</label>
            <input type="number" class="form-control" id="stockQuantity" name="stockQuantity" required>
        </div>
        <div class="form-group">
            <label for="image">Product Image:</label>
            <input type="file" class="form-control" id="image" name="image" required>
        </div>
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="isAvailable" name="isAvailable" checked>
            <label class="form-check-label" for="isAvailable">Available</label>
        </div>
        <button type="submit" name="addProduct" class="btn btn-primary">Add Product</button>
    </form>
    <a href="logout.php" class="btn btn-secondary mt-3">Logout</a>
</div>
</body>
</html>
