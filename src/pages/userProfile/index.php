<?php
session_start();
include(__DIR__ . '/../../../server/db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../RegistrationAndLogin/login.php");
    exit();
}

// Set the current tab (default is 'personal')
$currentTab = $_GET["tab"] ?? "personal";
$isEditMode = isset($_GET['edit']) && $_GET['edit'] === 'true';

// Function to determine active tab
function isActive($tab, $currentTab) {
    return $tab === $currentTab ? "active" : "";
}

// Function to generate tab URL
function getTabUrl($tab, $isEditMode) {
    $url = "?tab=" . $tab;
    if ($isEditMode) {
        $url .= "&edit=true";
    }
    return $url;
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Fetch current user data
$stmt = $conn->prepare("SELECT full_name, age, current_address, permanent_address, phone, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Profile</title>

    <!-- Global Styles -->
    <link rel="stylesheet" href="index.css?v=<?= time() ?>" />

    <!-- Component CSS -->
    <link rel="stylesheet" href="components/navbar/navbar.css?v=<?= time() ?>" />
    <link rel="stylesheet" href="components/profile-card/profile-card.css?v=<?= time() ?>" />
    <link rel="stylesheet" href="components/profile-info/profile-info.css?v=<?= time() ?>" />
    <link rel="stylesheet" href="components/preferences/preferences.css?v=<?= time() ?>" />
    <link rel="stylesheet" href="components/favorites/favorites.css?v=<?= time() ?>" />
</head>
<body>
    <?php include "components/navbar/navbar.php"; ?>

    <div class="profile-container">
        <?php include "components/profile-card/profile-card.php"; ?>

        <?php if (!empty($success_message)): ?>
            <div class="success-message"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <!-- Tab Navigation -->
        <div class="tabs">
            <a href="<?= getTabUrl('personal', $isEditMode) ?>" 
               class="tab <?= isActive("personal", $currentTab) ?>">Personal Info</a>
            <a href="<?= getTabUrl('preferences', $isEditMode) ?>" 
               class="tab <?= isActive("preferences", $currentTab) ?>">Account Settings</a>
            <a href="<?= getTabUrl('favorites', $isEditMode) ?>" 
               class="tab <?= isActive("favorites", $currentTab) ?>">Favorites</a>
            <a href="<?= getTabUrl('history', $isEditMode) ?>" 
               class="tab <?= isActive("history", $currentTab) ?>">History</a>
        </div>

        <!-- Tab Content -->
        <?php 
        switch ($currentTab) {
            case "personal":
                include "components/profile-info/profile-info.php";
                break;
            case "preferences":
                include "components/preferences/preferences.php";
                break;
            case "favorites":
                include "components/favorites/favorites.php";
                break;
            case "history":
                include "components/history/history.php";
                break;
        }
        ?>
    </div>
</body>
</html>
