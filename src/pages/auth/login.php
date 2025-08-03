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
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    
    $sql = "SELECT id, full_name, password, role FROM users WHERE email = '$email' AND is_active = 1";
    $result = $conn->query($sql);
    
    if ($result) {
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // password plain text
            if ($user['password'] === $password) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $escaped_password = $conn->real_escape_string($hashed_password);
                $user_id = (int)$user['id'];
                $update_sql = "UPDATE users SET password = '$escaped_password' WHERE id = $user_id";
                $conn->query($update_sql);
                
                // Continue with login
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];

                if($user['role'] == 'admin') {
                    header("Location: ../adminDashboard/index.php");
                } else {
                    header("Location: ../home/index.php");
                }

                exit;
            }
            // password hash
            else if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];

                if($user['role'] == 'admin') {
                    header("Location: ../adminDashboard/index.php");
                } else {
                    header("Location: ../home/index.php");
                }

                exit;
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No account found with that email.";
        }
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
    <link rel="stylesheet" href="login-register.css?v=<?= time() ?>">
</head>
<body>
<div class="header">
    <div class="brand-section">
        <span class="brand-icon"><?xml version="1.0" encoding="UTF-8"?><svg viewBox="0 0 24 24" stroke-width="1.5" fill="none" xmlns="http://www.w3.org/2000/svg" color="#000000"><path fill-rule="evenodd" clip-rule="evenodd" d="M19.9463 1.59905C19.7411 1.27486 19.3244 1.15775 18.9804 1.32762C18.346 1.64091 17.3056 2.37972 16.4755 2.99465C16.0485 3.31089 15.6577 3.60914 15.3739 3.82819L15.3143 3.87417C15.128 3.77341 14.8928 3.65452 14.6218 3.53557C13.9471 3.23941 12.9931 2.91677 12 2.91677C11.0069 2.91677 10.0529 3.23941 9.37823 3.53557C9.1077 3.65432 8.87275 3.773 8.68662 3.87366L8.63018 3.82977C8.3491 3.61133 7.9635 3.31397 7.54606 2.99878C6.73113 2.38346 5.73398 1.65332 5.1806 1.34495C4.8381 1.15409 4.40638 1.25926 4.19003 1.58626C2.61279 3.97014 2.55039 5.87414 3.00077 7.23562C3.21973 7.8975 3.54828 8.39461 3.82405 8.72806C3.84973 8.75912 3.875 8.78881 3.89975 8.81715C3.11813 10.4241 2.25 12.7782 2.25 15.3334C2.25 15.7476 2.58579 16.0834 3 16.0834C4.25484 16.0834 5.25805 16.4548 5.94897 16.827C6.29433 17.0131 6.55888 17.1979 6.7337 17.3331C6.77007 17.3612 6.80247 17.3871 6.83081 17.4103L6.83144 17.4126C6.87212 17.5615 6.93347 17.771 7.01789 18.0212C7.18586 18.5189 7.44942 19.19 7.83068 19.8678C8.57056 21.1831 9.89029 22.7501 12 22.7501C14.1097 22.7501 15.4294 21.1831 16.1693 19.8678C16.5506 19.19 16.8141 18.5189 16.9821 18.0212C17.0665 17.771 17.1279 17.5615 17.1686 17.4126L17.1692 17.4103C17.1975 17.3871 17.2299 17.3612 17.2663 17.3331C17.4411 17.1979 17.7057 17.0131 18.051 16.827C18.7419 16.4548 19.7452 16.0834 21 16.0834C21.4142 16.0834 21.75 15.7476 21.75 15.3334C21.75 12.7788 20.8823 10.4252 20.1008 8.81827C20.1254 8.79035 20.1504 8.76112 20.1759 8.73056C20.4529 8.39853 20.7847 7.90372 21.0121 7.24488C21.4796 5.89083 21.458 3.98802 19.9463 1.59905ZM11 17.25C10.5858 17.25 10.25 17.5858 10.25 18C10.25 18.4142 10.5858 18.75 11 18.75H11.25V19C11.25 19.4142 11.5858 19.75 12 19.75C12.4142 19.75 12.75 19.4142 12.75 19V18.75H13C13.4142 18.75 13.75 18.4142 13.75 18C13.75 17.5858 13.4142 17.25 13 17.25H11ZM7.96967 11.9697C8.26256 11.6768 8.73744 11.6768 9.03033 11.9697L10.5303 13.4697C10.8232 13.7626 10.8232 14.2374 10.5303 14.5303C10.2374 14.8232 9.76256 14.8232 9.46967 14.5303L7.96967 13.0303C7.67678 12.7374 7.67678 12.2626 7.96967 11.9697ZM16.0303 13.0303C16.3232 12.7374 16.3232 12.2626 16.0303 11.9697C15.7374 11.6768 15.2626 11.6768 14.9697 11.9697L13.4697 13.4697C13.1768 13.7626 13.1768 14.2374 13.4697 14.5303C13.7626 14.8232 14.2374 14.8232 14.5303 14.5303L16.0303 13.0303Z" fill="#f97316"></path></svg></span>
        <span class="brand-name">wonderpets</span>
    </div>
    <a href="../landingPage/index.php" class="back-link btn btn-xs btn-secondary btn-outline">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6.85355 3.14645C7.04882 3.34171 7.04882 3.65829 6.85355 3.85355L3.70711 7H12.5C12.7761 7 13 7.22386 13 7.5C13 7.77614 12.7761 8 12.5 8H3.70711L6.85355 11.1464C7.04882 11.3417 7.04882 11.6583 6.85355 11.8536C6.65829 12.0488 6.34171 12.0488 6.14645 11.8536L2.14645 7.85355C1.95118 7.65829 1.95118 7.34171 2.14645 7.14645L6.14645 3.14645C6.34171 2.95118 6.65829 2.95118 6.85355 3.14645Z" fill="currentColor"/>
        </svg>
        Back to Home
    </a>
</div>

<div class="form-container">
    <h2>Login</h2>
    <p class="form-subtitle"> Enter your credentials to access your account </p>

    <?php if (!empty($success_message)): ?>
        <p style="font-size: 0.85rem; color: green; background-color: #d7f8daff; padding: 10px; border-radius: 5px; list-style: none; margin-bottom: 20px;"><?= htmlspecialchars($success_message) ?></p>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <p class="error" style="font-size: 0.85rem; color: red; background-color: #f8d7da; padding: 10px; border-radius: 5px; list-style: none; margin-bottom: 20px;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="" class="form-row">
        <input type="email" name="email" class="input input-md" placeholder="Email" required>
        <input type="password" name="password" class="input input-md" placeholder="Password" required>
        <button type="submit" class="btn btn-md btn-primary">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>
</body>
</html>
