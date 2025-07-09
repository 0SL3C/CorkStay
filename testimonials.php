<?php
require_once 'config.php';

// Initialize session with timeout handling
initSession();

// Get database connection
$conn = getDbConnection();

// Fetch all testimonials
$result = $conn->query("
    SELECT t.content, t.created_at, t.rating, u.first_name
    FROM testimonials t
    JOIN users u ON t.tenant_id = u.user_id
    ORDER BY t.created_at DESC
");

$testimonials = [];
if ($result && $result->num_rows > 0) {
    $testimonials = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CorkStay - Testimonials</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="text-center mb-4">Testimonials</h2>

    <?php if (!empty($testimonials)): ?>
        <div class="row row-cols-1 row-cols-md-2 g-4">
            <?php foreach ($testimonials as $testimonial): ?>
                <div class="col">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($testimonial['first_name']) ?></h5>
                            <p class="card-text"><?= nl2br(htmlspecialchars($testimonial['content'])) ?></p>
                            <small class="text-muted">
                                <?= htmlspecialchars($testimonial['created_at']) ?> —
                                Rating: <?= htmlspecialchars($testimonial['rating'] ?? 'N/A') ?>/5
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-muted text-center">No testimonials available.</p>
    <?php endif; ?>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'tenant'): ?>
        <div class="text-center mt-4">
            <a href="testimonial_add.php" class="btn btn-primary">Add a Testimonial</a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
