<?php
// Get edit mode from query parameter
$isEditMode = isset($_GET['edit']) && $_GET['edit'] === 'true';

// Initialize messages
$success_message = '';
$error_message = '';

// Handle password update
if ($_SERVER["REQUEST_METHOD"] === "POST" && $isEditMode) {
    require(__DIR__ . '/../../../../../server/db.php');
    
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate passwords
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_message = "All password fields are required";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "New passwords do not match";
    } elseif (strlen($new_password) < 4) {
        $error_message = "New password must be at least 4 characters long";
    } else {
        // Verify current password
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (password_verify($current_password, $user['password'])) {
            // Update password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed_password, $_SESSION['user_id']);
            
            if ($stmt->execute()) {
                $success_message = "Password updated successfully!";
            } else {
                $error_message = "Error updating password: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_message = "Current password is incorrect";
        }
    }
    $conn->close();
}
?>

<div class="tab-content">
    <h2 class="section-header">Account Settings</h2>
    <p class="section-subheader">Update your account password</p>

    <?php if (!empty($success_message)): ?>
        <div class="success-message"><?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <form method="POST" action="" class="preferences-form <?= !$isEditMode ? 'view-mode' : '' ?>">
        <div class="form-group">
            <label for="current_password">Current Password</label>
            <input type="password" id="current_password" name="current_password" 
                   placeholder="Enter your current password"
                   <?= !$isEditMode ? 'disabled' : '' ?>>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" 
                       placeholder="Enter new password"
                       minlength="4"
                       <?= !$isEditMode ? 'disabled' : '' ?>>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" 
                       placeholder="Confirm new password"
                       minlength="4"
                       <?= !$isEditMode ? 'disabled' : '' ?>>
            </div>
        </div>

        <div class="form-actions" <?= !$isEditMode ? 'style="display: none;"' : '' ?>>
            <button type="submit" class="save-btn">Update Password</button>
            <button type="reset" class="cancel-btn">Reset</button>
        </div>
    </form>
</div>
