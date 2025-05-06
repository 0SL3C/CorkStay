<?php
session_start();
$conn = mysqli_connect('localhost', 'root', '159753', 'corkstay');
if ($conn->connect_error) {
    die("Error connecting to database: " . $conn->connect_error);
}

$results = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $price = $_GET['price'];
    $bedrooms = $_GET['bedrooms'];

    $query = "SELECT title, rental_price, category FROM properties WHERE rental_price <= ? AND category = ?";
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
            max-width: 600px;
            margin: 80px auto;
            background-color: rgba(255, 255, 255, 0.9);
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
            background-color: #f2f2f2;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Search Properties</h2>
        <form method="GET" action="search.php">
            <label for="price">Max Rent Price (€):</label>
            <input type="number" id="price" name="price" required>

            <label for="bedrooms">Bedrooms:</label>
            <input type="number" id="bedrooms" name="bedrooms" required>

            <input type="submit" name="search" value="Search">
        </form>

        <?php if (!empty($results)): ?>
            <h3>Search Results:</h3>
            <ul>
                <?php foreach ($results as $property): ?>
                    <li>
                        <?php echo htmlspecialchars($property['title']); ?> - 
                        €<?php echo htmlspecialchars($property['rental_price']); ?> - 
                        <?php echo htmlspecialchars($property['bedrooms']); ?> bedrooms
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <p style="text-align: center; margin: 20px 0px 0px 0px;">
    <a href="home.php" style="
        display: inline-block;
        padding: 10px 20px;
        background-color: #2196F3;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-weight: bold;
    ">← Go Back</a>
    </div>

</p>
</body>
</html>

