<?php
require_once 'config.php';

// Get database connection (this file doesn't use sessions)
$conn = getDbConnection();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_request'])) {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    } else {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($user_id);
        if ($stmt->fetch()) {
            $reset_token = bin2hex(random_bytes(32));
            $reset_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $stmt->close();
            $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", $reset_token, $reset_expiry, $user_id);
            if ($stmt->execute()) {
                $reset_link = "http://localhost/swd-corkstay/password_reset.php?token=$reset_token";
                // Simulate email sending (replace with actual email sending logic)
                mail($email, "Password Reset Request", "Click the link to reset your password: $reset_link");
                $success = true;
            } else {
                $errors[] = "Failed to generate reset token.";
            }
        } else {
            $errors[] = "No account found with that email.";
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
    <title>Password Reset Request</title>
<style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-image: url('./images/1126773.jpg');
        background-size: cover;
        background-position: center;
        filter: none;
    
    }
    *{box-sizing: border-box;}

    body::before {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(6px);
        z-index: -1;
    }

    .container {
        max-width: 450px;
        margin: 100px auto;
        background-color: rgba(255, 255, 255, 0.95);
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        text-align: center;
    }

    h2 {
        margin-bottom: 20px;
        color: #333;
    }

    label {
        display: block;
        text-align: left;
        margin-bottom: 6px;
        font-weight: bold;
        color: #444;
    }

    input[type="email"],
    input[type="submit"] {
        width: 100%;
        padding: 12px;
        margin-top: 8px;
        margin-bottom: 20px;
        border-radius: 6px;
        border: 1px solid #ccc;
        font-size: 16px;
    }

    input[type="submit"] {
        background-color: #007BFF;
        color: white;
        font-weight: bold;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    input[type="submit"]:hover {
        background-color: #0056b3;
    }

    ul {
        padding-left: 0;
        list-style: none;
        color: red;
        margin-bottom: 15px;
    }

    p {
        color: green;
        font-weight: bold;
    }
</style>
</head>
<body>
    <div class="container">
    <h2>Request Password Reset</h2>
    <?php if ($success): ?>
        <p>A password reset link has been sent to your email.</p>
    <?php else: ?>
        <?php if (!empty($errors)): ?>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <form method="POST" action="password_reset_request.php">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <input type="submit" name="reset_request" value="Request Reset">
        </form>
    <?php endif; ?>
    </div>
</body>
</html>
