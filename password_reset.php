<?php
require_once 'config.php';

// Initialize session with timeout handling
initSession();

// Get database connection
$conn = getDbConnection();

$errors = [];
$success = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT user_id, expires_at FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $expires_at);
        $stmt->fetch();

        if (strtotime($expires_at) < time()) {
            $errors[] = "This reset link has expired.";
        }
    } else {
        $errors[] = "Invalid reset token.";
    }
    $stmt->close();

    if ($_SERVER["REQUEST_METHOD"] === "POST" && empty($errors)) {
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        } elseif (strlen($password) < 6) {
            $errors[] = "Password must be at least 6 characters.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Update password
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $stmt->bind_param("si", $hashed_password, $user_id);
            $stmt->execute();
            $stmt->close();

            // Delete the token
            $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $stmt->close();

            $success = "Your password has been successfully reset!";
        }
    }
} else {
    $errors[] = "No reset token provided.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
</head>
<body>
    <h2>Reset Your Password</h2>

    <?php if (!empty($errors)): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color: green;"><?= $success ?></p>
        <p><a href="login.php">Go to login</a></p>
    <?php elseif (isset($_GET['token']) && empty($errors)): ?>
        <form method="post">
            <label>New Password:</label><br>
            <input type="password" name="password" required><br><br>

            <label>Confirm Password:</label><br>
            <input type="password" name="confirm_password" required><br><br>

            <input type="submit" value="Reset Password">
        </form>
    <?php endif; ?>
</body>
</html>
