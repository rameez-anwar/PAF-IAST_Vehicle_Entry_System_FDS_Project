<?php
session_start();
include 'db_config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit;
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin_login.php');
    exit;
}

// Search vehicle by number plate
$vehicle = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_vehicle'])) {
    $number_plate = trim($_POST['number_plate']);

    $stmt = $pdo->prepare("SELECT vehicles.*, users.username, users.email, users.cnic, users.profile_picture AS user_image, users.user_type, users.reg_no, users.semester, users.emp_type
                           FROM vehicles 
                           INNER JOIN users ON vehicles.user_id = users.id 
                           WHERE vehicles.number_plate = ?");
    $stmt->execute([$number_plate]);
    $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="header bg-primary text-white p-3">
    <h1 class="text-center">Admin Panel</h1>
    <a href="?logout" class="btn btn-danger float-right">Logout</a>
</div>

<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h4>Search Vehicle by Number Plate</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="number_plate">Enter Number Plate</label>
                    <input type="text" class="form-control" id="number_plate" name="number_plate" required>
                </div>
                <button type="submit" name="search_vehicle" class="btn btn-primary">Search</button>
            </form>

            <?php if ($vehicle): ?>
    <hr>
    <div class="row">
        <!-- Vehicle Details -->
        <div class="col-md-6">
            <h5>Vehicle Details</h5>
            <p><strong>Model:</strong> <?php echo htmlspecialchars($vehicle['model']); ?></p>
            <p><strong>Type:</strong> <?php echo htmlspecialchars($vehicle['type']); ?></p>
            <p><strong>License No:</strong> <?php echo htmlspecialchars($vehicle['license_no']); ?></p>
            <p><strong>Number Plate:</strong> <?php echo htmlspecialchars($vehicle['number_plate']); ?></p>
            <p>
                <strong>Vehicle Image:</strong><br>
                <?php if ($vehicle['image_path']): ?>
                    <img src="<?php echo htmlspecialchars($vehicle['image_path']); ?>" alt="Vehicle Image" width="300">
                <?php else: ?>
                    No Image Available
                <?php endif; ?>
            </p>
        </div>

        <!-- Owner Details -->
        <div class="col-md-6">
            <h5>Owner Details</h5>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($vehicle['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($vehicle['email']); ?></p>
            <p><strong>CNIC:</strong> <?php echo htmlspecialchars($vehicle['cnic']); ?></p>
           

            <?php if ($vehicle['user_type'] == 'student'): ?>
                <p><strong>Registration No:</strong> <?php echo htmlspecialchars($vehicle['reg_no']); ?></p>
                <p><strong>Semester:</strong> <?php echo htmlspecialchars($vehicle['semester']); ?></p>
            <?php elseif ($vehicle['user_type'] == 'faculty'): ?>
                <p><strong>Employee Type:</strong> <?php echo htmlspecialchars($vehicle['emp_type']); ?></p>
            <?php endif; ?>
                 
            <p>
                <strong>Owner Picture:</strong><br>
                <?php 
                    if ($vehicle['user_image']): 
                        echo '<img src="uploads/profile_picture/' . htmlspecialchars($vehicle['user_image']) . '" alt="User Profile Picture" width="300">';
                    else: 
                        echo 'No Profile Picture Available';
                    endif;
                ?>
            </p>





        </div>
    </div>
<?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <hr>
    <div class="alert alert-danger">No vehicle found with this number plate.</div>
<?php endif; ?>

        </div>
    </div>
</div>
</body>
</html>
