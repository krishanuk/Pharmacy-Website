<?php
session_start();
include("./config.php");

$token = $_POST["stripeToken"];
$contact_name = $_POST["c_name"];
$token_card_type = $_POST["stripeTokenType"];
$phone = $_POST["phone"];
$email = $_POST["stripeEmail"];
$address = $_POST["address"];
$amount_inr = $_POST["amount_inr"];
$desc = $_POST["product_name"];

// Store customer details in session
$_SESSION['customer_name'] = $contact_name;
$_SESSION['customer_address'] = $address;
$_SESSION['contact_number'] = $phone;
$_SESSION['email'] = $email;

// Ensure the amount is valid
if ($amount_inr < 50) {
    echo "<div class='alert alert-danger'>The minimum amount for Stripe payments is $50. Please adjust your cart.</div>";
    exit();
}

$charge = \Stripe\Charge::create([
    "amount" => str_replace(",", "", $amount_inr) * 100,
    "currency" => 'inr',
    "description" => $desc,
    "source" => $token,
]);

if ($charge) {
    // Redirect to success page
    header("Location: success.php?amount=$amount_inr");
    exit();
} else {
    echo "Payment failed. Please try again.";
}
