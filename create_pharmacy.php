<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pharmacyName = $_POST['pharmacyName'];
    $address = $_POST['address'];
    $phoneNumber = $_POST['phoneNumber'];
    $email = $_POST['email'];

    $sql = "INSERT INTO Pharmacies (PharmacyName, Address, PhoneNumber, Email) 
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $pharmacyName, $address, $phoneNumber, $email);

    if ($stmt->execute()) {
        echo "New pharmacy created successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
