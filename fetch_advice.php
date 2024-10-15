<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pharmacy_db";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$query = isset($_GET['query']) ? $_GET['query'] : '';


$sql = "SELECT id, title, description, image FROM healthcare_advice";
if (!empty($query)) {
    $sql .= " WHERE title LIKE '%$query%' OR description LIKE '%$query%'";
}


$result = $conn->query($sql);

$adviceList = array();


if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $adviceList[] = $row;
    }
}


echo json_encode($adviceList);


$conn->close();
?>
