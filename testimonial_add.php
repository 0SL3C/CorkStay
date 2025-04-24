<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tenant') {
    header("Location: home.php");
    exit();
}

$conn = mysqli_connect('localhost', 'root', '', 'corkstay');
if ($conn->connect_error) {
    die("Error connecting to database: " . $conn->connect_error);
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_testimonial'])) {
    $content = htmlspecialchars($_POST['content']);
    $rating = $_POST['rating'];

    if (empty($content)) {
        $errors[] = "Content cannot be empty.";
    }
    if ($rating < 1 || $rating > 5) {
        $errors[] = "Rating must be between 1 and 5.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO testimonials (user_id, content, rating) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $_SESSION['user_id'], $content, $rating);
        if ($stmt->execute()) {
            header("Location: testimonials.php");
            exit();
        } else {
            $errors[] = "Failed to add testimonial.";
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
    <title>CorkStay - Add Testimonial</title>
</head>
<body>
    <h2>Add Testimonial</h2>
    <?php if (!empty($errors)): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form method="POST" action="testimonial_add.php">
        <label for="content">Content:</label><br>
        <textarea id="content" name="content" required></textarea><br>
        <label for="rating">Rating (1-5):</label><br>
        <input type="number" id="rating" name="rating" min="1" max="5" required><br>
        <input type="submit" name="add_testimonial" value="Submit">
    </form>
</body>
</html>