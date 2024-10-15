<?php

include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pharmacistID = $_POST['PharmacistID'];
    $pharmacyID = $_POST['PharmacyID'];


    $pharmacistCheck = $conn->prepare("SELECT COUNT(*) FROM Pharmacists WHERE PharmacistID = ?");
    $pharmacistCheck->bind_param("i", $pharmacistID);
    $pharmacistCheck->execute();
    $pharmacistCheck->bind_result($pharmacistCount);
    $pharmacistCheck->fetch();
    $pharmacistCheck->close();

    if ($pharmacistCount == 0) {
        echo "Error: Pharmacist with ID $pharmacistID does not exist.";
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

    // Update the pharmacist assignment
    $sql = "UPDATE Pharmacies SET PharmacistID = ? WHERE PharmacyID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $pharmacistID, $pharmacyID);

    if ($stmt->execute()) {
        echo "Pharmacist assigned to pharmacy successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
}
