<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../../server/db.php';

$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $full_name = $first_name . ' ' . $last_name;
    $age = (int)$_POST['age'];
    $current = trim($_POST['current_address']);
    $permanent = trim($_POST['permanent_address']);
    $phone = trim($_POST['phone']);
    $email = trim(strtolower($_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (strlen($first_name) < 1) {
        $errors[] = "First name is required";
    }
    if (strlen($last_name) < 1) {
        $errors[] = "Last name is required";
    }
    if ($age < 18) {
        $errors[] = "You must be at least 18 years old";
    }
    if (strlen($password) < 4) {
        $errors[] = "Password must be at least 4 characters long";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }

    // Check if email already exists
    if (empty($errors)) {
        $escaped_email = $conn->real_escape_string($email);
        $check_sql = "SELECT id FROM users WHERE email = '$escaped_email'";
        $result = $conn->query($check_sql);
        
        if ($result && $result->num_rows > 0) {
            $errors[] = "This email is already registered";
        }
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'user';
        $is_active = 1;

        // Escape all string inputs
        $escaped_full_name = $conn->real_escape_string($full_name);
        $escaped_current = $conn->real_escape_string($current);
        $escaped_permanent = $conn->real_escape_string($permanent);
        $escaped_phone = $conn->real_escape_string($phone);
        $escaped_email = $conn->real_escape_string($email);
        $escaped_password = $conn->real_escape_string($hashed_password);
        $escaped_role = $conn->real_escape_string($role);

        $insert_sql = "INSERT INTO users (full_name, age, current_address, permanent_address, phone, email, password, role, is_active) 
                       VALUES ('$escaped_full_name', $age, '$escaped_current', '$escaped_permanent', '$escaped_phone', '$escaped_email', '$escaped_password', '$escaped_role', $is_active)";

        if ($conn->query($insert_sql)) {
            $conn->close();
            
            header("Location: " . dirname($_SERVER['PHP_SELF']) . "/login.php?registered=1");
            exit();
        } else {
            if ($conn->errno == 1062) {
                $errors[] = "This email is already registered";
            } else {
                $errors[] = "Registration failed: " . $conn->error;
            }
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Wonderpets</title>
    <link rel="stylesheet" href="login-register.css?v=<?php echo time(); ?>">
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
        <h2>Sign Up</h2>
        <p class="form-subtitle">Fill in your details to create your account</p>

        <?php if (!empty($errors)): ?>
            <ul style="font-size: 0.85rem; color: red; background-color: #f8d7da; padding: 10px; border-radius: 5px; list-style: none; margin-bottom: 20px;">
                <?php foreach ($errors as $e): ?>
                    <li><svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 16 16"><path fill="currentColor" fill-rule="evenodd" d="M8 14.5a6.5 6.5 0 1 0 0-13a6.5 6.5 0 0 0 0 13ZM8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16Zm1-5a1 1 0 1 1-2 0a1 1 0 0 1 2 0Zm-.25-6.25a.75.75 0 0 0-1.5 0v3.5a.75.75 0 0 0 1.5 0v-3.5Z" clip-rule="evenodd"/></svg> &nbsp; <?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name" class="form-label required">First Name</label>
                    <input type="text" id="first_name" name="first_name" class="input input-md" placeholder="John" required 
                        value="<?= isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="last_name" class="form-label required">Last Name</label>
                    <input type="text" id="last_name" name="last_name" class="input input-md" placeholder="Doe" required 
                        value="<?= isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : '' ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group small-width">
                    <label for="age" class="form-label required">Age</label>
                    <input type="number" id="age" name="age" class="input input-md" placeholder="18" required min="18"
                        value="<?= isset($_POST['age']) ? htmlspecialchars($_POST['age']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label required">Email</label>
                    <input type="email" id="email" name="email" class="input input-md" placeholder="john@example.com" required
                        value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group small-width">
                    <label for="phone" class="form-label required">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="input input-md" placeholder="+1 (555) 123-4567" required pattern="[0-9+\-\s\(\)]+"
                        value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="current_address" class="form-label required">Current Address</label>
                    <input type="text" id="current_address" name="current_address" class="input input-md" placeholder="Current Address" required
                        value="<?= isset($_POST['current_address']) ? htmlspecialchars($_POST['current_address']) : '' ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="permanent_address" class="form-label required">Permanent Address</label>
                <input type="text" id="permanent_address" name="permanent_address" class="input input-md" placeholder="Permanent Address" required
                    value="<?= isset($_POST['permanent_address']) ? htmlspecialchars($_POST['permanent_address']) : '' ?>">
            </div>

            <div class="form-group">
                <label for="password" class="form-label required">Password</label>
                <input type="password" id="password" name="password" class="input input-md" placeholder="••••••••" required minlength="4">
            </div>

            <div class="form-group">
                <label for="confirm_password" class="form-label required">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="input input-md" placeholder="••••••••" required minlength="4">
            </div>
            
            <button type="submit" class="btn btn-md btn-primary">Create Account</button>
        </form>

        <p class="form-footer">Already have an account? <a href="login.php" class="signin-link">Sign in</a></p>
    </div>
</body>
</html>
