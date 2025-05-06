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
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
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
        $stmt = $conn->prepare("INSERT INTO users (first_name, email, password, role) VALUES (?, ?, ?, ?)");
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

        header h1, header h2 {
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

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            max-width: 300px;
            margin: 0 auto;
        }

        label {
            font-weight: bold;
            color: #333;
            text-align: center;
            width: 100%;
        }

        input, textarea, select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
            max-width: 300px;
            box-sizing: border-box;
            margin: 0 auto;
        }

        input[type="submit"] {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
            max-width: 150px;
            margin: 10px auto;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        ul {
            list-style-type: none;
            padding: 0;
            color: red;
            text-align: center;
        }

        li {
            margin-bottom: 10px;
        }

        a {
            color: #007BFF;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1>CorkStay Property Lettings</h1>
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
                <a href="login.php">Login</a>
                <a href="search.php">Search Properties</a>
                <a href="testimonials.php">View Testimonials</a>
                <a href="home.php">Home</a>
            <?php endif; ?>
        </nav>
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
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>" required>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <label for="role">Role:</label>
                <select id="role" name="role" required>
                    <option value="tenant" <?php echo isset($_POST['role']) && $_POST['role'] === 'tenant' ? 'selected' : ''; ?>>Tenant</option>
                    <option value="landlord" <?php echo isset($_POST['role']) && $_POST['role'] === 'landlord' ? 'selected' : ''; ?>>Landlord</option>
                </select>
                <input type="submit" name="signup" value="Sign Up">
            </form>
        <?php endif; ?>
    </header>
</body>
</html>
