<?php
session_start();
require '../../../server/db.php';
require '../../../server/image.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Initial setup
$currentTab = $_GET["tab"] ?? "personal";
$isEditMode = isset($_GET['edit']) && $_GET['edit'] === 'true';
$user_id = (int) $_SESSION['user_id'];
$success_message = '';
$error_message = '';
$field_errors = [];

// Handle success/error messages from URL parameters
if (isset($_GET['success']) && $_GET['success'] == '1') {
    $success_message = "Profile updated successfully!";
}
if (isset($_GET['error'])) {
    $error_message = htmlspecialchars($_GET['error']);
}



// Fetch current user
$sql = "SELECT full_name, age, current_address, permanent_address, phone, email, profile_image, profile_image_type FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$user = $result ? $result->fetch_assoc() : null;

$GLOBALS['user'] = $user;
$GLOBALS['conn'] = $conn;

function isActive($tab, $currentTab) {
    return $tab === $currentTab ? "active" : "";
}

function getTabUrl($tab, $isEditMode) {
    $url = "?tab=" . $tab;
    if ($isEditMode) {
        $url .= "&edit=true";
    }
    return $url;
}

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] === "POST" && $currentTab === "personal" && $isEditMode && isset($_POST['full_name'])) {
    echo "<h3>✅ POST triggered!</h3>";

    $full_name = $conn->real_escape_string(trim($_POST['full_name']));
    $age = intval(trim($_POST['age']));
    $phone = $conn->real_escape_string(trim($_POST['phone']));
    $email = $conn->real_escape_string(trim(strtolower($_POST['email'])));
    $current_address = $conn->real_escape_string(trim($_POST['current_address']));
    $permanent_address = $conn->real_escape_string(trim($_POST['permanent_address']));
    
    $sql = "UPDATE users SET full_name = '$full_name', age = $age, current_address = '$current_address', permanent_address = '$permanent_address', phone = '$phone', email = '$email' WHERE id = $user_id";

    $result = $conn->query($sql);
    if (!$result) {
        echo "❌ Query failed: " . $conn->error;
        exit;
    }

    if ($conn->affected_rows > 0) {
        $_SESSION['name'] = $full_name;
        header("Location: index.php?tab=personal&edit=true&success=1");
        exit;
    } else if ($conn->affected_rows === 0) {
        header("Location: index.php?tab=personal&edit=true&success=1");
        exit;
    } else{
        header("Location: index.php?tab=personal&edit=true&error=" . urlencode("Update failed"));
        exit;
    }
}

// Handle image upload
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['profile_image']) && !isset($_POST['full_name'])) {
    $image = imageUpload('profile_image');
    
    $image_data = $image['image_data'] ? $conn->real_escape_string($image['image_data']) : null;
    $image_type = $image['image_type'] ? $conn->real_escape_string($image['image_type']) : null;
    $sql = "UPDATE users SET profile_image = '$image_data', profile_image_type = '$image_type' WHERE id = $user_id";

    if ($conn->query($sql)) {
        header("Location: index.php?tab=" . $currentTab . ($isEditMode ? "&edit=true" : "") . "&success=1");
        exit();
    } else {
        header("Location: index.php?tab=" . $currentTab . ($isEditMode ? "&edit=true" : "") . "&error=" . urlencode("Error updating profile image"));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Profile</title>
    <link rel="stylesheet" href="index.css?v=<?= time() ?>" />
    <link rel="stylesheet" href="components/profile-card/profile-card.css?v=<?= time() ?>" />
    <link rel="stylesheet" href="components/profile-info/profile-info.css?v=<?= time() ?>" />
    <link rel="stylesheet" href="components/preferences/preferences.css?v=<?= time() ?>" />
    <link rel="stylesheet" href="components/favorites/favorites.css?v=<?= time() ?>" />
</head>
<body>
    <?php require_once('../../components/userHeader.php'); ?>
    <div class="profile-container">
        <?php require('components/profile-card/profile-card.php'); ?>

        <div class="tabs">
            <a href="<?= getTabUrl('personal', $isEditMode) ?>" class="tab <?= isActive("personal", $currentTab) ?>">Personal Info</a>
            <a href="<?= getTabUrl('preferences', $isEditMode) ?>" class="tab <?= isActive("preferences", $currentTab) ?>">Account Settings</a>
            <a href="<?= getTabUrl('favorites', $isEditMode) ?>" class="tab <?= isActive("favorites", $currentTab) ?>">Favorites</a>
            <a href="<?= getTabUrl('history', $isEditMode) ?>" class="tab <?= isActive("history", $currentTab) ?>">History</a>
        </div>

        <?php 
        switch ($currentTab) {
            case "personal":
                require 'components/profile-info/profile-info.php';
                break;
            case "preferences":
                require 'components/preferences/preferences.php';
                break;
            case "favorites":
                require 'components/favorites/favorites.php';
                break;
            case "history":
                require 'components/history/history.php';
                break;
        }
        ?>
    </div>
</body>
</html>
