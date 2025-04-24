<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    header("Location: home.php");
    exit();
}

$conn = mysqli_connect('localhost', 'root', '', 'corkstay');
if ($conn->connect_error) {
    die("Error connecting to database: " . $conn->connect_error);
}

$errors = [];
$success = false;

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
    $available_from = $_POST['available_from'];
    $furnished = isset($_POST['furnished']) ? 1 : 0;

    if (empty($title) || empty($description) || empty($address) || empty($city) || empty($eircode) || empty($property_type) || empty($bedrooms) || empty($bathrooms) || empty($price) || empty($available_from)) {
        $errors[] = "All fields are required.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO properties (landlord_id, title, description, address, city, eircode, property_type, bedrooms, bathrooms, price, available_from, furnished) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssiiidss", $_SESSION['user_id'], $title, $description, $address, $city, $eircode, $property_type, $bedrooms, $bathrooms, $price, $available_from, $furnished);
        if ($stmt->execute()) {
            $success = true;
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CorkStay - Manage Properties</title>
</head>
<body>
    <h2>Manage Properties</h2>

    <?php if ($success): ?>
        <p>Property added successfully!</p>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <h3>Add New Property</h3>
    <form method="POST" action="property_listing.php">
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" required><br>

        <label for="description">Description:</label><br>
        <textarea id="description" name="description" required></textarea><br>

        <label for="address">Address:</label><br>
        <input type="text" id="address" name="address" required><br>

        <label for="city">City:</label><br>
        <input type="text" id="city" name="city" required><br>

        <label for="eircode">Eircode:</label><br>
        <input type="text" id="eircode" name="eircode" required><br>

        <label for="property_type">Property Type:</label><br>
        <select id="property_type" name="property_type" required>
            <option value="apartment">Apartment</option>
            <option value="house">House</option>
            <option value="studio">Studio</option>
            <option value="room">Room</option>
        </select><br>

        <label for="bedrooms">Bedrooms:</label><br>
        <input type="number" id="bedrooms" name="bedrooms" required><br>

        <label for="bathrooms">Bathrooms:</label><br>
        <input type="number" id="bathrooms" name="bathrooms" required><br>

        <label for="price">Price (€):</label><br>
        <input type="number" step="0.01" id="price" name="price" required><br>

        <label for="available_from">Available From:</label><br>
        <input type="date" id="available_from" name="available_from" required><br>

        <label for="furnished">Furnished:</label>
        <input type="checkbox" id="furnished" name="furnished"><br>

        <input type="submit" name="add_property" value="Add Property">
    </form>

    <h3>Your Properties</h3>
    <?php if (!empty($properties)): ?>
        <ul>
            <?php foreach ($properties as $property): ?>
                <li>
                    <strong><?php echo htmlspecialchars($property['title']); ?></strong><br>
                    <?php echo htmlspecialchars($property['description']); ?><br>
                    <?php echo htmlspecialchars($property['address']); ?>, <?php echo htmlspecialchars($property['city']); ?><br>
                    Bedrooms: <?php echo htmlspecialchars($property['bedrooms']); ?>, Bathrooms: <?php echo htmlspecialchars($property['bathrooms']); ?><br>
                    Price: €<?php echo htmlspecialchars($property['price']); ?><br>
                    Available From: <?php echo htmlspecialchars($property['available_from']); ?><br>
                    Furnished: <?php echo $property['furnished'] ? 'Yes' : 'No'; ?><br>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>You have no properties listed.</p>
    <?php endif; ?>
</body>
</html>