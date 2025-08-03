<?php
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
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" id="full_name" name="full_name" class="input input-md" value="<?= htmlspecialchars($user['full_name']) ?>" maxlength="100" <?= !$isEditMode ? 'disabled' : '' ?> required>
                <?php if (isset($field_errors['full_name'])): ?>
                    <span class="error-hint"><?= htmlspecialchars($field_errors['full_name']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="age" class="form-label">Age</label>
                <input type="text" id="age" name="age" class="input input-md" value="<?= htmlspecialchars($user['age']) ?>" maxlength="3" <?= !$isEditMode ? 'disabled' : '' ?> required>
                <?php if (isset($field_errors['age'])): ?>
                    <span class="form-error"><?= htmlspecialchars($field_errors['age']) ?></span>
                <?php endif; ?>
                <span class="form-help">Between 18 and 120</span>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="input input-md" value="<?= htmlspecialchars($user['email']) ?>" maxlength="100" <?= !$isEditMode ? 'disabled' : '' ?> required>
                <?php if (isset($field_errors['email'])): ?>
                    <span class="form-error"><?= htmlspecialchars($field_errors['email']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" id="phone" name="phone" class="input input-md" value="<?= htmlspecialchars($user['phone']) ?>" maxlength="11" placeholder="09123456789" <?= !$isEditMode ? 'disabled' : '' ?> required>
                <?php if (isset($field_errors['phone'])): ?>
                    <span class="form-error"><?= htmlspecialchars($field_errors['phone']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="current_address" class="form-label">Current Address</label>
            <input type="text" id="current_address" name="current_address" class="input input-md" value="<?= htmlspecialchars($user['current_address']) ?>" maxlength="255" <?= !$isEditMode ? 'disabled' : '' ?> required>
            <?php if (isset($field_errors['current_address'])): ?>
                <span class="form-error"><?= htmlspecialchars($field_errors['current_address']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="permanent_address" class="form-label">Permanent Address</label>
            <input type="text" id="permanent_address" name="permanent_address" class="input input-md" value="<?= htmlspecialchars($user['permanent_address']) ?>" maxlength="255" <?= !$isEditMode ? 'disabled' : '' ?> required>
            <?php if (isset($field_errors['permanent_address'])): ?>
                <span class="form-error"><?= htmlspecialchars($field_errors['permanent_address']) ?></span>
            <?php endif; ?>
        </div>

        <?php if ($isEditMode): ?>
        <div class="form-actions">
            <button type="submit" class="btn btn-md btn-primary">Save Changes</button>
            <button type="reset" class="btn btn-md btn-secondary">Reset</button>
        </div>
        <?php endif; ?>
    </form>
</div>
