<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require(__DIR__ . '/../../../server/db.php');

$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = trim($_POST['full_name']);
    $age = (int)$_POST['age'];
    $current = trim($_POST['current_address']);
    $permanent = trim($_POST['permanent_address']);
    $phone = trim($_POST['phone']);
    $email = trim(strtolower($_POST['email']));
    $password = $_POST['password'];

    // Validation
    if (strlen($full_name) < 2) {
        $errors[] = "Full name must be at least 2 characters long";
    }
    if ($age < 18) {
        $errors[] = "You must be at least 18 years old";
    }
    if (strlen($password) < 4) {
        $errors[] = "Password must be at least 4 characters long";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }

    // Check if email already exists
    if (empty($errors)) {
        $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $result = $check_email->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = "This email is already registered";
        }
        $check_email->close();
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'user';
        $is_active = 1;

        $stmt = $conn->prepare("INSERT INTO users (full_name, age, current_address, permanent_address, phone, email, password, role, is_active) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if ($stmt === false) {
            $errors[] = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("sissssssi", $full_name, $age, $current, $permanent, $phone, $email, $hashed_password, $role, $is_active);

            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                
                header("Location: " . dirname($_SERVER['PHP_SELF']) . "/login.php?registered=1");
                exit();
            } else {
                if ($stmt->errno == 1062) {
                    $errors[] = "This email is already registered";
                } else {
                    $errors[] = "Registration failed: " . $stmt->error;
                }
            }

            $stmt->close();
        }
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

    <?php if (!empty($errors)): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <input type="text" name="full_name" placeholder="Full Name" required 
               value="<?= isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : '' ?>">
        
        <input type="number" name="age" placeholder="Age" required min="18"
               value="<?= isset($_POST['age']) ? htmlspecialchars($_POST['age']) : '' ?>">
        
        <input type="text" name="current_address" placeholder="Current Address" required
               value="<?= isset($_POST['current_address']) ? htmlspecialchars($_POST['current_address']) : '' ?>">
        
        <input type="text" name="permanent_address" placeholder="Permanent Address" required
               value="<?= isset($_POST['permanent_address']) ? htmlspecialchars($_POST['permanent_address']) : '' ?>">
        
        <input type="tel" name="phone" placeholder="Phone Number" required pattern="[0-9]+"
               value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>">
        
        <input type="email" name="email" placeholder="Email" required
               value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
        
        <input type="password" name="password" placeholder="Password" required minlength="4">
        
        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login</a></p>
</div>
</body>
</html>
