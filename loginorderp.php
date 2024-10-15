<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate credentials
    $stmt = $conn->prepare("SELECT PharmacistID, PharmacyID FROM pharmacists WHERE Username = ? AND Password = ?");
    $stmt->bind_param("ss", $username, md5($password));
    $stmt->execute();
    $pharmacist = $stmt->get_result()->fetch_assoc();

    if ($pharmacist) {
        // Set session variables
        $_SESSION['pharmacist_id'] = $pharmacist['PharmacistID'];
        $_SESSION['pharmacy_id'] = $pharmacist['PharmacyID'];

        // Redirect to the order management dashboard
        header("Location: order_management.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pharmacist Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Pharmacist Login</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="post" action="loginorderp.php">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>
</body>
</html>
