<?php
session_start();
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect input data
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Hash the entered password for comparison
    $hashed_password = md5($password);

    // Check if the user exists in the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt->execute([$email, $hashed_password]);

    if ($stmt->rowCount() > 0) {
        // User found, start session and store user details
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_type'] = $user['user_type'];

        // Redirect to dashboard
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Invalid credentials.";
    }
}
?>

<!-- Login Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f0f8ff; /* Light blue background */
            font-family: 'Roboto', sans-serif;
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

        .container {
            max-width: 600px;
            margin-top: 50px;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #007bff; /* Blue header */
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 20px;
        }

        .btn-primary {
            background-color: #0056b3; /* Deep blue */
            border-color: #0056b3;
            border-radius: 10px;
        }

        .btn-primary:hover {
            background-color: #003366;
            border-color: #003366;
        }

        .form-control {
            border-radius: 10px;
        }

        .alert {
            margin-bottom: 20px;
        }

        .container h2 {
            color: #333333;
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
        <h2>Login</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
            
        </form>

        <div class="register-link">
            <p>Not an account? <a href="register.php">Register New</a></p>
        </div>
    </div>
    </div>

</body>
</html>
