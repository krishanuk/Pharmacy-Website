<?php
session_start();
$connection = mysqli_connect('localhost', 'root', '', 'pharmacy_db');

if (!$connection) {
    die('Connection failed: ' . mysqli_connect_error());
}

$message = [];

if (isset($_POST['submit'])) {
    
    $username = mysqli_real_escape_string($connection, trim($_POST['email']));
    $password = mysqli_real_escape_string($connection, trim($_POST['password']));

    // Validate inputs
    if (empty($username)) {
        $message[] = 'Email is required!';
    } elseif (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
        $message[] = 'Invalid email format!';
    }

    if (empty($password)) {
        $message[] = 'Password is required!';
    }

    if (empty($message)) {
        // Check if the user is in the Admin table
        $select_admin = "SELECT * FROM Admin WHERE Email = ?";
        $stmt_admin = mysqli_prepare($connection, $select_admin);
        mysqli_stmt_bind_param($stmt_admin, 's', $username);
        mysqli_stmt_execute($stmt_admin);
        $result_admin = mysqli_stmt_get_result($stmt_admin);

        if ($result_admin && mysqli_num_rows($result_admin) > 0) {
            $admin = mysqli_fetch_assoc($result_admin);
            if (password_verify($password, $admin['Password'])) {
                // Set admin session variables
                $_SESSION['admin_id'] = $admin['AdminID'];
                $_SESSION['user_email'] = $admin['Email'];
                header('Location: pharmacyadmin.php');
                exit();
            } else {
                $message[] = 'Invalid password for admin!';
            }
        } else {
            // check if the user is in the Pharmacists table
            $select_pharmacist = "SELECT * FROM pharmacists WHERE Email = ?";
            $stmt_pharmacist = mysqli_prepare($connection, $select_pharmacist);
            mysqli_stmt_bind_param($stmt_pharmacist, 's', $username);
            mysqli_stmt_execute($stmt_pharmacist);
            $result_pharmacist = mysqli_stmt_get_result($stmt_pharmacist);

            if ($result_pharmacist && mysqli_num_rows($result_pharmacist) > 0) {
                $pharmacist = mysqli_fetch_assoc($result_pharmacist);
                if (password_verify($password, $pharmacist['Password'])) {
                    // Create session
                    $_SESSION['pharmacist_id'] = $pharmacist['PharmacistID'];
                    $_SESSION['user_email'] = $pharmacist['Email'];
                    $_SESSION['pharmacy_id'] = $pharmacist['PharmacyID']; 
                    header('Location: dashboardinvent.php'); 
                    exit();
                } else {
                    $message[] = 'Invalid password for pharmacist!';
                }
            } else {
                //check if the user is in the Users table
                $select_user = "SELECT * FROM users WHERE email = ?";
                $stmt_user = mysqli_prepare($connection, $select_user);
                mysqli_stmt_bind_param($stmt_user, 's', $username);
                mysqli_stmt_execute($stmt_user);
                $result_user = mysqli_stmt_get_result($stmt_user);

                if ($result_user && mysqli_num_rows($result_user) > 0) {
                    $user = mysqli_fetch_assoc($result_user);
                    if (password_verify($password, $user['password'])) {

                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['user_email'] = $user['email'];
                        header('Location: index.php'); 
                        exit();
                    } else {
                        $message[] = 'Invalid password for user!';
                    }
                } else {
                    $message[] = 'No matching account found!';
                }
            }
        }
    }

    $_SESSION['message'] = $message;
    header('Location: login.php');
    exit();
}

mysqli_close($connection);
?>
