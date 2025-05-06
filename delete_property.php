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

if (isset($_GET['id'])) {
    $property_id = (int)$_GET['id'];

    // Make sure the property belongs to this landlord
    $stmt = $conn->prepare("DELETE FROM properties WHERE id = ? AND landlord_id = ?");
    $stmt->bind_param("ii", $property_id, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
}

header("Location: property_listing.php");
exit();

