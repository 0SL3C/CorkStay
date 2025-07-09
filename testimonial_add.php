<<<<<<< HEAD
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

=======
<?php
require_once 'config.php';

// Initialize session with timeout handling
initSession();

// Get database connection
$conn = getDbConnection();

// Acesso restrito a 'tenant'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tenant') {
    header("Location: home.php");
    exit();
}

$errors = [];
$success = false;
$user_id = $_SESSION['user_id'];

// Pega nome do usuário para exibir na tela
$result = $conn->query("SELECT first_name FROM users WHERE user_id = $user_id");
$user = $result->fetch_assoc();
$first_name = $user['first_name'] ?? '';

// Submissão do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_testimonial'])) {
    $service_name = htmlspecialchars(trim($_POST['service_name']));
    $content = htmlspecialchars(trim($_POST['content']));
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : null;
    $testimonial_date = date('Y-m-d');

    // Validações
    if (empty($service_name)) {
        $errors[] = "Service name is required.";
    }
    if (empty($content)) {
        $errors[] = "Testimonial content is required.";
    }
    if ($rating === null || $rating < 1 || $rating > 5) {
        $errors[] = "Rating must be between 1 and 5.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO testimonials (tenant_id, service_name, content, rating, testimonial_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issis", $user_id, $service_name, $content, $rating, $testimonial_date);

        if ($stmt->execute()) {
            $success = true;
        } else {
            $errors[] = "Failed to submit testimonial.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Testimonial - CorkStay</title>
</head>
<body>
    <h2>Add Testimonial</h2>
    <p>Logged in as <strong><?= htmlspecialchars($first_name) ?></strong></p>

    <?php if ($success): ?>
        <p style="color: green;">Testimonial submitted successfully!</p>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="service_name">Service Name:</label><br>
        <input type="text" name="service_name" id="service_name" required><br><br>

        <label for="content">Testimonial:</label><br>
        <textarea name="content" id="content" required></textarea><br><br>

        <label for="rating">Rating (1 to 5):</label><br>
        <select name="rating" id="rating" required>
            <option value="">--Select--</option>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
        </select><br><br>

        <input type="submit" name="submit_testimonial" value="Submit Testimonial">
    </form>
</body>
</html>
>>>>>>> origin/Michel
