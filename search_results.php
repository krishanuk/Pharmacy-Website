<?php
include 'db_connection.php';

$search_query = "";
$products = array();

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_query = $_GET['search'];
    
    $stmt = $conn->prepare("
        SELECT products.*, pharmacies.PharmacyName, inventory.UnitPrice 
        FROM products 
        JOIN pharmacies ON products.PharmacyID = pharmacies.PharmacyID 
        JOIN inventory ON products.ProductID = inventory.ProductID 
        WHERE (ProductName LIKE ? OR BrandName LIKE ? OR Category LIKE ?)
    ");
    $like_search_query = "%" . $search_query . "%";
    $stmt->bind_param("sss", $like_search_query, $like_search_query, $like_search_query);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch the results
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();
} else {
    echo "<script>alert('Please enter a search term.');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - DEXCARE Pharmacy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/search_result_styles.css">
</head>
<body>

    <header>
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand" href="index.php">DEXCARE Pharmacy</a>
                <!-- Search form to allow user to search again from the search results page -->
                <form class="form-inline mx-auto row" action="search_results.php" method="GET" style="max-width: 600px;">
                    <div class="col-9">
                        <input class="form-control w-100" type="search" name="search" placeholder="Search products..." aria-label="Search" value="<?php echo htmlspecialchars($search_query); ?>">
                    </div>
                    <div class="col-3">
                        <button class="btn btn-outline-light w-100" type="submit">Search</button>
                    </div>
                </form>
            </div>
        </nav>
    </header>

    <div class="container mt-4">
        <h2>Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h2>
        <div class="row">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <img class="card-img-top" src="../images/<?php echo htmlspecialchars($product['Image']); ?>" alt="Product Image">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['ProductName']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($product['Description']); ?></p>
                                <p class="card-text"><strong>Brand:</strong> <?php echo htmlspecialchars($product['BrandName']); ?></p>
                                <p class="card-text"><strong>Pharmacy:</strong> <?php echo htmlspecialchars($product['PharmacyName']); ?></p> <!-- Pharmacy Name -->
                                <p class="card-text"><strong>Price:</strong> $<?php echo htmlspecialchars(number_format($product['UnitPrice'], 2)); ?></p> <!-- Inventory Price -->
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">
                    <p>No products found for "<?php echo htmlspecialchars($search_query); ?>"</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
