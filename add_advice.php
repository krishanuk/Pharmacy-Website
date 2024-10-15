<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pharmacy_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$title = $_POST['title'];
$description = $_POST['description'];
$image = $_FILES['image']['name'];

$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["image"]["name"]);

if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
    $sql = "INSERT INTO healthcare_advice (title, description, image) VALUES ('$title', '$description', '$image')";

    if ($conn->query($sql) === TRUE) {
        echo "New advice added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Sorry, there was an error uploading your file.";
}

$conn->close();
?>
