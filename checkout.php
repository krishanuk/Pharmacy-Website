<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the cart items from the database
$sql = "
    SELECT
        c.CartID,
        p.ProductID,
        c.InventoryID,
        c.PharmacyID,
        p.ProductName,
        i.UnitPrice,
        c.Quantity,
        i.Quantity AS StockQuantity,  
        p.Image,
        ph.PharmacyName,
        (i.UnitPrice * c.Quantity) AS TotalPrice
    FROM Cart c
    INNER JOIN Products p ON c.ProductID = p.ProductID
    INNER JOIN inventory i ON c.InventoryID = i.InventoryID
    INNER JOIN pharmacies ph ON c.PharmacyID = ph.PharmacyID
    WHERE c.UserID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);

// Check if the cart is empty
if (empty($cart_items)) {
    echo "<div class='alert alert-danger'>Your cart is empty.</div>";
    exit();
}

// Calculate total price
$total_price = 0;
$product_name = "Multiple Items"; 
$product_image = ""; 

if (count($cart_items) === 1) {
    // If there's only one item, use its name and image
    $item = $cart_items[0];
    $product_name = $item['ProductName'];
    $product_image = !empty($item['Image']) ? $item['Image'] : '../images/default.jpg'; 
    $total_price = $item['Quantity'] * $item['UnitPrice'];
} else {
    // Loop through the cart to calculate the total price
    foreach ($cart_items as $item) {
        $total_price += $item['Quantity'] * $item['UnitPrice'];
    }
}

// Pretend the total 
$total_price_usd_display = number_format($total_price, 2); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Integration (Stripe)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-container {
            margin-bottom: 20px;
        }
        h4 {
            color: #333;
            margin-bottom: 20px;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        button.back {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 20px;
        }
        button.back:hover {
            background-color: #2980b9;
        }
        .checkout-container {
            margin-top: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .checkout-container img {
            max-width: 100px;
            display: block;
            margin: 10px 0;
        }
        .checkout-container span {
            font-size: 18px;
            color: #333;
        }
    </style>
</head>
<body>
<div class="container">
    <button type="button" onclick="goback()" class="back">Go Back</button> 
    <div class="row">
        <div class="col-md-6">
            <div class="form-container">
                <form autocomplete="off" action="checkout-charge.php" method="POST">
                    <div>
                        <label>Customer Name</label>
                        <input type="text" name="c_name" required/>
                    </div>
                    <div>
                        <label>Address</label>
                        <input type="text" name="address" required/>
                    </div>
                    <div>
                        <label>Contact Number</label>
                        <input type="number" id="ph" name="phone" pattern="\d{10}" maxlength="10" required/>
                    </div>
                    <div>
                        <label>Product Name</label>
                        <input type="text" name="product_name" value="<?php echo htmlspecialchars($product_name); ?>" disabled required/>
                    </div>
                    <div>
                        <label>Price in USD (display only)</label>
                        <input type="text" name="price" value="$<?php echo htmlspecialchars($total_price_usd_display); ?>" disabled required/>
                    </div>
                   
                    <input type="hidden" name="amount_inr" value="<?php echo htmlspecialchars($total_price); ?>"> <!-- Use INR for payment -->
                    <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product_name); ?>">
                    
                    <script
                    src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                    data-key="pk_test_51Q4YtKB2GV3voZGNaqTVoGF16ihTeLaqHh0K1Eg3msOiocuRBLb2fOEgbL79aD9Oxh2agXOIWCUSZSSlyl4Rew8N00DGwnoLx7"
                    data-amount="<?php echo $total_price * 100; ?>" <!-- Charge in INR -->
                    data-name="<?php echo htmlspecialchars($product_name); ?>"
                    data-description="<?php echo htmlspecialchars($product_name); ?>"
                    data-image="<?php echo htmlspecialchars($product_image); ?>"
                    data-currency="inr"
                    data-locale="auto">
                    </script>
                </form>
            </div>
        </div>
        <div class="col-md-6">
            <div class="checkout-container">
                <h4>Product Name&nbsp;:&nbsp;<?php echo htmlspecialchars($product_name); ?></h4>
                <?php if (!empty($product_image)): ?>
                    <img src="<?php echo htmlspecialchars($product_image); ?>" alt="Product Image"/>
                <?php endif; ?>
                <span>Price&nbsp;:&nbsp;$<?php echo $total_price_usd_display; ?> USD</span> <!-- Display as USD -->
            </div>
        </div>
    </div>
</div>

<script>
    function goback(){
        window.history.go(-1);
    }

    $('#ph').on('keypress',function(){
         var text = $(this).val().length;
         if(text > 9){
              return false;
         }else{
            $('#ph').text($(this).val());
         }
    });
</script>
</body>
</html>
