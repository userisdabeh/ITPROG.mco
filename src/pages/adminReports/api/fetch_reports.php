<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => true, 'message' => 'Unauthorized']);
    exit;
}

require_once '../../../../server/db.php';

$sql = "SELECT 
    COUNT(CASE WHEN status = 'submitted' THEN 1 END) AS total_submitted,
    COUNT(CASE WHEN status = 'under_review' THEN 1 END) AS total_for_review,
    COUNT(CASE WHEN status = 'interview_required' THEN 1 END) AS total_for_interview,
    COUNT(CASE WHEN status = 'approved' THEN 1 END) AS total_approved,
    COUNT(CASE WHEN status = 'denied' THEN 1 END) AS total_denied,
    COUNT(CASE WHEN status = 'completed' THEN 1 END) AS total_completed,
    COUNT(CASE WHEN status = 'withdrawn' THEN 1 END) AS total_withdrawn,
    COUNT(*) AS total_applications
    FROM adoption_applications";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['error' => true, 'message' => 'Query failed']);
    exit;
}

$data = $result->fetch_assoc();

echo json_encode([
    'error' => false,
    'data' => $data
]);
?>
