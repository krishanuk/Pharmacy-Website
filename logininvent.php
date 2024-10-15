<?php
session_start();
include 'db_connection.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $loginInput = $_POST['loginInput']; 
    $password = $_POST['password'];

   
    $stmt = $conn->prepare("SELECT PharmacistID, Password, PharmacyID FROM Pharmacists WHERE Username = ? OR Email = ?");
    $stmt->bind_param("ss", $loginInput, $loginInput);
    $stmt->execute();
    $stmt->bind_result($pharmacistID, $hashedPassword, $pharmacyID);
    $stmt->fetch();
    $stmt->close();

    // Verify password and handle login
    if (password_verify($password, $hashedPassword)) {
        $_SESSION['pharmacist_id'] = $pharmacistID;
        $_SESSION['pharmacy_id'] = $pharmacyID;
        header("Location: dashboardinvent.php");
        exit;
    } else {
        $error = "Invalid login credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacist Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Pharmacist Login</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="loginInput">Username or Email:</label>
            <input type="text" class="form-control" id="loginInput" name="loginInput" required>
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
