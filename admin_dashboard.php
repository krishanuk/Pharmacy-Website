<?php
session_start();
include 'db_connection.php';

$errors = [];

// Handle create pharmacy request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_pharmacy'])) {
    $pharmacyName = trim($_POST['pharmacyName']);
    $address = trim($_POST['address']);
    $phoneNumber = trim($_POST['phoneNumber']);
    $email = trim($_POST['email']);

    if (empty($pharmacyName)) {
        $errors[] = "Pharmacy name is required.";
    }
    if (empty($address)) {
        $errors[] = "Address is required.";
    }
    if (!preg_match("/^[0-9]{10}$/", $phoneNumber)) {
        $errors[] = "Phone number must be 10 digits.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO Pharmacies (PharmacyName, Address, PhoneNumber, Email) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $pharmacyName, $address, $phoneNumber, $email);
        $stmt->execute();
        $stmt->close();

        $_SESSION['success_message'] = "Pharmacy created successfully!";
        header("Location: admin_dashboard.php");
        exit;
    }
}

// Handle create pharmacist request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_pharmacist'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $pharmacyID = $_POST['pharmacyID'] ?: null;

    // Server-side validation
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO Pharmacists (Username, Email, Password, PharmacyID) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $username, $email, $hashedPassword, $pharmacyID);
        $stmt->execute();
        $stmt->close();

        $_SESSION['success_message'] = "Pharmacist created successfully!";
        header("Location: admin_dashboard.php");
        exit;
    }
}

// Handle delete pharmacy request
if (isset($_GET['delete_pharmacy'])) {
    $pharmacyID = $_GET['delete_pharmacy'];

    $stmt = $conn->prepare("DELETE FROM Pharmacies WHERE PharmacyID = ?");
    $stmt->bind_param("i", $pharmacyID);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success_message'] = "Pharmacy deleted successfully!";
    header("Location: admin_dashboard.php");
    exit;
}

// Handle delete pharmacist request
if (isset($_GET['delete_pharmacist'])) {
    $pharmacistID = $_GET['delete_pharmacist'];

    $stmt = $conn->prepare("DELETE FROM Pharmacists WHERE PharmacistID = ?");
    $stmt->bind_param("i", $pharmacistID);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success_message'] = "Pharmacist deleted successfully!";
    header("Location: admin_dashboard.php");
    exit;
}

// Handle update pharmacy request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_pharmacy'])) {
    $pharmacyID = $_POST['pharmacyID'];
    $pharmacyName = trim($_POST['pharmacyName']);
    $address = trim($_POST['address']);
    $phoneNumber = trim($_POST['phoneNumber']);
    $email = trim($_POST['email']);

    if (empty($pharmacyName)) {
        $errors[] = "Pharmacy name is required.";
    }
    if (empty($address)) {
        $errors[] = "Address is required.";
    }
    if (!preg_match("/^[0-9]{10}$/", $phoneNumber)) {
        $errors[] = "Phone number must be 10 digits.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE Pharmacies SET PharmacyName = ?, Address = ?, PhoneNumber = ?, Email = ? WHERE PharmacyID = ?");
        $stmt->bind_param("ssssi", $pharmacyName, $address, $phoneNumber, $email, $pharmacyID);
        $stmt->execute();
        $stmt->close();

        $_SESSION['success_message'] = "Pharmacy updated successfully!";
        header("Location: admin_dashboard.php");
        exit;
    }
}

// Handle update pharmacist request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_pharmacist'])) {
    $pharmacistID = $_POST['pharmacistID'];
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = !empty($_POST['password']) ? password_hash(trim($_POST['password']), PASSWORD_DEFAULT) : null;
    $pharmacyID = $_POST['pharmacyID'] ?: null;

    // Server-side validation
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($errors)) {
        if ($password) {
            $stmt = $conn->prepare("UPDATE Pharmacists SET Username = ?, Email = ?, Password = ?, PharmacyID = ? WHERE PharmacistID = ?");
            $stmt->bind_param("sssii", $username, $email, $password, $pharmacyID, $pharmacistID);
        } else {
            $stmt = $conn->prepare("UPDATE Pharmacists SET Username = ?, Email = ?, PharmacyID = ? WHERE PharmacistID = ?");
            $stmt->bind_param("ssii", $username, $email, $pharmacyID, $pharmacistID);
        }
        $stmt->execute();
        $stmt->close();

        $_SESSION['success_message'] = "Pharmacist updated successfully!";
        header("Location: admin_dashboard.php");
        exit;
    }
}

// Fetch pharmacies and pharmacists for display
$pharmacies = $conn->query("SELECT * FROM Pharmacies");
$pharmacists = $conn->query("SELECT p.*, ph.PharmacyName FROM Pharmacists p LEFT JOIN Pharmacies ph ON p.PharmacyID = ph.PharmacyID");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy & Pharmacists Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background-color: #0e76a8;
            color: #fff;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
        }

        header h1 {
            margin: 0;
        }

        h2 {
            color: #0e76a8;
            margin-bottom: 20px;
            border-bottom: 2px solid #0e76a8;
            padding-bottom: 10px;
        }

        .form-group label {
            font-weight: bold;
        }

        .form-control {
            width: 100%;
            /* Ensures the form elements take full width of the column */
            box-sizing: border-box;
            /* Ensures padding doesn't increase element width */
            padding: 10px;
            margin: 5px 0 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background-color: #0a5a77;
        }

        .btn-danger {
            background-color: #ff4d4d;
        }

        .btn-danger:hover {
            background-color: #cc0000;
        }

        .btn-warning {
            background-color: #ffcc00;
            color: #333;
        }

        .btn-warning:hover {
            background-color: #e6b800;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
            word-wrap: break-word;
            max-width: 150px;
        }

        th {
            background-color: #0e76a8;
            color: #fff;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 5px;
        }

        .alert-success {
            background-color: #dff0d8;
            border-color: #d6e9c6;
            color: #3c763d;
        }

        .alert-danger {
            background-color: #f2dede;
            border-color: #ebccd1;
            color: #a94442;
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Header Section -->
        <header>
            <h1>Pharmacy & Pharmacists Management</h1>
        </header>

        <!-- Display Success Messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <!-- Display Errors -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Manage Pharmacies -->
        <h2>Manage Pharmacies</h2>
        <table>
            <thead>
                <tr>
                    <th>Pharmacy Name</th>
                    <th>Address</th>
                    <th>Phone Number</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $pharmacies->fetch_assoc()): ?>
                    <tr>
                        <form action="admin_dashboard.php" method="post">
                            <td><input type="text" class="form-control" name="pharmacyName" value="<?php echo htmlspecialchars($row['PharmacyName']); ?>" required></td>
                            <td><input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($row['Address']); ?>" required></td>
                            <td><input type="tel" class="form-control" name="phoneNumber" value="<?php echo htmlspecialchars($row['PhoneNumber']); ?>" pattern="[0-9]{10}" required></td>
                            <td><input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($row['Email']); ?>" required></td>
                            <td>
                                <input type="hidden" name="pharmacyID" value="<?php echo $row['PharmacyID']; ?>">
                                <button type="submit" name="update_pharmacy" class="btn btn-warning btn-sm">Update</button>
                                <a href="admin_dashboard.php?delete_pharmacy=<?php echo $row['PharmacyID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this pharmacy?')">Delete</a>
                            </td>
                        </form>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Manage Pharmacists -->
        <h2>Manage Pharmacists</h2>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Pharmacy</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $pharmacists->fetch_assoc()): ?>
                    <tr>
                        <form action="admin_dashboard.php" method="post">
                            <td><input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($row['Username']); ?>" required></td>
                            <td><input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($row['Email']); ?>" required></td>
                            <td>
                                <select class="form-control" name="pharmacyID">
                                    <option value="">None</option>
                                    <?php
                                    $pharmacyOptions = $conn->query("SELECT * FROM Pharmacies");
                                    while ($option = $pharmacyOptions->fetch_assoc()):
                                        $selected = ($option['PharmacyID'] == $row['PharmacyID']) ? 'selected' : '';
                                    ?>
                                        <option value="<?php echo $option['PharmacyID']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($option['PharmacyName']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </td>
                            <td>
                                <input type="hidden" name="pharmacistID" value="<?php echo $row['PharmacistID']; ?>">
                                <button type="submit" name="update_pharmacist" class="btn btn-warning btn-sm">Update</button>
                                <a href="admin_dashboard.php?delete_pharmacist=<?php echo $row['PharmacistID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this pharmacist?')">Delete</a>
                            </td>
                        </form>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Create New Pharmacy Form -->
        <h2>Create New Pharmacy</h2>
        <form action="admin_dashboard.php" method="post" id="pharmacyForm">
            <div class="form-group">
                <label for="pharmacyName">Pharmacy Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="pharmacyName" name="pharmacyName" required>
            </div>
            <div class="form-group">
                <label for="address">Address <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            <div class="form-group">
                <label for="phoneNumber">Phone Number <span class="text-danger">*</span></label>
                <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" pattern="[0-9]{10}" required>
            </div>
            <div class="form-group">
                <label for="email">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <button type="submit" name="create_pharmacy" class="btn">Create Pharmacy</button>
        </form>

        <!-- Create New Pharmacist Form -->
        <h2>Create New Pharmacist</h2>
        <form action="admin_dashboard.php" method="post" id="pharmacistForm">
            <div class="form-group">
                <label for="username">Username <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="password" name="password" required minlength="6">
            </div>
            <div class="form-group">
                <label for="pharmacyID">Pharmacy (optional)</label>
                <select class="form-control" id="pharmacyID" name="pharmacyID">
                    <option value="">None</option>
                    <?php
                    $pharmacyOptions = $conn->query("SELECT * FROM Pharmacies");
                    while ($row = $pharmacyOptions->fetch_assoc()):
                    ?>
                        <option value="<?php echo $row['PharmacyID']; ?>"><?php echo htmlspecialchars($row['PharmacyName']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" name="create_pharmacist" class="btn">Create Pharmacist</button>
        </form>

    </div>

    <!-- Client-Side Validation Script -->
    <script>
        document.getElementById('pharmacyForm').addEventListener('submit', function(event) {
            var phoneInput = document.getElementById('phoneNumber');
            var phonePattern = /^[0-9]{10}$/;
            if (!phonePattern.test(phoneInput.value)) {
                alert('Phone number must be exactly 10 digits.');
                event.preventDefault();
            }
        });

        document.getElementById('pharmacistForm').addEventListener('submit', function(event) {
            var passwordInput = document.getElementById('password');
            if (passwordInput.value.length < 6) {
                alert('Password must be at least 6 characters.');
                event.preventDefault();
            }
        });
    </script>

</body>

</html>