<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    if (!isset($_POST['Username']) || !isset($_POST['Password']) || !isset($_POST['Email']) || !isset($_POST['PharmacyID'])) {
        echo "Error: Missing required fields.";
        exit;
    }

    $username = $_POST['Username'];
    $password = $_POST['Password']; 
    $email = $_POST['Email'];
    $pharmacyID = $_POST['PharmacyID'];

    // Validate
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Error: Invalid email format.";
        exit;
    }


    $pharmacyCheck = $conn->prepare("SELECT COUNT(*) FROM Pharmacies WHERE PharmacyID = ?");
    $pharmacyCheck->bind_param("i", $pharmacyID);
    $pharmacyCheck->execute();
    $pharmacyCheck->bind_result($pharmacyCount);
    $pharmacyCheck->fetch();
    $pharmacyCheck->close();

    if ($pharmacyCount == 0) {
        echo "Error: Pharmacy with ID $pharmacyID does not exist.";
        exit;
    }

 
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert the new pharmacist
    $sql = "INSERT INTO Pharmacists (Username, Email, Password, PharmacyID) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $username, $email, $hashedPassword, $pharmacyID);

    if ($stmt->execute()) {
        echo "Pharmacist created successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
