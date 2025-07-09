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