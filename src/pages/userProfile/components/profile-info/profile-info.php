<?php
if (!isset($user) || !isset($conn)) {
    require(__DIR__ . '/../../../../../server/db.php');
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
}

// Check if edit mode is enable
$isEditMode = isset($_GET['edit']) && $_GET['edit'] === 'true';

// Error array
$field_errors = [];

// Form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && $isEditMode) {
    // validation
    $full_name = trim($_POST['full_name']);
    if (strlen($full_name) < 2 || strlen($full_name) > 100) {
        $field_errors['full_name'] = "Full name must be between 2 and 100 characters";
    }
    $age = trim($_POST['age']);
    if (!preg_match('/^(1[8-9]|[2-9][0-9]|1[0-1][0-9]|120)$/', $age)) {
        $field_errors['age'] = "Age must be between 18 and 120";
    }
    $phone = trim($_POST['phone']);
    if (!preg_match('/^[0-9]{11}$/', $phone)) {
        $field_errors['phone'] = "Phone number must be exactly 11 digits";
    }
    $email = trim(strtolower($_POST['email']));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $field_errors['email'] = "Please enter a valid email address";
    } else {
        // Check if email exists for other users
        $check_email = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $check_email->bind_param("si", $email, $user_id);
        $check_email->execute();
        if ($check_email->get_result()->num_rows > 0) {
            $field_errors['email'] = "This email is already registered to another user";
        }
        $check_email->close();
    }
    $current_address = trim($_POST['current_address']);
    if (empty($current_address) || strlen($current_address) > 255) {
        $field_errors['current_address'] = "Current address is required and must not exceed 255 characters";
    }
    $permanent_address = trim($_POST['permanent_address']);
    if (empty($permanent_address) || strlen($permanent_address) > 255) {
        $field_errors['permanent_address'] = "Permanent address is required and must not exceed 255 characters";
    }

    // UPDATE
    if (empty($field_errors)) {
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, age = ?, current_address = ?, permanent_address = ?, phone = ?, email = ? WHERE id = ?");
        $stmt->bind_param("sisssssi", $full_name, $age, $current_address, $permanent_address, $phone, $email, $user_id);

        if ($stmt->execute()) {
            $success_message = "Profile updated successfully!";
            $_SESSION['name'] = $full_name;
            $user['full_name'] = $full_name;
            $user['age'] = $age;
            $user['current_address'] = $current_address;
            $user['permanent_address'] = $permanent_address;
            $user['phone'] = $phone;
            $user['email'] = $email;
            header("Location: index.php?tab=personal&edit=true");
            exit();
        } else {
            $error_message = "Error updating profile: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<div class="tab-content">
    <h2 class="section-header">Personal Information</h2>
    <p class="section-subheader">Update your personal details and contact information</p>

    <?php if (!empty($success_message)): ?>
        <div class="success-message"><?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <form method="POST" action="" class="personal-form <?= !$isEditMode ? 'view-mode' : '' ?>">
        <div class="form-row">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" required 
                       value="<?= htmlspecialchars($user['full_name']) ?>"
                       maxlength="100"
                       <?= !$isEditMode ? 'disabled' : '' ?>>
                <?php if (isset($field_errors['full_name'])): ?>
                    <span class="error-hint"><?= htmlspecialchars($field_errors['full_name']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="age">Age</label>
                <input type="text" id="age" name="age" required 
                       value="<?= htmlspecialchars($user['age']) ?>"
                       pattern="^(1[8-9]|[2-9][0-9]|1[0-1][0-9]|120)$"
                       <?= !$isEditMode ? 'disabled' : '' ?>>
                <?php if (isset($field_errors['age'])): ?>
                    <span class="error-hint"><?= htmlspecialchars($field_errors['age']) ?></span>
                <?php endif; ?>
                <span class="input-hint">Must be between 18 and 120 years old</span>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required
                       value="<?= htmlspecialchars($user['email']) ?>"
                       maxlength="100"
                       <?= !$isEditMode ? 'disabled' : '' ?>>
                <?php if (isset($field_errors['email'])): ?>
                    <span class="error-hint"><?= htmlspecialchars($field_errors['email']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" required
                       value="<?= htmlspecialchars($user['phone']) ?>"
                       maxlength="11"
                       placeholder="09123456789"
                       <?= !$isEditMode ? 'disabled' : '' ?>>
                <?php if (isset($field_errors['phone'])): ?>
                    <span class="error-hint"><?= htmlspecialchars($field_errors['phone']) ?></span>
                <?php endif; ?>
                <span class="input-hint">11 digits number (e.g., 09123456789)</span>
            </div>
        </div>

        <div class="form-group">
            <label for="current_address">Current Address</label>
            <input type="text" id="current_address" name="current_address" required
                   value="<?= htmlspecialchars($user['current_address']) ?>"
                   maxlength="255"
                   <?= !$isEditMode ? 'disabled' : '' ?>>
            <?php if (isset($field_errors['current_address'])): ?>
                <span class="error-hint"><?= htmlspecialchars($field_errors['current_address']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="permanent_address">Permanent Address</label>
            <input type="text" id="permanent_address" name="permanent_address" required
                   value="<?= htmlspecialchars($user['permanent_address']) ?>"
                   maxlength="255"
                   <?= !$isEditMode ? 'disabled' : '' ?>>
            <?php if (isset($field_errors['permanent_address'])): ?>
                <span class="error-hint"><?= htmlspecialchars($field_errors['permanent_address']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-actions" <?= !$isEditMode ? 'style="display: none;"' : '' ?>>
            <button type="submit" class="save-btn">Save Changes</button>
            <button type="reset" class="cancel-btn">Reset</button>
        </div>
    </form>
</div>
