<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => true, 'message' => 'Unauthorized']);
    exit;
}

require_once '../../../../server/db.php';

$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$role = $_GET['role'] ?? '';
$minAdoptions = $_GET['min-adoptions'] ?? '';
$maxAdoptions = $_GET['max-adoptions'] ?? '';
$joinDate = $_GET['join-date'] ?? '';

$sql = "SELECT u.* FROM users u LEFT JOIN (
            SELECT user_id, COUNT(*) as adoption_count FROM adoptions GROUP BY user_id
        ) a ON u.id = a.user_id WHERE 1";

$params = [];
$types = "";

// Search
if (!empty($search)) {
    $sql .= " AND (u.full_name LIKE ? OR u.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "ss";
}

// Status
if ($status !== '') {
    $sql .= " AND u.is_active = ?";
    $params[] = $status;
    $types .= "i";
}

// Role
if (!empty($role)) {
    $sql .= " AND u.role = ?";
    $params[] = $role;
    $types .= "s";
}

// Adoptions
if ($minAdoptions !== '') {
    $sql .= " AND IFNULL(a.adoption_count, 0) >= ?";
    $params[] = $minAdoptions;
    $types .= "i";
}
if ($maxAdoptions !== '') {
    $sql .= " AND IFNULL(a.adoption_count, 0) <= ?";
    $params[] = $maxAdoptions;
    $types .= "i";
}

// Join date
if (!empty($joinDate)) {
    $sql .= " AND DATE_FORMAT(u.created_at, '%Y-%m') = ?";
    $params[] = $joinDate;
    $types .= "s";
}

$stmt = $conn->prepare($sql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode(['error' => false, 'users' => $users]);
