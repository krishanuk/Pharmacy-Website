<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pharmacy_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_POST['id'];

$sql = "DELETE FROM healthcare_advice WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    echo "Advice deleted successfully";
} else {
    echo "Error deleting record: " . $conn->error;
}

$conn->close();
?>
