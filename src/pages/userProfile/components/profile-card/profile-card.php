<?php
include "../../../api/image.php";
$isEditMode = isset($_GET['edit']) && $_GET['edit'] === 'true';
$currentTab = $_GET['tab'] ?? 'personal';

// Get user's profile image
$image_src = displayImage($user['profile_image'], $user['profile_image_type']);
?>

<div class="profile-card">
    <div class="profile-img-section">
        <img src="<?= htmlspecialchars($image_src) ?>" alt="Profile" class="profile-img" id="profileImg">
        <?php if ($isEditMode): ?>
            <form method="POST" enctype="multipart/form-data" class="image-upload-form">
                <label class="img-edit-btn" title="Change Photo">
                    📷
                    <input type="file" name="profile_image" accept="image/jpeg,image/png,image/gif" onchange="this.form.submit()" style="display: none;">
                </label>
            </form>
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <div class="profile-main">
        <div class="profile-header">
            <div>
                <h1 id="profileName"><?= htmlspecialchars($user['full_name']) ?></h1>
                <div class="profile-address">
                    <span class="icon">📍</span> 
                    <span id="profileAddress"><?= htmlspecialchars($user['current_address']) ?></span>
                </div>
                <div class="profile-age">
                    <span class="icon">🎂</span> 
                    <span id="profileAge"><?= htmlspecialchars($user['age']) ?> years old</span>
                </div>
            </div>
            <?php if ($isEditMode): ?>
                <button class="edit-btn" onclick="window.location.href='index.php?tab=personal'">
                    <svg width="24px" height="24px" stroke-width="1.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" color="#000000"><path d="M6.75827 17.2426L12.0009 12M17.2435 6.75736L12.0009 12M12.0009 12L6.75827 6.75736M12.0009 12L17.2435 17.2426" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                    <span>Cancel Edit</span>
                </button>
            <?php else: ?>
                <button class="edit-btn" onclick="window.location.href='index.php?tab=personal&edit=true'">
                <svg width="20px" height="20px" viewBox="0 0 24 24" stroke-width="1.5" fill="none" xmlns="http://www.w3.org/2000/svg" color="#000000"><path d="M14.3632 5.65156L15.8431 4.17157C16.6242 3.39052 17.8905 3.39052 18.6716 4.17157L20.0858 5.58579C20.8668 6.36683 20.8668 7.63316 20.0858 8.41421L18.6058 9.8942M14.3632 5.65156L4.74749 15.2672C4.41542 15.5993 4.21079 16.0376 4.16947 16.5054L3.92738 19.2459C3.87261 19.8659 4.39148 20.3848 5.0115 20.33L7.75191 20.0879C8.21972 20.0466 8.65806 19.8419 8.99013 19.5099L18.6058 9.8942M14.3632 5.65156L18.6058 9.8942" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>                    <span>Edit Profile</span>
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>