<?php
session_start();
include(__DIR__ . '/../../../server/db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../RegistrationAndLogin/login.php");
    exit();
}

// Initial setup
$currentTab = $_GET["tab"] ?? "personal";
$isEditMode = isset($_GET['edit']) && $_GET['edit'] === 'true';
$user_id = (int) $_SESSION['user_id'];
$success_message = '';
$error_message = '';
$field_errors = [];

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] === "POST" && $currentTab === "personal" && $isEditMode) {
    echo "<h3>✅ POST triggered!</h3>";

    // Sanitize
    $full_name = trim($_POST['full_name']);
    $age = intval(trim($_POST['age'])); // Ensure integer
    $phone = trim($_POST['phone']);
    $email = trim(strtolower($_POST['email']));
    $current_address = trim($_POST['current_address']);
    $permanent_address = trim($_POST['permanent_address']);

    // Debug print
    echo "<pre>";
    echo "full_name: $full_name\n";
    echo "age: $age\n";
    echo "phone: $phone\n";
    echo "email: $email\n";
    echo "current_address: $current_address\n";
    echo "permanent_address: $permanent_address\n";
    echo "</pre>";

    // Validate (simplified)
    if (empty($full_name) || empty($email)) {
        echo "<strong>Missing required fields</strong>";
        exit;
    }

    // SQL
    echo "<p>🔧 Preparing SQL update...</p>";
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, age = ?, current_address = ?, permanent_address = ?, phone = ?, email = ? WHERE id = ?");
    if (!$stmt) {
        echo "❌ Prepare failed: " . $conn->error;
        exit;
    }

    $stmt->bind_param("sissssi", $full_name, $age, $current_address, $permanent_address, $phone, $email, $user_id);
    
    if ($stmt->execute()) {
        echo "<h3>✅ Update successful!</h3>";
        $_SESSION['name'] = $full_name;
        header("Location: index.php?tab=personal&edit=true&success=1");
        exit;
    } else {
        echo "<strong>❌ Update failed:</strong> " . $stmt->error;
        exit;
    }
}



// Fetch current user
$stmt = $conn->prepare("SELECT full_name, age, current_address, permanent_address, phone, email, profile_image, profile_image_type FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Profile</title>
    <link rel="stylesheet" href="index.css?v=<?= time() ?>" />
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

        <div class="tabs">
            <a href="<?= getTabUrl('personal', $isEditMode) ?>" class="tab <?= isActive("personal", $currentTab) ?>">Personal Info</a>
            <a href="<?= getTabUrl('preferences', $isEditMode) ?>" class="tab <?= isActive("preferences", $currentTab) ?>">Account Settings</a>
            <a href="<?= getTabUrl('favorites', $isEditMode) ?>" class="tab <?= isActive("favorites", $currentTab) ?>">Favorites</a>
            <a href="<?= getTabUrl('history', $isEditMode) ?>" class="tab <?= isActive("history", $currentTab) ?>">History</a>
        </div>

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
