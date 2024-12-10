<?php
session_start();

// Default admin credentials
$default_admin_username = "admin";
$default_admin_password = "admin123";

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin_dashboard.php');
    exit;
}

// Handle admin login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username === $default_admin_username && $password === $default_admin_password) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: admin_dashboard.php');
        exit;
    } else {
        $error_message = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e7f3ff; /* Light blue background */
            font-family: 'Roboto', sans-serif;
        }
        .container {
            margin-top: 100px;
        }
        .card {
            border: none;
            border-radius: 15px; /* Rounded corners */
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }
        .card-header {
            background-color: #007bff; /* Blue header */
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .btn-primary {
            background-color: #0056b3; /* Deep blue for the primary button */
            border-color: #0056b3;
        }
        .btn-primary:hover {
            background-color: #004085;
            border-color: #003366;
        }
        .form-control {
            border-radius: 15px;
        }
        .card-body p {
            font-size: 18px;
            color: #333333;
        }
        .header {
            background-color: #0056b3; /* Blue background for header */
            padding: 20px 0;
            text-align: center;
            color: white;
            border-radius: 0 0 15px 15px;
        }
        .header img {
            max-height: 50px;
            margin-right: 15px;
        }
        .header h1 {
            display: inline-block;
            font-size: 28px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <img src="logo.png" alt="PAF-IAST Logo">
        <h1>Vehicle Entry System</h1>
    </div>

    <!-- Main Content Section -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Admin Login</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger">
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" name="admin_login" class="btn btn-primary btn-block">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
