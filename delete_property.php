<?php
require_once 'config.php';

// Initialize session with timeout handling
initSession();

// Get database connection
$conn = getDbConnection();

// Checks if the user is logged in as landlord
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    header("Location: login.php");
    exit;
}

$property_id = $_GET['id'] ?? null;

if ($property_id) {
    // Ensures that the user can only delete properties that he owns
    $stmt = $conn->prepare("DELETE FROM properties WHERE id = ? AND landlord_id = ?");
    $stmt->bind_param("ii", $property_id, $_SESSION['user_id']);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: property_listing.php?deleted=1");
        exit;
    } else {
        $stmt->close();
        echo "Error: Could not delete property.";
    }
} else {
    echo "Error: Property ID not provided.";
}
?>
