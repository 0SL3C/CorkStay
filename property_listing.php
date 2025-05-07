
<?php
session_start();
$conn = mysqli_connect('localhost', 'root', '', 'corkstay');

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    header("Location: login.php");
    exit;
}

$errors = [];

// Handle property addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_property'])) {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $address = htmlspecialchars($_POST['address']);
    $city = htmlspecialchars($_POST['city']);
    $eircode = htmlspecialchars($_POST['eircode']);
    $property_type = $_POST['property_type'];
    $bedrooms = (int)$_POST['bedrooms'];
    $bathrooms = (int)$_POST['bathrooms'];
    $price = (float)$_POST['price'];
    $tenancy_length = $_POST['tenancy_length'];
    $available_from = $_POST['available_from'];
    $furnished = isset($_POST['furnished']) ? 1 : 0;

    if (empty($title) || empty($description) || empty($address) || empty($city) || empty($eircode) || empty($property_type) || empty($bedrooms) || empty($bathrooms) || empty($price) || empty($available_from)) {
        $errors[] = "All fields are required.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO properties (landlord_id, title, description, address, city, eircode, property_type, bedrooms, bathrooms, price, tenancy_length, available_from, furnished) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssiiidsss", $_SESSION['user_id'], $title, $description, $address, $city, $eircode, $property_type, $bedrooms, $bathrooms, $price, $tenancy_length, $available_from, $furnished);

        if ($stmt->execute()) {
            header("Location: property_listing.php?added=1");
            exit;
        } else {
            $errors[] = "Failed to add property.";
        }
        $stmt->close();
    }
}

// Fetch properties for the logged-in landlord
$stmt = $conn->prepare("SELECT * FROM properties WHERE landlord_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$properties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Properties - CorkStay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
    <h2 class="mb-4">Your Property Listings</h2>

    <?php if (isset($_GET['added'])): ?>
        <div class="alert alert-success">Property added successfully!</div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" class="row g-3 bg-white p-4 rounded shadow-sm mb-5">
        <input type="hidden" name="add_property" value="1">

        <div class="col-md-6">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">City</label>
            <input type="text" name="city" class="form-control" required>
        </div>
        <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Eircode</label>
            <input type="text" name="eircode" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Type</label>
            <select name="property_type" class="form-select" required>
                <option value="Apartment">Apartment</option>
                <option value="House">House</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Bedrooms</label>
            <input type="number" name="bedrooms" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Bathrooms</label>
            <input type="number" name="bathrooms" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Price (€)</label>
            <input type="number" step="0.01" name="price" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Tenancy Length</label>
            <select name="tenancy_length" class="form-select" required>
                <option value="3 months">3 months</option>
                <option value="6 months">6 months</option>
                <option value="1 year">1 year</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Available From</label>
            <input type="date" name="available_from" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-check-label">
                <input type="checkbox" name="furnished" class="form-check-input"> Furnished
            </label>
        </div>
        <div class="col-md-6 text-end">
            <input type="submit" class="btn btn-primary" value="Add Property">
        </div>
    </form>

    <h3>Your Properties</h3>
    <?php if (!empty($properties)): ?>
        <ul class="list-group">

            <?php foreach ($properties as $property): ?>
                <li class="list-group-item">
                    <strong><?= htmlspecialchars($property['title']) ?></strong> — <?= htmlspecialchars($property['city']) ?><br>
                    <?= htmlspecialchars($property['bedrooms']) ?> bed / <?= htmlspecialchars($property['bathrooms']) ?> bath<br>
                    €<?= htmlspecialchars($property['price']) ?>, <?= htmlspecialchars($property['tenancy_length']) ?><br>
                    Furnished: <?= $property['furnished'] ? 'Yes' : 'No' ?><br>

                    <a href="edit_property.php?id=<?= htmlspecialchars($property['id']) ?>" class="btn btn-sm btn-warning mt-2">Edit</a>
                    <a href="delete_property.php?id=<?= htmlspecialchars($property['id']) ?>" class="btn btn-sm btn-danger mt-2" onclick="return confirm('Are you sure you want to delete this property?');">Delete</a>

                    
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-muted">No properties listed yet.</p>
    <?php endif; ?>
</div>
</body>
</html>
