<<<<<<< HEAD
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

=======
<?php
require_once 'config.php';

// Initialize session with timeout handling
initSession();

// Get database connection
$conn = getDbConnection();

$results = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $min_price = $_GET['min_price'] ?? null;
    $max_price = $_GET['max_price'] ?? null;
    $bedrooms = $_GET['bedrooms'] ?? null;
    $property_type = $_GET['property_type'] ?? null;
    $furnished = $_GET['furnished'] ?? null;
    $city = $_GET['city'] ?? null;

    $query = "SELECT * FROM properties WHERE 1=1";
    $params = [];
    $types = "";

    if (!empty($min_price)) {
        $query .= " AND price >= ?";
        $params[] = $min_price;
        $types .= "d";
    }

    if (!empty($max_price)) {
        $query .= " AND price <= ?";
        $params[] = $max_price;
        $types .= "d";
    }

    if (!empty($bedrooms)) {
        $query .= " AND bedrooms = ?";
        $params[] = $bedrooms;
        $types .= "i";
    }

    if (!empty($property_type)) {
        $query .= " AND property_type = ?";
        $params[] = $property_type;
        $types .= "s";
    }

    if ($furnished !== null && $furnished !== "") {
        $query .= " AND furnished = ?";
        $params[] = $furnished;
        $types .= "i";
    }

    if (!empty($city)) {
        $query .= " AND city LIKE ?";
        $params[] = '%' . $city . '%';
        $types .= "s";
    }

    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CorkStay - Search</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="text-center mb-4">Search Properties</h2>

    <form class="card p-4 shadow-sm mb-5" method="GET" action="search.php">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Min Price</label>
                <input type="number" step="0.01" name="min_price" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Max Price</label>
                <input type="number" step="0.01" name="max_price" class="form-control">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Bedrooms</label>
                <input type="number" name="bedrooms" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Property Type</label>
                <select name="property_type" class="form-select">
                    <option value="">--Any--</option>
                    <option value="Apartment">Apartment</option>
                    <option value="House">House</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Furnished</label>
                <select name="furnished" class="form-select">
                    <option value="">--Any--</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">City</label>
            <input type="text" name="city" class="form-control">
        </div>

        <div class="d-grid">
            <button type="submit" name="search" class="btn btn-primary">Search</button>
        </div>
    </form>

    <h4 class="mb-3">Results</h4>

    <?php if (!empty($results)): ?>
        <div class="row">
            <?php foreach ($results as $property): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($property['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($property['description']); ?></p>
                            <p class="card-text"><strong>Location:</strong> <?php echo htmlspecialchars($property['address'] . ", " . $property['city']); ?></p>
                            <p class="card-text">
                                <strong>Bedrooms:</strong> <?php echo $property['bedrooms']; ?> |
                                <strong>Bathrooms:</strong> <?php echo $property['bathrooms']; ?>
                            </p>
                            <p class="card-text">
                                <strong>Price:</strong> €<?php echo $property['price']; ?> |
                                <strong>Furnished:</strong> <?php echo $property['furnished'] ? 'Yes' : 'No'; ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-muted">No results found.</p>
    <?php endif; ?>
</div>
</body>
</html>
>>>>>>> origin/Michel
