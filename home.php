<?php
// Start session for user authentication
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : null;

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    $conn = new mysqli('localhost', 'root', '', 'corkstay');
    if ($conn->connect_error) {
        $error = "Database connection failed.";
    } else {
        $stmt = $conn->prepare("SELECT user_id, first_name, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['role'] = $user['role'];
                header("Location: home.php");
                exit();
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Invalid email or password.";
        }
        $stmt->close();
        $conn->close();
    }
    $error = "Login functionality placeholder. Configure database connection.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                <a href="home.php">Login</a>
            <?php endif; ?>
            <a href="search.php">Search Properties</a>
            <a href="testimonials.php">View Testimonials</a>
        </nav>
    </header>

    <main>
        <!-- Agency Information -->
        <section>
            <h2>About CorkStay</h2>
            <p>CorkStay is your trusted property lettings agency in Cork, offering a wide range of rental properties for tenants and comprehensive property management services for landlords. Find your perfect home or manage your properties with ease.</p>
        </section>

        <!-- Login Form (Displayed only if not logged in) -->
        <?php if (!$isLoggedIn): ?>
            <section>
                <h2>Login</h2>
                <?php if (isset($error)): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                <form method="POST" action="home.php">
                    <label for="email">Email:</label><br>
                    <input type="email" id="email" name="email" required><br>
                    <label for="password">Password:</label><br>
                    <input type="password" id="password" name="password" required><br>
                    <input type="submit" name="login" value="Login">
                    <p><a href="password_reset.php">Forgot Password?</a></p>
                </form>
            </section>
        <?php endif; ?>

        <!-- Feature Boxes -->
        <section>
            <h2>Featured Properties</h2>
            <div>
                <h3>Cozy 1-Bed Apartment</h3>
                <img src="images/property1.jpg" alt="1-Bed Apartment" width="200">
                <p>Modern 1-bedroom apartment in the heart of Cork. €900/month, 6-month lease.</p>
                <a href="search.php">View Details</a>
            </div>
            <div>
                <h3>Spacious 2-Bed House</h3>
                <img src="images/property2.jpg" alt="2-Bed House" width="200">
                <p>Beautiful 2-bedroom house with garden. €1,200/month, 1-year lease.</p>
                <a href="search.php">View Details</a>
            </div>
            <div>
                <h3>Luxury 3-Bed Penthouse</h3>
                <img src="images/property3.jpg" alt="3-Bed Penthouse" width="200">
                <p>Stunning 3-bedroom penthouse with city views. €2,000/month, 3-month lease.</p>
                <a href="search.php">View Details</a>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 CorkStay Property Lettings. All rights reserved.</p>
    </footer>
</body>
</html>