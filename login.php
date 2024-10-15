<?php
session_start();

// Display any messages set in the session
$message = isset($_SESSION['message']) ? $_SESSION['message'] : [];
unset($_SESSION['message']); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <title>Login Form</title>
    
</head>
<body>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg" style="width: 100%; max-width: 600px;">
            <div class="row g-0">
                <div class="col-md-6">
                    <img src="images/doctors.jpg" alt="Pharmacy" class="img-fluid w-100 h-100" style="object-fit: cover;">
                </div>
                <div class="col-md-6 p-4 d-flex flex-column justify-content-center">
                    <h3 class="mb-4 text-center">User Login</h3>

                    <!-- Display Messages -->
                    <?php if (!empty($message)): ?>
                        <?php foreach ($message as $msg): ?>
                            <div class="alert <?php echo ($msg == 'Registration successful') ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                                <?php echo htmlspecialchars($msg); ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <form action="loginsubmit.php" method="POST">
                        <div class="mb-3">
                            <label for="InputEmail" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="InputEmail" name="email" placeholder="Enter email" required>
                        </div>

                        <div class="mb-3">
                            <label for="InputPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="InputPassword" name="password" placeholder="Enter Password" required>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" name="submit" class="btn btn-primary">Login</button>
                            <button type="reset" class="btn btn-secondary">Clear</button>
                        </div>

                        <div class="text-center">
                            <p>Don't have an account? <a href="register.php">Register Now</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
