<?php
require(__DIR__ . '/../../../../../server/image.php');

if (!isset($user) || !isset($conn)) {
    require(__DIR__ . '/../../../../../server/db.php');
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}

$isEditMode = isset($_GET['edit']) && $_GET['edit'] === 'true';
$currentTab = $_GET['tab'] ?? 'personal';

// Handle image upload
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['profile_image'])) {
    $file = $_FILES['profile_image'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $image_data = file_get_contents($file['tmp_name']);
        $image_type = $file['type'];
        
        if ($image_data && $image_type) {
            $stmt = $conn->prepare("UPDATE users SET profile_image = ?, profile_image_type = ? WHERE id = ?");
            $stmt->bind_param("ssi", $image_data, $image_type, $user_id);
            
            if ($stmt->execute()) {
                $stmt->close();
                header("Location: index.php");
                exit();
            } else {
                $error_message = "Error updating profile image: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_message = "Failed to process image data";
        }
    } else {
        $error_message = "Error uploading file: " . $file['error'];
    }
}

// Get user's profile image
$image_src = $user['profile_image'] ? "data:" . $user['profile_image_type'] . ";base64," . base64_encode($user['profile_image']) : "https://placehold.co/120x120";
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
                    ❌ <span>Cancel Edit</span>
                </button>
            <?php else: ?>
                <button class="edit-btn" onclick="window.location.href='index.php?tab=personal&edit=true'">
                    ✏️ <span>Edit Profile</span>
                </button>
            <?php endif; ?>
        </div>
        <p class="profile-bio" id="profileBio">
            Pet adoption enthusiast looking to provide a loving home for a furry friend.
        </p>
    </div>
</div>