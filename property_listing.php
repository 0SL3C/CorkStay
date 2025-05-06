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

// Handle property addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_property'])) {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $category = $_POST['category'];
    $tenancy_length = $_POST['tenancy_length']; // No casting
    $rental_price = (float)$_POST['rental_price'];

    if (empty($title) || empty($description) || empty($category) || empty($tenancy_length) || empty($rental_price)) {
        $errors[] = "All fields are required.";
    }

    if (empty($errors)) {
        // Initial insert with empty image
        $stmt = $conn->prepare("INSERT INTO properties (landlord_id, title, description, image, category, tenancy_length, rental_price, created_at) VALUES (?, ?, ?, '', ?, ?, ?, NOW())");
        $stmt->bind_param("issssd", $_SESSION['user_id'], $title, $description, $category, $tenancy_length, $rental_price);

        if ($stmt->execute()) {
            $property_id = $stmt->insert_id;
            $stmt->close();

            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imageTmp = $_FILES['image']['tmp_name'];
                $targetDir = './properties/';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $imagePath = $targetDir . $property_id . ".jpg";

                if (move_uploaded_file($imageTmp, $imagePath)) {
                    $updateStmt = $conn->prepare("UPDATE properties SET image = ? WHERE id = ?");
                    $updateStmt->bind_param("si", $imagePath, $property_id);
                    $updateStmt->execute();
                    $updateStmt->close();
                    $success = true;
                } else {
                    $errors[] = "Image upload failed.";
                }
            } else {
                $success = true; // Still count as success if no image is uploaded
            }

        } else {
            $errors[] = "Failed to add property.";
        }
    }
}
// Fetch properties for the logged-in landlord:
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
    <title>Manage Properties</title>
    <style>
        body {
            background: url('./images/1126773.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: rgba(255,255,255,0.9);
            padding: 30px;
            border-radius: 10px;
        }
        h2, h3 {
            text-align: center;
        }
        input, textarea, select {
            width: 100%;
            margin-top: 5px;
            padding: 8px;
            box-sizing: border-box;
            margin-bottom: 15px;
        }
        textarea {
            resize: none;
            height: 100px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .error { color: red; }
        .success { color: green; text-align: center; }
        ul { padding-left: 0; list-style-type: none; }
        li {
            background-color: #f0f8ff;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 6px;
        }
        img {
            max-width: 100%;
            height: auto;
            margin-top: 10px;
        }
        .back-button {
            text-align: center;
            margin-top: 30px;
        }
        .back-button a {
            text-decoration: none;
            background-color: #2196F3;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Manage Properties</h2>

    <?php if ($success): ?>
        <p class="success">Property added successfully!</p>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <h3>Add New Property</h3>
    <form method="POST" action="property_listing.php" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required>

        <label for="description">Description:</label>
        <textarea name="description" id="description" required></textarea>

        <label for="image">Property Image:</label><br>
        <input type="file" name="image" id="image" accept="image/*" required><br>

        <label for="category">Category:</label><br>
        <select id="category" name="category" required>
            <option value="1 bed">1 bed</option>
            <option value="2 bed">2 bed</option>
            <option value="3 bed">3 bed</option>
            <option value="4 bed">4 bed</option>
        </select><br>

        <label for="tenancy_length">Tenancy Length:</label><br>
        <select id="tenancy_length" name="tenancy_length" required>
            <option value="3 months">3 months</option>
            <option value="6 months">6 months</option>
            <option value="1 year">1 year</option>
        </select><br>


        <label for="rental_price">Rental Price (€):</label>
        <input type="number" step="0.01" name="rental_price" id="rental_price" required>

        <input type="submit" name="add_property" value="Add Property">
    </form>

    <h3>Your Properties</h3>
    <?php if (!empty($properties)): ?>
        <ul>
            <?php foreach ($properties as $property): ?>
            <li>
                <strong><?php echo htmlspecialchars($property['title']); ?></strong><br>
                <?php echo nl2br(htmlspecialchars($property['description'])); ?><br>
                Category: <?php echo htmlspecialchars($property['category']); ?><br>
                Tenancy: <?php echo htmlspecialchars($property['tenancy_length']); ?> months<br>
                Price: €<?php echo htmlspecialchars($property['rental_price']); ?><br>
                Listed on: <?php echo htmlspecialchars($property['created_at']); ?><br>
                <?php if ($property['image']): ?>
                    <img src="<?php echo htmlspecialchars($property['image']); ?>" alt="Property Image">
                <?php endif; ?>
                <br>
                <a href="edit_property.php?id=<?php echo $property['id']; ?>">Edit</a> |
                <a href="delete_property.php?id=<?php echo $property['id']; ?>" onclick="return confirm('Are you sure you want to delete this property?');">Delete</a>
            </li>

            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p style="text-align:center;">You have no properties listed.</p>
    <?php endif; ?>

    <div class="back-button">
        <a href="home.php">← Go Back</a>
    </div>
</div>
</body>
</html>

