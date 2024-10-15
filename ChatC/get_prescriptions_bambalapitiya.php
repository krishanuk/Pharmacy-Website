<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pharmacy_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch prescription details for Nugegoda (branch ID = 5)
$sql = "SELECT p.PrescriptionID, p.Name, p.Address, p.PhoneNumber, p.Email, p.PicturePath, ph.PharmacyName
        FROM prescriptions p
        JOIN pharmacies ph ON p.PharmacyID = ph.PharmacyID
        WHERE p.PharmacyID = 5";

$result = $conn->query($sql);

$data = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($data);

$conn->close();
?>
