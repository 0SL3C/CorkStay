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
if ($conn->connect_error) {
    die("Error connecting to database: " . $conn->connect_error);
}

// Fetch all testimonials
$query = "SELECT t.comment, t.service_name, t.testimonial_date, u.first_name 
          FROM testimonials t 
          JOIN users u ON t.tenant_id = u.user_id 
          ORDER BY t.testimonial_date DESC";
$result = $conn->query($query);

$testimonials = [];
if ($result && $result->num_rows > 0) {
    $testimonials = $result->fetch_all(MYSQLI_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CorkStay - Testimonials</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: url('./images/1126773.jpg') no-repeat center center fixed;
            background-size: cover;
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

        .container {
            max-width: 700px;
            margin: 50px auto;
            padding: 20px;
            background-color: rgba(255,255,255,0.85);
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        header {
            text-align: center;
            margin-bottom: 30px;
        }

        main ul {
            list-style-type: none;
            padding-left: 0;
        }

        main li {
            margin-bottom: 20px;
            padding: 10px;
            border-bottom: 1px solid #ccc;
            border-radius: 10px;
            background-color: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
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

        ul {
            list-style-type: none;
            padding-left: 0;
        }

        li {
            margin: 20px 0;
        }

        li p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
    <header>
        <h1>Testimonials</h1>
            <?php if (isset($_SESSION['user_id'])): ?>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</p>
                <nav>
                <a href="logout.php">Logout</a>
                <?php if ($_SESSION['role'] === 'landlord'): ?>
                    <a href="property_listing.php">Manage Properties</a>
                <?php elseif ($_SESSION['role'] === 'tenant'): ?>
                    <a href="testimonial_add.php">Add Testimonial</a>
                <?php endif; ?>
            <?php else: ?>
            <nav>
                <a href="signup.php">Sign Up</a>
                <a href="login.php">Login</a>
            <?php endif; ?>
            <a href="search.php">Search Properties</a>
            <a href="home.php">Home</a>
        </nav>
    </header>

    <main>
        <div style="max-width: 700px; margin: 0 auto; padding: 0px 20px;">
            <?php if (!empty($testimonials)): ?>
            <ul>
                <?php foreach ($testimonials as $testimonial): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($testimonial['first_name']); ?></strong>
                        (<?php echo htmlspecialchars($testimonial['testimonial_date']); ?>):
                        <p><em>Service: <?php echo htmlspecialchars($testimonial['service_name']); ?></em></p>
                        <p><?php echo htmlspecialchars($testimonial['comment']); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No testimonials available.</p>
        <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>

