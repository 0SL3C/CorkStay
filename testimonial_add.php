<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tenant') {
    header("Location: home.php");
    exit();
}

$conn = mysqli_connect('localhost', 'root', '159753', 'corkstay');
if ($conn->connect_error) {
    die("Error connecting to database: " . $conn->connect_error);
}

// Fetch user first name
$userName = "Unknown User";
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT first_name FROM users WHERE user_id = $user_id");
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $userName = $user['first_name'];
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_testimonial'])) {
      $content = htmlspecialchars(trim($_POST['content']));
      $service_name = htmlspecialchars(trim($_POST['service_name']));

    if (empty($service_name)) {
        $errors[] = "Service name cannot be empty.";
    }


    if (empty($content)) {
        $errors[] = "Content cannot be empty.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO testimonials (tenant_id, service_name, comment, testimonial_date) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $_SESSION['user_id'], $service_name, $content);
        if ($stmt->execute()) {
            header("Location: testimonials.php");
            exit();
        } else {
            $errors[] = "Failed to add testimonial.";
        }
        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CorkStay - Add Testimonial</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: url('./images/1126773.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background: inherit;
            filter: blur(8px);
            z-index: -1;
        }

        .container {
            max-width: 600px;
            margin: 80px auto;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        form label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }

        form textarea,
        form input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 8px;
            resize: none;
        }

        form input[type="submit"] {
            display: block;
            width: 100%;
            margin-top: 20px;
            padding: 12px;
            background-color: #007BFF;
            color: white;
            font-size: 1em;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        form input[type="submit"]:hover {
            background-color: #0056b3;
        }

        ul {
            padding-left: 20px;
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add Testimonial</h2>
<p>You are logged in as <strong><?php echo htmlspecialchars($userName); ?></strong></p>

        <?php if (!empty($errors)): ?>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="POST" action="testimonial_add.php">
            <label for="service_name">Service Name:</label>
            <input type="text" id="service_name" name="service_name" required>
            <label for="content">Testimonial:</label>
            <textarea id="content" name="content" rows="5" required></textarea>
            <input type="submit" name="add_testimonial" value="Submit Testimonial">
        </form>
    </div>
</body>
</html>

