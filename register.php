<?php
session_start();
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect input data and validate
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $user_type = $_POST['user_type'];
    $reg_no = $user_type == 'student' ? trim($_POST['reg_no']) : NULL;
    $emp_id = $user_type == 'faculty' ? trim($_POST['emp_id']) : NULL;
    $emp_type = $user_type == 'faculty' ? trim($_POST['emp_type']) : NULL;  // Add emp_type for faculty
    $cnic = trim($_POST['cnic']);  // CNIC is required for both student and faculty
    $program = $user_type == 'student' ? trim($_POST['program']) : NULL;
    $semester = $user_type == 'student' ? trim($_POST['semester']) : NULL;

// Handle Profile Picture Upload
$profile_picture = $_FILES['profile_picture']['name'];
$target_dir = "uploads/profile_picture/";

// Ensure the directory exists
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true); // Creates the directory if it doesn't exist
}

$target_file = $target_dir . basename($profile_picture);
$upload_ok = 1;

if ($_FILES['profile_picture']['error'] != UPLOAD_ERR_OK) {
    $error = "There was an error uploading the file.";
} else {
    // Check if image file is a valid image
    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    if ($check !== false) {
        $upload_ok = 1;
    } else {
        $upload_ok = 0;
        $error = "Sorry, your file is not a valid image.";
    }

    // Try to upload the file
    if ($upload_ok == 1) {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            // Profile picture uploaded successfully
        } else {
            $error = "Sorry, there was an error uploading your profile picture.";
        }
    }
}


    // Hash password for security
    $hashed_password = md5($password);

    // Server-side validation
    if ($user_type == 'student') {
        // Check CNIC: must be 13 digits and numeric
        if (!preg_match('/^[0-9]{13}$/', $cnic)) {
            $error = "CNIC must be 13 digits long and numeric.";
        }

        // Check Registration Number: must start with 'B'
        if (!preg_match('/^B/', $reg_no)) {
            $error = "Registration Number must start with 'B'.";
        }

        // Check Program: must contain only alphabets
        if (!preg_match('/^[A-Za-z\s]+$/', $program)) {
            $error = "Program must contain only alphabets.";
        }

        // Check Semester: must be a number
        if (!preg_match('/^[0-9]+$/', $semester)) {
            $error = "Semester must be a number.";
        }
    }

    // Check if CNIC is valid
    if (!preg_match('/^[0-9]{13}$/', $cnic)) {
        $error = "CNIC must be 13 digits long and numeric.";
    }

    // Validate Email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    }

    // Check if the email already exists
    if (!isset($error)) {
        $stmt = $pdo->prepare("SELECT email FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $error = "Email is already registered.";
        }
    }

    // If no errors, insert into the database
    if (!isset($error)) {
        if ($user_type == 'student') {
            // For student, insert all fields including program and semester
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, user_type, reg_no, emp_id, cnic, program, semester, profile_picture) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$username, $email, $hashed_password, $user_type, $reg_no, $emp_id, $cnic, $program, $semester, $profile_picture]);
        } else {
            // For faculty, exclude program and semester, and include emp_type
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, user_type, reg_no, emp_id, emp_type, cnic, profile_picture) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$username, $email, $hashed_password, $user_type, $reg_no, $emp_id, $emp_type, $cnic, $profile_picture]);
        }

        // Redirect to login page after successful registration
        header('Location: user_login.php');
        exit;
    }
}
?>

<!-- Registration Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
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
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .form-control {
            border-radius: 5px;
        }
        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <img src="logo.png" alt="PAF-IAST Logo">
        <h1>Vehicle Entry System</h1>
    </div>

    <div class="container">
        <h2>User Registration</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="username">Name</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="user_type">User Type</label>
                <select class="form-control" id="user_type" name="user_type" required>
                    <option value="student">Student</option>
                    <option value="faculty">Faculty</option>
                </select>
            </div>

            <!-- Fields for Student -->
            <div class="form-group" id="reg_no_field">
                <label for="reg_no">Registration No</label>
                <input type="text" class="form-control" id="reg_no" name="reg_no" >
            </div>
            <div class="form-group" id="cnic_field">
                <label for="cnic">CNIC</label>
                <input type="text" class="form-control" id="cnic" name="cnic" required>
            </div>
            <div class="form-group" id="program_field">
                <label for="program">Program</label>
                <input type="text" class="form-control" id="program" name="program" >
            </div>
            <div class="form-group" id="semester_field">
                <label for="semester">Semester</label>
                <input type="text" class="form-control" id="semester" name="semester" >
            </div>

            <!-- Fields for Faculty -->
            <div class="form-group" id="emp_id_field" style="display: none;">
                <label for="emp_id">Employee ID</label>
                <input type="text" class="form-control" id="emp_id" name="emp_id">
            </div>
            <div class="form-group" id="emp_type_field" style="display: none;">
                <label for="emp_type">Employee Type</label>
                <input type="text" class="form-control" id="emp_type" name="emp_type" >
            </div>

            <!-- Profile Picture -->
            <div class="form-group">
                <label for="profile_picture">Profile Picture</label>
                <input type="file" class="form-control-file" id="profile_picture" name="profile_picture">
            </div>

            <button type="submit" class="btn btn-primary">Register</button>
        </form>

        <div class="register-link">
            <p>Already have an account? <a href="user_login.php">Login</a></p>
        </div>
    </div>

    <script>
        document.getElementById('user_type').addEventListener('change', function() {
            var userType = this.value;
            if (userType === 'student') {
                document.getElementById('reg_no_field').style.display = 'block';
                document.getElementById('cnic_field').style.display = 'block';
                document.getElementById('program_field').style.display = 'block';
                document.getElementById('semester_field').style.display = 'block';
                document.getElementById('emp_id_field').style.display = 'none';
                document.getElementById('emp_type_field').style.display = 'none';
            } else {
                document.getElementById('reg_no_field').style.display = 'none';
                document.getElementById('cnic_field').style.display = 'block';
                document.getElementById('program_field').style.display = 'none';
                document.getElementById('semester_field').style.display = 'none';
                document.getElementById('emp_id_field').style.display = 'block';
                document.getElementById('emp_type_field').style.display = 'block';
            }
        });
    </script>
</body>
</html>
