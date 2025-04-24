<?php
session_start();
$timeout = 3600; // 1 hour
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_unset();
    session_destroy();
    header("Location: home.php");
    exit();
}
$_SESSION['last_activity'] = time();

$conn = mysqli_connect('localhost', 'root', '', 'corkstay');
if($conn->connect_error){
    die("Error connecting to database: " . $conn->connect_error);
}

$isLoggedIn = isset($_SESSION['user_id']);
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : null;
?>

<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/home.css">
        <title>CorkStay - Home</title>
    </head>

    <body>
        <header>
            <h1>Welcome to CorkStay Property Lettings</h1>
            <nav>
                <?php if ($isLoggedIn): ?>
                    <p>Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</p>
                    <a href="logout.php">Logout</a>
                    <?php if ($userRole === 'landlord'): ?>
                        <a href="property_listing.php">Manage Properties</a>
                    <?php elseif ($userRole === 'tenant'): ?>
                        <a href="testimonial_add.php">Add Testimonial</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="signup.php">Sign Up</a> | 
                    <a href="login.php">Login</a>
                <?php endif; ?>
                <a href="search.php">Search Properties</a>
                <a href="testimonials.php">View Testimonials</a>
            </nav>
        </header>

    </body>
</html>