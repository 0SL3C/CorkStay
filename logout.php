<?php
session_start();

// clean all sessios
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
