<?php
// Start session
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Options</title>
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
        .btn-secondary {
            background-color: #6c757d; /* Grey for secondary button */
            border-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
        .card-body p {
            font-size: 18px;
            color: #333333;
        }
        .card-body .btn {
            margin-top: 10px;
        }

        /* Header styles */
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
                        <h4>Welcome</h4>
                    </div>
                    <div class="card-body text-center">
                        <p>Please choose an option to log in:</p>
                        <a href="admin_login.php" class="btn btn-primary btn-block">Admin Login</a>
                        <a href="user_login.php" class="btn btn-secondary btn-block">User Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
