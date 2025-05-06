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

$conn = mysqli_connect('localhost', 'root', '159753', 'corkstay');
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
    <title>CorkStay - Home</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: url('./images/1126773.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background: inherit;
            filter: blur(8px);
            z-index: -1;
        }

        header {
            text-align: center;
            padding: 40px 20px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            max-width: 700px;
            width: 90%;
        }


        header h1 {
            margin-bottom: 20px;
            font-size: 2em;
            color: #333;
        }

        nav {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }

        nav a, nav p {
            text-decoration: none;
            color: white;
            background-color: #007BFF;
            padding: 10px 20px;
            border-radius: 25px;
            transition: background-color 0.3s ease;
            font-weight: bold;
        }

        nav a:hover {
            background-color: #0056b3;
        }

        nav p {
            background-color: transparent;
            color: #333;
            padding: 10px;
            font-weight: normal;
        }
    </style>
</head>

<body>
    <header>
        <h1>Welcome to CorkStay Property Listings</h1>
        <?php if ($isLoggedIn): ?>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</p>
        <nav>
                <a href="logout.php">Logout</a>
                <?php if ($userRole === 'landlord'): ?>
                    <a href="property_listing.php">Manage Properties</a>
                <?php elseif ($userRole === 'tenant'): ?>
                    <a href="testimonial_add.php">Add Testimonial</a>
                <?php endif; ?>
        <?php else: ?>
        <nav>
                <a href="signup.php">Sign Up</a>
                <a href="login.php">Login</a>
        <?php endif; ?>
            <a href="search.php">Search Properties</a>
            <a href="testimonials.php">View Testimonials</a>
        </nav>
    </header>
</body>
</html>

