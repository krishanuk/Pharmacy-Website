<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pharmacy_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle file upload
$picturePath = '';
if (!empty($_FILES['picture']['name'])) {
    $target_dir = "images/";
    $target_file = $target_dir . basename($_FILES["picture"]["name"]);
    
    // Check if the directory exists, if not create it
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    if (move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file)) {
        $picturePath = $target_file;
    }
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO prescriptions (Name, Address, PhoneNumber, Email, PharmacyID, PicturePath) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $name, $address, $phone, $email, $pharmacyID, $picturePath);

$name = $_POST['name'];
$address = $_POST['address'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$pharmacyID = $_POST['branch']; // Assuming branch is mapped to PharmacyID

if ($stmt->execute()) {
    echo "New record created successfully";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
