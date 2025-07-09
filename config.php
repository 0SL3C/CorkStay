<?php
/**
 * Configuration file for CorkStay application
 * Loads environment variables and provides database connection
 */

// Load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        throw new Exception('.env file not found at: ' . $path);
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue; // Skip comments
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Load .env file
loadEnv(__DIR__ . '/.env');

// Database configuration
function getDbConnection() {
    $host = getenv('DB_HOST') ?: 'localhost';
    $username = getenv('DB_USERNAME') ?: 'root';
    $password = getenv('DB_PASSWORD') ?: '';
    $database = getenv('DB_NAME') ?: 'corkstay';
    
    $conn = mysqli_connect($host, $username, $password, $database);
    
    if ($conn->connect_error) {
        die("Error connecting to database: " . $conn->connect_error);
    }
    
    return $conn;
}

// Session configuration
function getSessionTimeout() {
    return (int)(getenv('SESSION_TIMEOUT') ?: 3600);
}

// Initialize session with timeout handling
function initSession() {
    session_start();
    $timeout = getSessionTimeout();
    
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        session_unset();
        session_destroy();
        header("Location: home.php");
        exit();
    }
    $_SESSION['last_activity'] = time();
}
?>
