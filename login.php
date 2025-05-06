<?php
session_start();
$conn = mysqli_connect('localhost', 'root', '159753', 'corkstay');
if ($conn->connect_error) {
    die("Error connecting to database: " . $conn->connect_error);
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT user_id, first_name, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($user_id, $first_name, $hashed_password, $role);
        if ($stmt->fetch() && password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['role'] = $role;
            header("Location: home.php");
            exit();
        } else {
            $errors[] = "Invalid email or password.";
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
    <title>CorkStay - Login</title>
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
            gap: 10px;
            align-items: center;
        }

        label {
            font-weight: bold;
            color: #333;
        }

        input, textarea, select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
            max-width: 300px;
        }

        input[type="submit"] {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            width: auto;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        ul {
            list-style-type: none;
            padding: 0;
            color: red;
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
        <h2>Login</h2>
        <?php if (!empty($errors)): ?>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <input type="submit" name="login" value="Login">
            <p><a href="password_reset_request.php">Forgot Password?</a></p>
        </form>
        <nav>
            <a href="signup.php">Sign Up</a>
            <a href="search.php">Search Properties</a>
            <a href="testimonials.php">View Testimonials</a>
            <a href="home.php">Home</a>
        </nav>
    </header>
</body>
</html>
