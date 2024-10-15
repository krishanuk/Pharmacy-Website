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
$title = $_POST['title'];
$description = $_POST['description'];
$image = $_FILES['image']['name'];

if ($image) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $sql = "UPDATE healthcare_advice SET title='$title', description='$description', image='$image' WHERE id=$id";
    } else {
        echo "Sorry, there was an error uploading your file.";
        exit;
    }
} else {
    $sql = "UPDATE healthcare_advice SET title='$title', description='$description' WHERE id=$id";
}

if ($conn->query($sql) === TRUE) {
    echo "Advice updated successfully";
} else {
    echo "Error updating record: " . $conn->error;
}

$conn->close();
?>
