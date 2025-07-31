<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require(__DIR__ . '/../../../../../server/db.php');

// Always get user_id from session
$user_id = $_SESSION['user_id'] ?? null;



// Fetch user if not already set
if (!isset($user)) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}

// Check if in edit mode
$isEditMode = isset($_GET['edit']) && $_GET['edit'] === 'true';
?>

<!-- HTML Form -->
<div class="tab-content">
    <h2 class="section-header">Personal Information</h2>
    <p class="section-subheader">Update your personal details and contact information</p>

    <?php if (!empty($success_message)): ?>
        <div class="success-message"><?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <form method="POST" action="?tab=personal&edit=true" class="personal-form <?= !$isEditMode ? 'view-mode' : '' ?>">
        <div class="form-row">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" maxlength="100" <?= !$isEditMode ? 'disabled' : '' ?> required>
                <?php if (isset($field_errors['full_name'])): ?>
                    <span class="error-hint"><?= htmlspecialchars($field_errors['full_name']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="age">Age</label>
                <input type="text" id="age" name="age" value="<?= htmlspecialchars($user['age']) ?>" maxlength="3" <?= !$isEditMode ? 'disabled' : '' ?> required>
                <?php if (isset($field_errors['age'])): ?>
                    <span class="error-hint"><?= htmlspecialchars($field_errors['age']) ?></span>
                <?php endif; ?>
                <span class="input-hint">Between 18 and 120</span>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" maxlength="100" <?= !$isEditMode ? 'disabled' : '' ?> required>
                <?php if (isset($field_errors['email'])): ?>
                    <span class="error-hint"><?= htmlspecialchars($field_errors['email']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" maxlength="11" placeholder="09123456789" <?= !$isEditMode ? 'disabled' : '' ?> required>
                <?php if (isset($field_errors['phone'])): ?>
                    <span class="error-hint"><?= htmlspecialchars($field_errors['phone']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="current_address">Current Address</label>
            <input type="text" id="current_address" name="current_address" value="<?= htmlspecialchars($user['current_address']) ?>" maxlength="255" <?= !$isEditMode ? 'disabled' : '' ?> required>
            <?php if (isset($field_errors['current_address'])): ?>
                <span class="error-hint"><?= htmlspecialchars($field_errors['current_address']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="permanent_address">Permanent Address</label>
            <input type="text" id="permanent_address" name="permanent_address" value="<?= htmlspecialchars($user['permanent_address']) ?>" maxlength="255" <?= !$isEditMode ? 'disabled' : '' ?> required>
            <?php if (isset($field_errors['permanent_address'])): ?>
                <span class="error-hint"><?= htmlspecialchars($field_errors['permanent_address']) ?></span>
            <?php endif; ?>
        </div>

        <?php if ($isEditMode): ?>
        <div class="form-actions">
            <button type="submit" class="save-btn">Save Changes</button>
            <button type="reset" class="cancel-btn">Reset</button>
        </div>
        <?php endif; ?>
    </form>
</div>
