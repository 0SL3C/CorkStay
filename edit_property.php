<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    header("Location: home.php");
    exit();
}

$conn = mysqli_connect('localhost', 'root', '159753', 'corkstay');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errors = [];
$success = false;
$property = null;

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Get property details
    $stmt = $conn->prepare("SELECT * FROM properties WHERE id = ? AND landlord_id = ?");
    $stmt->bind_param("ii", $id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $property = $result->fetch_assoc();
    $stmt->close();

    if (!$property) {
        die("Property not found.");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_property'])) {
        $title = htmlspecialchars($_POST['title']);
        $description = htmlspecialchars($_POST['description']);
        $category = $_POST['category'];
        $tenancy_length = (int)$_POST['tenancy_length'];
        $rental_price = (float)$_POST['rental_price'];

        // Image update optional
        $image = $property['image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageTmp = $_FILES['image']['tmp_name'];
            $imageName = basename($_FILES['image']['name']);
            $targetDir = 'uploads/';
            $targetFile = $targetDir . time() . "_" . $imageName;

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            if (move_uploaded_file($imageTmp, $targetFile)) {
                $image = $targetFile;
            } else {
                $errors[] = "Failed to upload image.";
            }
        }

        if (empty($title) || empty($description) || empty($category) || empty($tenancy_length) || empty($rental_price)) {
            $errors[] = "All fields are required.";
        }

        if (empty($errors)) {
            $stmt = $conn->prepare("UPDATE properties SET title = ?, description = ?, image = ?, category = ?, tenancy_length = ?, rental_price = ? WHERE id = ? AND landlord_id = ?");
            $stmt->bind_param("ssssdiii", $title, $description, $image, $category, $tenancy_length, $rental_price, $id, $_SESSION['user_id']);
            if ($stmt->execute()) {
                $success = true;
                $stmt->close();
                header("Location: property_listing.php");
                exit();
            } else {
                $errors[] = "Update failed.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Property</title>
</head>
<body>
    <h2>Edit Property</h2>

    <?php if (!empty($errors)): ?>
        <ul style="color:red;">
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ($property): ?>
        <form method="POST" enctype="multipart/form-data">
            <label>Title:</label><br>
            <input type="text" name="title" value="<?php echo htmlspecialchars($property['title']); ?>" required><br>

            <label>Description:</label><br>
            <textarea name="description" required><?php echo htmlspecialchars($property['description']); ?></textarea><br>

            <label>Category:</label><br>
            <select name="category" required>
                <option value="apartment" <?php if ($property['category'] === 'apartment') echo 'selected'; ?>>Apartment</option>
                <option value="house" <?php if ($property['category'] === 'house') echo 'selected'; ?>>House</option>
                <option value="studio" <?php if ($property['category'] === 'studio') echo 'selected'; ?>>Studio</option>
                <option value="room" <?php if ($property['category'] === 'room') echo 'selected'; ?>>Room</option>
            </select><br>

            <label>Tenancy Length (months):</label><br>
            <input type="number" name="tenancy_length" value="<?php echo $property['tenancy_length']; ?>" required><br>

            <label>Rental Price (€):</label><br>
            <input type="number" name="rental_price" step="0.01" value="<?php echo $property['rental_price']; ?>" required><br>

            <label>Change Image (optional):</label><br>
            <input type="file" name="image" accept="image/*"><br>
            <?php if ($property['image']): ?>
                <img src="<?php echo htmlspecialchars($property['image']); ?>" width="200"><br>
            <?php endif; ?>

            <input type="submit" name="update_property" value="Update Property">
        </form>
    <?php endif; ?>

    <p><a href="property_listing.php">← Back to Properties</a></p>
</body>
</html>

