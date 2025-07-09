<?php
require_once 'config.php';

// Initialize session with timeout handling
initSession();

// Get database connection
$conn = getDbConnection();

$first_name = $_SESSION['first_name'] ?? '';
$role = $_SESSION['role'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CorkStay - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="text-center mb-4">
            <h1 class="display-5">Welcome to CorkStay Property Lettings</h1>
            <p class="lead">Hello, <strong><?= htmlspecialchars($first_name) ?></strong>! You are logged in as a <strong><?= htmlspecialchars($role) ?></strong>.</p>
        </div>

        <div class="row justify-content-center">
            <?php if ($role === 'landlord'): ?>
                <div class="col-md-4 mb-3">
                    <a href="property_listing.php" class="btn btn-primary w-100">Manage Properties</a>
                </div>
            <?php endif; ?>
            
            <?php if ($role === 'tenant'): ?>
                <div class="col-md-4 mb-3">
                    <a href="search.php" class="btn btn-success w-100">Search Properties</a>
                </div>
            <?php endif; ?>

            <div class="col-md-4 mb-3">
                <a href="testimonials.php" class="btn btn-info w-100">View Testimonials</a>
            </div>

            <div class="col-md-4 mb-3">
                <a href="logout.php" class="btn btn-danger w-100">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>
