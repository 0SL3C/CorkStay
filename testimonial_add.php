<?php
session_start();
$conn = mysqli_connect('localhost', 'root', '', 'corkstay');
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Acesso restrito a 'tenant'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tenant') {
    header("Location: home.php");
    exit();
}

$errors = [];
$success = false;
$user_id = $_SESSION['user_id'];

// Pega nome do usuário para exibir na tela
$result = $conn->query("SELECT first_name FROM users WHERE user_id = $user_id");
$user = $result->fetch_assoc();
$first_name = $user['first_name'] ?? '';

// Submissão do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_testimonial'])) {
    $service_name = htmlspecialchars(trim($_POST['service_name']));
    $content = htmlspecialchars(trim($_POST['content']));
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : null;
    $testimonial_date = date('Y-m-d');

    // Validações
    if (empty($service_name)) {
        $errors[] = "Service name is required.";
    }
    if (empty($content)) {
        $errors[] = "Testimonial content is required.";
    }
    if ($rating === null || $rating < 1 || $rating > 5) {
        $errors[] = "Rating must be between 1 and 5.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO testimonials (tenant_id, service_name, content, rating, testimonial_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issis", $user_id, $service_name, $content, $rating, $testimonial_date);

        if ($stmt->execute()) {
            $success = true;
        } else {
            $errors[] = "Failed to submit testimonial.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Testimonial - CorkStay</title>
</head>
<body>
    <h2>Add Testimonial</h2>
    <p>Logged in as <strong><?= htmlspecialchars($first_name) ?></strong></p>

    <?php if ($success): ?>
        <p style="color: green;">Testimonial submitted successfully!</p>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="service_name">Service Name:</label><br>
        <input type="text" name="service_name" id="service_name" required><br><br>

        <label for="content">Testimonial:</label><br>
        <textarea name="content" id="content" required></textarea><br><br>

        <label for="rating">Rating (1 to 5):</label><br>
        <select name="rating" id="rating" required>
            <option value="">--Select--</option>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
        </select><br><br>

        <input type="submit" name="submit_testimonial" value="Submit Testimonial">
    </form>
</body>
</html>
