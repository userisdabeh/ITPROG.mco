<?php
require(__DIR__ . '/../../../server/db.php');

$errors = [];
$success = false;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = $_POST['full_name'];
    $age = $_POST['age'];
    $current = $_POST['current_address'];
    $permanent = $_POST['permanent_address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (full_name, age, current_address, permanent_address, phone, email, password) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");

    if ($stmt === false) {
        $errors[] = "Database error: " . $conn->error;
    } else {
        $stmt->bind_param("sisssss", $full_name, $age, $current, $permanent, $phone, $email, $password);

        if ($stmt->execute()) {
            $success = true;
        } else {
            $errors[] = "Registration failed: " . $stmt->error;
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Wonderpets</title>
    <link rel="stylesheet" href="login_register.css">
</head>
<body>
<div class="form-container">
    <h2>Register</h2>

    <?php if ($success): ?>
        <p style="color: green;">Registration successful! <a href="login.php">Click here to login.</a></p>
    <?php elseif (!empty($errors)): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="text" name="full_name" placeholder="Full Name" required>
        <input type="number" name="age" placeholder="Age" required>
        <input type="text" name="current_address" placeholder="Current Address" required>
        <input type="text" name="permanent_address" placeholder="Permanent Address" required>
        <input type="text" name="phone" placeholder="Phone Number" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login</a></p>
</div>
</body>
</html>
