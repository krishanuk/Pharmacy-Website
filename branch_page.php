<?php
session_start();
include 'db_connection.php';

// Enable error reporting for MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Check if Pharmacy ID is provided and valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<div class='alert-danger'>Invalid Pharmacy ID.</div>");
}

$pharmacyID = intval($_GET['id']); // Ensure it's an integer

// Fetch pharmacy details
$pharmacyStmt = $conn->prepare("SELECT * FROM pharmacies WHERE PharmacyID = ?");
$pharmacyStmt->bind_param("i", $pharmacyID);
$pharmacyStmt->execute();
$pharmacy = $pharmacyStmt->get_result()->fetch_assoc();

if (!$pharmacy) {
    die("<div class='alert-danger'>Pharmacy not found.</div>");
}

// Fetch filter options for Brand and Dosage
$brandStmt = $conn->prepare("SELECT DISTINCT BrandName FROM products WHERE PharmacyID = ?");
$brandStmt->bind_param("i", $pharmacyID);
$brandStmt->execute();
$brands = $brandStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$brandStmt->close();

$dosageStmt = $conn->prepare("SELECT DISTINCT Dosage FROM products WHERE PharmacyID = ?");
$dosageStmt->bind_param("i", $pharmacyID);
$dosageStmt->execute();
$dosages = $dosageStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$dosageStmt->close();

// Initialize filters as empty arrays if not provided
$brandsFilter = isset($_GET['brand']) ? $_GET['brand'] : [];
$dosagesFilter = isset($_GET['dosage']) ? $_GET['dosage'] : [];
$searchTerm = isset($_GET['search']) ? "%" . $_GET['search'] . "%" : null;

// Ensure the filters are arrays to prevent errors
if (!is_array($brandsFilter)) {
    $brandsFilter = [];
}
if (!is_array($dosagesFilter)) {
    $dosagesFilter = [];
}

// Prepare the base query
$query = "SELECT p.ProductID, p.ProductName, p.Description, p.BrandName, p.Dosage, p.Image, 
                 i.InventoryID, i.Quantity, i.UnitPrice, i.BatchNumber, i.IsAvailable, i.ExpiryDate
          FROM products p
          JOIN inventory i ON p.ProductID = i.ProductID
          WHERE i.PharmacyID = ? AND i.Quantity > 0 AND i.IsAvailable = 1";

// Bind dynamic parameters
$bindParams = [$pharmacyID]; 
$bindTypes = 'i'; 

// Add brand filter if selected
if (!empty($brandsFilter)) {
    $brandPlaceholders = implode(',', array_fill(0, count($brandsFilter), '?'));
    $query .= " AND p.BrandName IN ($brandPlaceholders)";
    $bindParams = array_merge($bindParams, $brandsFilter);
    $bindTypes .= str_repeat('s', count($brandsFilter)); 
}

// Add dosage filter if selected
if (!empty($dosagesFilter)) {
    $dosagePlaceholders = implode(',', array_fill(0, count($dosagesFilter), '?'));
    $query .= " AND p.Dosage IN ($dosagePlaceholders)";
    $bindParams = array_merge($bindParams, $dosagesFilter);
    $bindTypes .= str_repeat('s', count($dosagesFilter)); // Add string types for dosages
}

// Add search term if provided
if (!empty($searchTerm)) {
    $query .= " AND (p.ProductName LIKE ? OR p.Description LIKE ?)";
    $bindParams[] = $searchTerm;
    $bindParams[] = $searchTerm;
    $bindTypes .= 'ss'; 
}

// Complete the query with ordering
$query .= " AND i.InventoryID = (
                SELECT i2.InventoryID 
                FROM inventory i2 
                WHERE i2.ProductID = p.ProductID 
                AND i2.Quantity > 0 
                AND i2.IsAvailable = 1 
                ORDER BY i2.ExpiryDate ASC LIMIT 1
            )
            ORDER BY i.ExpiryDate ASC";

// Prepare statement
$productStmt = $conn->prepare($query);

// Bind parameters dynamically
if (!empty($bindParams)) {
    $productStmt->bind_param($bindTypes, ...$bindParams);
}

$productStmt->execute();
$products = $productStmt->get_result()->fetch_all(MYSQLI_ASSOC);

$productStmt->close();
$conn->close();

// Check product added successfully
$successMessage = "";
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $successMessage = "<div class='toast-notification' id='success-message'>Product added to cart successfully!</div>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($pharmacy['PharmacyName']); ?> - Products</title>
    <link rel="stylesheet" href="CSS/pharmacy.css">
    <link rel="stylesheet" href="CSS/branch_page_style.css">

    <script>
        // JavaScript to hide the success message after 3 seconds and redirect
        document.addEventListener('DOMContentLoaded', function() {
            var successMessage = document.getElementById('success-message');
            if (successMessage) {
                setTimeout(function() {
                    successMessage.classList.add('hidden');
                    
                    window.location.href = "branch_page.php?id=<?php echo $pharmacyID; ?>";
                }, 3000); // Hide and redirect after 3 seconds
            }
        });
    </script>
</head>

<body>
    <div id="pharmacy-container">
        <header id="pharmacy-header">
            <a href="index.php" style="text-decoration: none; color: #fff;">
                <h1><?php echo htmlspecialchars($pharmacy['PharmacyName']); ?></h1>
            </a>
            <p><?php echo htmlspecialchars($pharmacy['Address']); ?></p>
            <p>Phone: <?php echo htmlspecialchars($pharmacy['PhoneNumber']); ?> | Email: <?php echo htmlspecialchars($pharmacy['Email']); ?></p>
        </header>

        <main id="main-content">
            <div id="content-wrapper">

                <!-- Display success message product was added successfully -->
                <?php echo $successMessage; ?>

                <aside id="filter-sidebar">
                    <form action="branch_page.php" method="GET" id="filter-form">
                        <input type="hidden" name="id" value="<?php echo $pharmacyID; ?>">

                        <div class="filter-group">
                            <h3>Search</h3>
                            <input type="text" name="search" placeholder="Search products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        </div>

                        <div class="filter-group">
                            <h3>Brand</h3>
                            <?php foreach ($brands as $brand): ?>
                                <label>
                                    <input type="checkbox" name="brand[]" value="<?php echo htmlspecialchars($brand['BrandName']); ?>"
                                        <?php echo in_array($brand['BrandName'], $brandsFilter) ? 'checked' : ''; ?>>
                                    <?php echo htmlspecialchars($brand['BrandName']); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <div class="filter-group">
                            <h3>Dosage</h3>
                            <?php foreach ($dosages as $dosage): ?>
                                <label>
                                    <input type="checkbox" name="dosage[]" value="<?php echo htmlspecialchars($dosage['Dosage']); ?>"
                                        <?php echo in_array($dosage['Dosage'], $dosagesFilter) ? 'checked' : ''; ?>>
                                    <?php echo htmlspecialchars($dosage['Dosage']); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <button type="submit">Apply Filters</button>
                    </form>
                </aside>

                <section id="products-section">
                    <h2 id="products-heading">Available Products</h2>
                    <div id="products-grid">
                        <?php if (!empty($products)): ?>
                            <?php foreach ($products as $product): ?>
                                <div class="product-card">
                                    <div class="product-image">
                                        <?php if (!empty($product['Image'])): ?>
                                            <img src="../images/<?php echo htmlspecialchars($product['Image']); ?>" alt="<?php echo htmlspecialchars($product['ProductName']); ?>">
                                        <?php else: ?>
                                            <img src="../images/default.jpg" alt="Default Image">
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-details">
                                        <h3 class="product-name">
                                            <?php echo htmlspecialchars($product['ProductName']); ?>
                                            (Batch: <?php echo htmlspecialchars($product['BatchNumber']); ?>)
                                        </h3>
                                        <p class="product-price">$<?php echo htmlspecialchars(number_format($product['UnitPrice'], 2)); ?></p>
                                        <p class="product-description"><?php echo htmlspecialchars($product['Description']); ?></p>
                                        <p class="product-brand">Brand: <?php echo htmlspecialchars($product['BrandName']); ?></p>
                                        <p class="product-dosage">Dosage: <?php echo htmlspecialchars($product['Dosage']); ?></p>
                                        <p class="product-stock">Stock: <?php echo htmlspecialchars($product['Quantity']); ?></p>
                                        <p class="product-status">Status: <?php echo $product['IsAvailable'] ? 'Available' : 'Out of Stock'; ?></p>
                                    </div>
                                    <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
                                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['ProductID']); ?>">
                                        <input type="hidden" name="inventory_id" value="<?php echo htmlspecialchars($product['InventoryID']); ?>">
                                        <input type="hidden" name="pharmacy_id" value="<?php echo htmlspecialchars($pharmacyID); ?>"> <!-- Include Pharmacy ID -->
                                        <input type="number" name="quantity" min="1" max="<?php echo htmlspecialchars($product['Quantity']); ?>" required placeholder="Quantity">
                                        <button type="submit">Add to Cart</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-products">
                                <p>No products found.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>

</html>