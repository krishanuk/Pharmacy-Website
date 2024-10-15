<?php
session_start();
$connection = mysqli_connect('localhost', 'root', '', 'pharmacy_db');

$message = [];

if (isset($_POST['submit'])) {
    //validate input fields
    $username = mysqli_real_escape_string($connection, trim($_POST['username']));
    $email = mysqli_real_escape_string($connection, trim($_POST['email']));
    $password = mysqli_real_escape_string($connection, trim($_POST['password']));

    // validation
    if (empty($username) || empty($email) || empty($password)) {
        $message[] = 'All fields are required!';
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,25}$/', $username)) {
        $message[] = 'Username must be 3-25 characters long and can only contain letters, numbers, and underscores!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message[] = 'Invalid email format!';
    } else {
        // Split the email to get the domain and check MX records
        $email_parts = explode("@", $email);
        if (!checkdnsrr(array_pop($email_parts), "MX")) {
            $message[] = 'Invalid email domain!';
        } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
            $message[] = 'Password must be at least 8 characters long, contain at least one uppercase letter, one number, and one special character!';
        } else {
            //prevent SQL injection
            $stmt = $connection->prepare("SELECT * FROM admin WHERE email = ?");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $message[] = 'Admin already exists!';
            } else {
                // Hash the password 
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert data using prepared statements
                $insert_stmt = $connection->prepare("INSERT INTO admin (Username, Email, Password) VALUES (?, ?, ?)");
                $insert_stmt->bind_param('sss', $username, $email, $hashed_password);

                if ($insert_stmt->execute()) {
                    $message[] = 'Admin added successfully!';
                } else {
                    $message[] = 'Admin addition failed! Please try again.';
                }

                $insert_stmt->close();
            }

            $stmt->close();
        }
    }

    $_SESSION['message'] = $message;
}

mysqli_close($connection);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Add Admin</title>
    <style>
        body {
            background: linear-gradient(to right, #e6f4f1, #cdeff3);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .form-container {
            max-width: 450px;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
        }

        .form-container h3 {
            color: #2e8b57;
            font-weight: bold;
            margin-bottom: 25px;
        }

        .btn-custom {
            background-color: #2e8b57;
            color: white;
            transition: all 0.3s;
        }

        .btn-custom:hover {
            background-color: #218c74;
        }

        .btn-clear {
            background-color: #007bff;
            color: white;
            transition: all 0.3s;
        }

        .btn-clear:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h3 class="text-center">Add Admin</h3>

        <?php
        if (!empty($message)) {
            foreach ($message as $msg) {
                echo '<div class="alert alert-info">' . $msg . '</div>';
            }
        }
        ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label for="InputUsername" class="form-label">Username</label>
                <input type="text" class="form-control" id="InputUsername" name="username" pattern="^[a-zA-Z0-9_]{3,25}$" title="Username must be between 3-25 characters long and can only contain letters, numbers, and underscores." placeholder="Enter Username" required>
            </div>

            <div class="mb-3">
                <label for="InputEmail" class="form-label">Email</label>
                <input type="email" class="form-control" id="InputEmail" name="email" placeholder="Enter Email" required>
            </div>

            <div class="mb-3">
                <label for="InputPassword" class="form-label">Password</label>
                <input type="password" class="form-control" id="InputPassword" name="password" pattern="^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" title="Password must be at least 8 characters long, contain at least one uppercase letter, one number, and one special character." placeholder="Enter Password" required>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <button type="submit" name="submit" class="btn btn-custom w-100"><i class="fas fa-user-plus"></i> Add Admin</button>
                </div>
                <div class="col">
                    <button type="reset" class="btn btn-clear w-100"><i class="fas fa-eraser"></i> Clear</button>
                </div>
            </div>
        </form>
    </div>
</body>

</html> 
