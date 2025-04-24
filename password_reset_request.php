<?php
$conn = mysqli_connect('localhost', 'root', '', 'corkstay');
if ($conn->connect_error) {
    die("Error connecting to database: " . $conn->connect_error);
}

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
</head>
<body>
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
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required><br>
            <input type="submit" name="reset_request" value="Request Reset">
        </form>
    <?php endif; ?>
</body>
</html>