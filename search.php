<?php
session_start();
$conn = mysqli_connect('localhost', 'root', '', 'corkstay');
if ($conn->connect_error) {
    die("Error connecting to database: " . $conn->connect_error);
}

$results = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $price = $_GET['price'];
    $bedrooms = $_GET['bedrooms'];

    $query = "SELECT title, price, bedrooms FROM properties WHERE price <= ? AND bedrooms = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("di", $price, $bedrooms);
    $stmt->execute();
    $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CorkStay - Search Properties</title>
</head>
<body>
    <h2>Search Properties</h2>
    <form method="GET" action="search.php">
        <label for="price">Max Price:</label><br>
        <input type="number" id="price" name="price" required><br>
        <label for="bedrooms">Bedrooms:</label><br>
        <input type="number" id="bedrooms" name="bedrooms" required><br>
        <input type="submit" name="search" value="Search">
    </form>

    <?php if (!empty($results)): ?>
        <h3>Search Results:</h3>
        <ul>
            <?php foreach ($results as $property): ?>
                <li>
                    <?php echo htmlspecialchars($property['title']); ?> - 
                    €<?php echo htmlspecialchars($property['price']); ?> - 
                    <?php echo htmlspecialchars($property['bedrooms']); ?> bedrooms
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>