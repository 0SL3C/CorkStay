<?php
session_start();
$conn = mysqli_connect('localhost', 'root', '', 'corkstay');
if ($conn->connect_error) {
    die("Error connecting to database: " . $conn->connect_error);
}

// Fetch all testimonials
$query = "SELECT t.content, t.rating, t.created_at, u.first_name 
          FROM testimonials t 
          JOIN users u ON t.user_id = u.user_id 
          ORDER BY t.created_at DESC";
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
</head>
<body>
    <h2>Testimonials</h2>
    <?php if (!empty($testimonials)): ?>
        <ul>
            <?php foreach ($testimonials as $testimonial): ?>
                <li>
                    <strong><?php echo htmlspecialchars($testimonial['first_name']); ?></strong> 
                    (<?php echo htmlspecialchars($testimonial['created_at']); ?>):
                    <p><?php echo htmlspecialchars($testimonial['content']); ?></p>
                    <p>Rating: <?php echo htmlspecialchars($testimonial['rating']); ?>/5</p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No testimonials available.</p>
    <?php endif; ?>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'tenant'): ?>
        <p><a href="testimonial_add.php">Add a Testimonial</a></p>
    <?php endif; ?>
</body>
</html>