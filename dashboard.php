<?php
session_start();
include 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: user_login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: user_login.php');
    exit;
}

// Fetch user details from the database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch user's vehicle details
$vehicle_stmt = $pdo->prepare("SELECT * FROM vehicles WHERE user_id = ?");
$vehicle_stmt->execute([$user_id]);
$vehicles = $vehicle_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle adding new vehicle
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_vehicle'])) {
    $model = trim($_POST['model']);
    $type = trim($_POST['type']);
    $license_no = trim($_POST['license_no']);
    $number_plate = trim($_POST['number_plate']);

    // Check if the vehicle with the same number plate already exists for any user
    $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM vehicles WHERE number_plate = ?");
    $check_stmt->execute([$number_plate]);
    $existing_vehicle = $check_stmt->fetchColumn();

    if ($existing_vehicle > 0) {
        // Display error message if the vehicle already exists
        $error_message = "This vehicle has already been registered by another user.";
    } else {
        // Handle vehicle image upload
        $image_path = '';
        if (isset($_FILES['vehicle_image']) && $_FILES['vehicle_image']['error'] == 0) {
            $image_name = $_FILES['vehicle_image']['name'];
            $image_tmp_name = $_FILES['vehicle_image']['tmp_name'];
            $image_extension = pathinfo($image_name, PATHINFO_EXTENSION);
            $image_path = 'uploads/vehicles/' . uniqid() . '.' . $image_extension;

            if (!move_uploaded_file($image_tmp_name, $image_path)) {
                $error_message = "Error uploading vehicle image.";
            }
        }

        // Insert vehicle into database
        $insert_stmt = $pdo->prepare("INSERT INTO vehicles (user_id, model, type, license_no, number_plate, image_path) 
                                     VALUES (?, ?, ?, ?, ?, ?)");
        $insert_stmt->execute([$user_id, $model, $type, $license_no, $number_plate, $image_path]);

        header('Location: dashboard.php');
        exit;
    }
}

// Handle deleting a vehicle
if (isset($_GET['delete_vehicle_id'])) {
    $delete_vehicle_id = intval($_GET['delete_vehicle_id']);
    $delete_stmt = $pdo->prepare("DELETE FROM vehicles WHERE id = ? AND user_id = ?");
    $delete_stmt->execute([$delete_vehicle_id, $user_id]);

    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
        }
        .header {
            background-color: #007bff;
            padding: 10px 0;
            text-align: center;
            color: white;
            position: relative;
        }
        .logout-link {
            position: absolute;
            right: 20px;
            top: 10px;
        }
        .profile-pic {
            position: absolute;
            left: 20px;
            top: 10px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        .container {
            margin-top: 30px;
        }
        .tab-content {
            margin-top: 20px;
        }
        .vehicle-table th, .vehicle-table td {
            padding: 10px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="header">
    <?php if ($user['profile_picture']) { ?>
        <img src="uploads/profile_picture/<?php echo htmlspecialchars($user['profile_picture']); ?>" class="profile-pic" alt="Profile Picture">
    <?php } ?>
    <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?></h1>
    <a href="dashboard.php?action=logout" class="btn btn-danger logout-link">Logout</a>
</div>


<div class="container">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" id="dashboardTabs">
        <li class="nav-item">
            <a class="nav-link active" id="userDetailsTab" data-toggle="tab" href="#userDetails">User Details</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="vehicleInfoTab" data-toggle="tab" href="#vehicleInfo">Vehicle Information</a>
        </li>
    </ul>

    <!-- Tab content -->
    <div class="tab-content">
        <!-- User Details Tab -->
        <div id="userDetails" class="tab-pane fade show active">
            <div class="card">
                <div class="card-header">
                    <h4>Your Profile</h4>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>CNIC:</strong> <?php echo htmlspecialchars($user['cnic']); ?></p>

                    <?php if ($user['user_type'] == 'student') { ?>
                        <p><strong>Reg. No:</strong> <?php echo htmlspecialchars($user['reg_no']); ?></p>
                        <p><strong>Program:</strong> <?php echo htmlspecialchars($user['program']); ?></p>
                        <p><strong>Semester:</strong> <?php echo htmlspecialchars($user['semester']); ?></p>
                    <?php } elseif ($user['user_type'] == 'faculty') { ?>
                        <p><strong>Employee ID:</strong> <?php echo htmlspecialchars($user['emp_id']); ?></p>
                        <p><strong>Employee Type:</strong> <?php echo htmlspecialchars($user['emp_type']); ?></p>
                    <?php } ?>
                </div>
            </div>
        </div>

        <!-- Vehicle Information Tab -->
        <div id="vehicleInfo" class="tab-pane fade">
            <div class="card">
                <div class="card-header">
                    <h4>Vehicle Information</h4>
                </div>
                <div class="card-body">
                    <!-- Form to Add New Vehicle -->
                    <form method="POST" action="dashboard.php" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="model">Model:</label>
                            <input type="text" class="form-control" id="model" name="model" required>
                        </div>
                        <div class="form-group">
                            <label for="type">Type:</label>
                            <input type="text" class="form-control" id="type" name="type" required>
                        </div>
                        <div class="form-group">
                            <label for="license_no">License No:</label>
                            <input type="text" class="form-control" id="license_no" name="license_no" required>
                        </div>
                        <div class="form-group">
                            <label for="number_plate">Number Plate:</label>
                            <input type="text" class="form-control" id="number_plate" name="number_plate" required>
                        </div>
                        <div class="form-group">
                            <label for="vehicle_image">Upload Vehicle Image:</label>
                            <input type="file" class="form-control" id="vehicle_image" name="vehicle_image" accept="image/*">
                        </div>
                        <button type="submit" name="add_vehicle" class="btn btn-primary">Add Vehicle</button>
                    </form>

                    <?php if (isset($error_message)) { ?>
                        <div class="alert alert-danger mt-3">
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php } ?>

                    <hr>

                    <!-- Table to Display Existing Vehicles -->
                    <h5>Your Registered Vehicles</h5>
                    <table class="table table-striped vehicle-table">
                        <thead>
                            <tr>
                                <th>Model</th>
                                <th>Type</th>
                                <th>License No</th>
                                <th>Number Plate</th>
                                <th>Vehicle Image</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vehicles as $vehicle) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($vehicle['model']); ?></td>
                                    <td><?php echo htmlspecialchars($vehicle['type']); ?></td>
                                    <td><?php echo htmlspecialchars($vehicle['license_no']); ?></td>
                                    <td><?php echo htmlspecialchars($vehicle['number_plate']); ?></td>
                                    <td><img src="<?php echo htmlspecialchars($vehicle['image_path']); ?>" alt="Vehicle Image" width="50"></td>
                                    <td><a href="dashboard.php?delete_vehicle_id=<?php echo $vehicle['id']; ?>" class="btn btn-danger">Delete</a></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
