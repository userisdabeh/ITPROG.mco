<?php
session_start();
require '../../server/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$pet_id = isset($_POST['pet_id']) ? (int)$_POST['pet_id'] : null;
$redirect_url = $_POST['redirect_url'] ?? '../home/index.php';

if (!$pet_id) {
    header('Location: ' . $redirect_url);
    exit;
}

// Check if pet exists
$pet_check_sql = "SELECT id FROM pets WHERE id = $pet_id";
$pet_check_result = $conn->query($pet_check_sql);
if (!$pet_check_result || !$pet_check_result->fetch_assoc()) {
    header('Location: ' . $redirect_url);
    exit;
}

// Check if already favorited
$check_favorite_sql = "SELECT id FROM user_favorites WHERE user_id = $user_id AND pet_id = $pet_id";
$check_result = $conn->query($check_favorite_sql);
$existing = $check_result ? $check_result->fetch_assoc() : null;

if ($existing) {
    // Remove from favorites
    $remove_sql = "DELETE FROM user_favorites WHERE user_id = $user_id AND pet_id = $pet_id";
    $conn->query($remove_sql);
} else {
    // Add to favorites
    $add_sql = "INSERT INTO user_favorites (user_id, pet_id) VALUES ($user_id, $pet_id)";
    $conn->query($add_sql);
}

$conn->close();
header('Location: ' . $redirect_url);
exit;
?>
