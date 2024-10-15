<?php
include('db_connection.php');
include('session.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $phone_number = $_POST['phone_number'];
    $street_address = $_POST['street_address'];
    $city = $_POST['city'];

    $sql = "UPDATE users SET phone_number=?, street_address=?, city=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $phone_number, $street_address, $city, $user_id);

    if ($stmt->execute()) {
        echo '<div class="alert alert-success" role="alert">Profile updated successfully.</div>';
        $_SESSION['phone_number'] = $phone_number;
        $_SESSION['street_address'] = $street_address;
        $_SESSION['city'] = $city;
    } else {
        echo '<div class="alert alert-danger" role="alert">Error: ' . $stmt->error . '</div>';
    }
    $stmt->close();
}

$conn->close(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">User Profile</h2>
        <form action="user_profile.php" method="POST" class="w-50 mx-auto">
            <div class="form-group">
                <label>Phone Number:</label>
                <input type="text" name="phone_number" class="form-control" value="<?php echo $_SESSION['phone_number']; ?>" required>
            </div>
            <div class="form-group">
                <label>Street Address:</label>
                <input type="text" name="street_address" class="form-control" value="<?php echo $_SESSION['street_address']; ?>" required>
            </div>
            <div class="form-group">
                <label>City:</label>
                <input type="text" name="city" class="form-control" value="<?php echo $_SESSION['city']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Update Profile</button>
        </form>
    </div>
</body>
</html>
