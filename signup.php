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
if ($conn->connect_error) {
    die("Error connecting to database: " . $conn->connect_error);
}

$isLoggedIn = isset($_SESSION['user_id']);
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : null;

// Handle form submission
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    // Sanitize and validate inputs
    $first_name = htmlspecialchars($_POST['first_name']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validation
    if (empty($first_name)) {
        $errors[] = "First name is required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }
    if (empty($password) || strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if (!in_array($role, ['landlord', 'tenant'])) {
        $errors[] = "Invalid role selected.";
    }

    // Check for duplicate email
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM Users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        if ($count > 0) {
            $errors[] = "Email already registered.";
        }
    }

    // Insert user if no errors
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO Users (first_name, email, hpass, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $first_name, $email, $hashed_password, $role);
        if ($stmt->execute()) {
            // Log the user in
            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['role'] = $role;
            $stmt->close();
            header("Location: home.php");
            exit();
        } else {
            $errors[] = "Registration failed: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CorkStay - Sign Up</title>
</head>
<body>
    <header>
        <h1>CorkStay Property Lettings</h1>
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
                <a href="login.php">Login</a>
            <?php endif; ?>
            <a href="search.php">Search Properties</a>
            <a href="testimonials.php">View Testimonials</a>
        </nav>
    </header>

    <main>
        <section>
            <h2>Sign Up</h2>
            <?php if (!empty($errors)): ?>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <?php if ($isLoggedIn): ?>
                <p>You are already logged in. <a href="home.php">Go to Home</a></p>
            <?php else: ?>
                <form method="POST" action="signup.php">
                    <label for="first_name">First Name:</label><br>
                    <input type="text" id="first_name" name="first_name" value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>" required><br>
                    <label for="email">Email:</label><br>
                    <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required><br>
                    <label for="password">Password:</label><br>
                    <input type="password" id="password" name="password" required><br>
                    <label for="role">Role:</label><br>
                    <select id="role" name="role" required>
                        <option value="tenant" <?php echo isset($_POST['role']) && $_POST['role'] === 'tenant' ? 'selected' : ''; ?>>Tenant</option>
                        <option value="landlord" <?php echo isset($_POST['role']) && $_POST['role'] === 'landlord' ? 'selected' : ''; ?>>Landlord</option>
                    </select><br>
                    <input type="submit" name="signup" value="Sign Up">
                </form>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>© 2025 CorkStay Property Lettings. All rights reserved.</p>
    </footer>
</body>
</html>