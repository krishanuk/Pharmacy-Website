<?php 
session_start();
$connection = mysqli_connect('localhost', 'root', '', 'pharmacy_db');
$message = [];

if(isset($_POST['submit'])){
    $user_name      = mysqli_real_escape_string($connection, trim($_POST['username']));
    $user_email     = mysqli_real_escape_string($connection, trim($_POST['email']));
    $user_password  = mysqli_real_escape_string($connection, trim($_POST['password']));
    $user_phone     = mysqli_real_escape_string($connection, trim($_POST['phone']));
    $user_street    = mysqli_real_escape_string($connection, trim($_POST['street']));
    $user_city      = mysqli_real_escape_string($connection, trim($_POST['city']));
    
    // Validation 
    if(empty($user_name)) {
        $message[] = 'Username is required!';
    } elseif(strlen($user_name) < 3 || strlen($user_name) > 50) {
        $message[] = 'Username must be between 3 and 50 characters!';
    }

    if(empty($user_email)) {
        $message[] = 'Email is required!';
    } elseif(!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        $message[] = 'Invalid email format!';
    }

    if(empty($user_password)) {
        $message[] = 'Password is required!';
    } elseif(strlen($user_password) < 8 || !preg_match('/[A-Z]/', $user_password) || !preg_match('/[0-9]/', $user_password)) {
        $message[] = 'Password must be at least 8 characters long and include at least one uppercase letter and one number!';
    }

    if(empty($user_phone)) {
        $message[] = 'Phone number is required!';
    } elseif(!preg_match('/^[0-9]{10}$/', $user_phone)) {
        $message[] = 'Invalid phone number! Must be 10 digits.';
    }

    if(empty($user_street)) {
        $message[] = 'Street address is required!';
    } elseif(strlen($user_street) < 5) {
        $message[] = 'Street address must be at least 5 characters long!';
    }

    if(empty($user_city)) {
        $message[] = 'City is required!';
    }

    
    if(empty($message)) {
        // Check if the email already exists in the database
        $select_all_users = mysqli_query($connection,"SELECT * FROM users WHERE email = '$user_email'") or die('query failed');
        $select_phone_check = mysqli_query($connection,"SELECT * FROM users WHERE phone_number = '$user_phone'") or die('query failed');

        if(mysqli_num_rows($select_all_users) > 0){
            $message[] = 'User already exists with this email!';
        } elseif (mysqli_num_rows($select_phone_check) > 0) {
            $message[] = 'User already exists with this phone number!';
        } else {
            // Hash the password 
            $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);
            $insert_query = "INSERT INTO users (username, email, password, phone_number, street_address, city) 
                             VALUES ('$user_name', '$user_email', '$hashed_password', '$user_phone', '$user_street', '$user_city')";

            if(mysqli_query($connection, $insert_query)){
                $message[] = 'Registration successful!';
                $_SESSION['message'] = $message;
                header('Location: login.php');
                exit();
            } else {
                $message[] = 'Registration failed! Please try again.';
            }
        }
    }

    
    $_SESSION['message'] = $message; 
    header('Location: register.php'); 
    exit();
}

mysqli_close($connection);
