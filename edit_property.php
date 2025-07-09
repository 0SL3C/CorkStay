<?php
require_once 'config.php';

// Initialize session with timeout handling
initSession();

// Get database connection
$conn = getDbConnection();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    header("Location: login.php");
    exit;
}

$property_id = $_GET['id'] ?? null;
$errors = [];
$property = [];

if ($property_id) {
    // Fetch property
    $stmt = $conn->prepare("SELECT * FROM properties WHERE id = ? AND landlord_id = ?");
    $stmt->bind_param("ii", $property_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $property = $result->fetch_assoc();
    $stmt->close();

    if (!$property) {
        $errors[] = "Property not found or access denied.";
    }
} else {
    $errors[] = "No property ID provided.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_property'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $eircode = trim($_POST['eircode']);
    $property_type = $_POST['property_type'];
    $bedrooms = (int)$_POST['bedrooms'];
    $bathrooms = (int)$_POST['bathrooms'];
    $price = (float)$_POST['price'];
    $tenancy_length = $_POST['tenancy_length'];
    $available_from = $_POST['available_from'];
    $furnished = isset($_POST['furnished']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE properties SET title=?, description=?, address=?, city=?, eircode=?, property_type=?, bedrooms=?, bathrooms=?, price=?, tenancy_length=?, available_from=?, furnished=? WHERE id=? AND landlord_id=?");
    $stmt->bind_param("ssssssiiissiii", $title, $description, $address, $city, $eircode, $property_type, $bedrooms, $bathrooms, $price, $tenancy_length, $available_from, $furnished, $property_id, $_SESSION['user_id']);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: property_listing.php?updated=1");
        exit;
    } else {
        $errors[] = "Failed to update property.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Property - CorkStay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2>Edit Property</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($property)): ?>
        <form method="POST" class="row g-3 bg-white p-4 rounded shadow-sm mb-4">
            <input type="hidden" name="update_property" value="1">

            <div class="col-md-6">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($property['title']) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">City</label>
                <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($property['city']) ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" required><?= htmlspecialchars($property['description']) ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Address</label>
                <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($property['address']) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Eircode</label>
                <input type="text" name="eircode" class="form-control" value="<?= htmlspecialchars($property['eircode']) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Type</label>
                <select name="property_type" class="form-select" required>
                    <option value="Apartment" <?= $property['property_type'] === 'Apartment' ? 'selected' : '' ?>>Apartment</option>
                    <option value="House" <?= $property['property_type'] === 'House' ? 'selected' : '' ?>>House</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Bedrooms</label>
                <input type="number" name="bedrooms" class="form-control" value="<?= htmlspecialchars($property['bedrooms']) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Bathrooms</label>
                <input type="number" name="bathrooms" class="form-control" value="<?= htmlspecialchars($property['bathrooms']) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Price (€)</label>
                <input type="number" step="0.01" name="price" class="form-control" value="<?= htmlspecialchars($property['price']) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Tenancy Length</label>
                <select name="tenancy_length" class="form-select" required>
                    <option value="3 months" <?= $property['tenancy_length'] === '3 months' ? 'selected' : '' ?>>3 months</option>
                    <option value="6 months" <?= $property['tenancy_length'] === '6 months' ? 'selected' : '' ?>>6 months</option>
                    <option value="1 year" <?= $property['tenancy_length'] === '1 year' ? 'selected' : '' ?>>1 year</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Available From</label>
                <input type="date" name="available_from" class="form-control" value="<?= htmlspecialchars($property['available_from']) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-check-label">
                    <input type="checkbox" name="furnished" class="form-check-input" <?= $property['furnished'] ? 'checked' : '' ?>> Furnished
                </label>
            </div>
            <div class="col-md-6 text-end">
                <input type="submit" class="btn btn-primary" value="Update Property">
            </div>
        </form>

        <a href="property_listing.php" class="btn btn-secondary">← Back to Listings</a>
    <?php endif; ?>
</div>
</body>
</html>
