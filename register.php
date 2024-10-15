<?php
session_start();
$message = isset($_SESSION['message']) ? $_SESSION['message'] : [];
unset($_SESSION['message']); 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <title>User Registration Form</title>
</head>

<body>
    <div class="container h-100 d-flex justify-content-center align-items-center Rcontents">
        <div class="row w-100 shadow-lg rounded bg-white Rforms">
            <?php
            if (!empty($message)) {
                foreach ($message as $msg) {
                    if ($msg == 'Registration successful!') {
                        echo '<div class="alert alert-success">' . $msg . '</div>';
                    } else {
                        echo '<div class="alert alert-danger">' . $msg . '</div>';
                    }
                }
            }
            ?>
            <div class="col-md-6 main">
                <img src="images/doctors.jpg" alt="Doctor" class="Rimg img-fluid rounded-start w-100 h-100">
            </div>
            <div class="col-md-6 p-4 d-flex flex-column justify-content-center">
                <h3 class="mb-4 text-center">User Registration Form</h3>

                <form action="registrationsubmit.php" method="POST">
                    <div class="mb-3">
                        <label for="InputName" class="form-label">User Name</label>
                        <input type="text" class="form-control" id="InputName" name="username" placeholder="Enter Name" required>
                    </div>

                    <div class="mb-3">
                        <label for="InputEmail1" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="InputEmail1" name="email" placeholder="Enter email" required>
                    </div>

                    <div class="mb-3">
                        <label for="InputPassword" class="form-label">Enter Password</label>
                        <input type="password" class="form-control" id="InputPassword" name="password" placeholder="Enter Password" required>
                    </div>

                    <div class="mb-3">
                        <label for="InputPhone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="InputPhone" name="phone" placeholder="Enter Phone Number" required>
                    </div>

                    <div class="mb-3">
                        <label for="InputStreet" class="form-label">Street Address</label>
                        <input type="text" class="form-control" id="InputStreet" name="street" placeholder="Enter Street Address" required>
                    </div>

                    <div class="mb-3">
                        <label for="InputCity" class="form-label">City</label>
                        <input type="text" class="form-control" id="InputCity" name="city" placeholder="Enter City" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <button type="submit" name="submit" class="btn btn-success w-100">Register</button>
                        </div>
                        <div class="col">
                            <button type="reset" class="btn btn-secondary w-100">Clear</button>
                        </div>
                    </div>

                    <div class="text-center">
                        <p>Already have an account? <a href="login.php">Sign in Now</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
