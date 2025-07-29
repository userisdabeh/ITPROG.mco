<?php
session_start();
require(__DIR__ . '/../../../server/db.php');

$error = "";
$success_message = "";

// Check if redirected from registration
if (isset($_GET['registered']) && $_GET['registered'] == 1) {
    $success_message = "Registration successful! Please login with your new account.";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT id, full_name, password, role FROM users WHERE email = ? AND is_active = 1");
    
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // password plain text
            if ($user['password'] === $password) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update_stmt->bind_param("si", $hashed_password, $user['id']);
                $update_stmt->execute();
                $update_stmt->close();
                
                // Continue with login
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];

                header("Location: ../home/home.php");
                exit;
            }
            // password hash
            else if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];

                header("Location: ../home/home.php");
                exit;
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No account found with that email.";
        }

        $stmt->close();
    } else {
        $error = "Something went wrong: " . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Wonderpets</title>
    <link rel="stylesheet" href="login_register.css">
</head>
<body>
<div class="form-container">
    <h2>Login</h2>

    <?php if (!empty($success_message)): ?>
        <p style="color: green;"><?= htmlspecialchars($success_message) ?></p>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>
</body>
</html>
