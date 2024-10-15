<?php
session_start();
$connection = mysqli_connect('localhost', 'root', '', 'pharmacy_db');

if (!$connection) {
    die('Connection failed: ' . mysqli_connect_error());
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit(); 
}

$user_id = $_SESSION['user_id'];
$message = [];

// Fetch user details
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    $message[] = "User not found.";
}

//updating user details
if (isset($_POST['update_profile'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $street_address = trim($_POST['street_address']);
    $city = trim($_POST['city']);
    
    // Check if an image was uploaded
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $image = file_get_contents($_FILES['profile_image']['tmp_name']); 

        $update_query = "UPDATE users SET username=?, email=?, phone_number=?, street_address=?, city=?, image=? WHERE user_id=?";
        $stmt_update = $connection->prepare($update_query);
        $stmt_update->bind_param("ssssssi", $username, $email, $phone_number, $street_address, $city, $image, $user_id);
    } else {
        // Update user details without the image
        $update_query = "UPDATE users SET username=?, email=?, phone_number=?, street_address=?, city=? WHERE user_id=?";
        $stmt_update = $connection->prepare($update_query);
        $stmt_update->bind_param("sssssi", $username, $email, $phone_number, $street_address, $city, $user_id);
    }

    if ($stmt_update->execute()) {
        $message[] = "Profile updated successfully!";
    
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
    } else {
        $message[] = "Error updating profile.";
    }

    $stmt_update->close();
}

mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/updateuser.css">
    <style>
       
        /* Profile Image Style */
        .profile-image {
            display: block;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0 auto 20px;
            background-image: url('data:image/jpeg;base64,<?php echo isset($user['image']) && !empty($user['image']) ? base64_encode($user['image']) : 'https://via.placeholder.com/120'; ?>');
            background-size: cover;
            background-position: center;
            border: 2px solid var(--primary-color); 
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="profile-card">
                <!-- Profile Image -->
                <div class="profile-image"></div>

                <h2 class="text-center mb-4">User Profile</h2>

                <!-- Display Messages -->
                <?php if (!empty($message)): ?>
                    <div class="alert alert-info">
                        <?php foreach ($message as $msg): ?>
                            <p><?php echo $msg; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- User Profile Form -->
                <form action="updateuser.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="street_address" class="form-label">Street Address</label>
                        <input type="text" class="form-control" id="street_address" name="street_address" value="<?php echo htmlspecialchars($user['street_address']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="city" class="form-label">City</label>
                        <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($user['city']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="profile_image" class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                    </div>

                    <div class="text-center d-flex justify-content-between">
                        <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>

                        <a href="view_order_users.php?user_id=<?php echo $user_id; ?>" class="btn btn-secondary">View My Orders</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
