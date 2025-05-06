<?php
session_start();
$conn = mysqli_connect('localhost', 'root', '159753', 'corkstay');
if ($conn->connect_error) {
    die("Error connecting to database: " . $conn->connect_error);
}

$results = [];
// Base query
$query = "SELECT p.title, p.rental_price, p.category, p.image, u.first_name AS landlord_name FROM properties p JOIN users u ON p.landlord_id = u.user_id WHERE 1=1";
$params = [];
$types = "";

// If filters are applied
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    if (!empty($_GET['price'])) {
        $query .= " AND rental_price <= ?";
        $types .= "d";
        $params[] = $_GET['price'];
    }

    if (!empty($_GET['bedrooms'])) {
        $category = intval($_GET['bedrooms']) . " bed";
        $query .= " AND category = ?";
        $types .= "s";
        $params[] = $category;
    }
}

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CorkStay - Search Properties</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            position: relative;
            min-height: 100vh;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background-image: url('./images/1126773.jpg');
            background-size: cover;
            background-position: center;
            filter: blur(8px);
            z-index: -1;
        }

        .container {
            max-width: 700px;
            margin: 80px auto;
            background-color: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        h2, h3 {
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-top: 10px;
            font-weight: bold;
        }

        input[type="number"] {
            padding: 10px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        input[type="submit"] {
            margin-top: 20px;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        ul {
            list-style-type: none;
            padding: 0;
            margin-top: 20px;
        }

        li {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        li img {
            width: 120px;
            height: 90px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .property-details {
            flex: 1;
        }

        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #2196F3;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Search Properties</h2>
    <form method="GET" action="search.php">
        <label for="price">Max Rent Price (€):</label>
        <input type="number" id="price" name="price">

        <label for="bedrooms">Bedrooms:</label>
        <input type="number" id="bedrooms" name="bedrooms">

        <input type="submit" name="search" value="Search">
    </form>

    <?php if (!empty($results)): ?>
        <h3>Search Results:</h3>
        <ul>
            <?php foreach ($results as $property): ?>
                <li>
                    <?php if (!empty($property['image']) && file_exists($property['image'])): ?>
                        <img src="<?php echo htmlspecialchars($property['image']); ?>" alt="Property image">
                    <?php else: ?>
                        <img src="./images/placeholder.jpg" alt="No image available">
                    <?php endif; ?>
                    <div class="property-details">
                        <strong><?php echo htmlspecialchars($property['title']); ?></strong><br>
                        €<?php echo htmlspecialchars($property['rental_price']); ?><br>
                        <?php echo htmlspecialchars($property['category']); ?><br>
                        <em>Landlord: <?php echo htmlspecialchars($property['landlord_name']); ?></em>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p style="text-align:center; margin-top: 20px;">No properties found.</p>
    <?php endif; ?>

    <p style="text-align: center;">
        <a href="home.php" class="back-btn">← Go Back</a>
    </p>
</div>
</body>
</html>

