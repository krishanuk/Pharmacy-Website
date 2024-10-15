<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pharmacy_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$prescriptionId = isset($_GET['prescriptionId']) ? intval($_GET['prescriptionId']) : 0;

$sql = "SELECT p.PrescriptionID, p.Name, p.Address, p.PhoneNumber, p.Email, p.PicturePath, ph.PharmacyName
        FROM prescriptions p
        JOIN pharmacies ph ON p.PharmacyID = ph.PharmacyID
        WHERE p.PrescriptionID = $prescriptionId";

$result = $conn->query($sql);

$data = null;
if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
}

header('Content-Type: application/json');
echo json_encode($data);

$conn->close();
?>
