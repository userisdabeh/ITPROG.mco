<?php
    session_start();

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized']);
        exit();
    }

    if (!isset($_GET['applicationID'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Application ID is required']);
        exit();
    }

    if (!isset($_GET['datetimeInterview'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Interview date and time is required']);
        exit();
    }

    $applicationID = $_GET['applicationID'];
    $datetimeInterview = $_GET['datetimeInterview'];

    include_once '../../../../server/db.php';

    $updateApplicationQuery = "UPDATE adoption_applications SET interview_completed_at = NOW(), interview_scheduled_at = '$datetimeInterview' WHERE id = $applicationID";
    $updateApplicationResult = $conn->query($updateApplicationQuery);

    if (!$updateApplicationResult) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update application status']);
        exit();
    }

    $conn->close();

    echo json_encode(['success' => 'Interview completed successfully']);
?>