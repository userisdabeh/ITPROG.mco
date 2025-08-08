<?php
    session_start();

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized']);
        exit();
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['applicationID'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Application ID is required']);
        exit();
    }

    if (!isset($input['interviewCompletion'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Interview completion is required']);
        exit();
    }

    $applicationID = $input['applicationID'];
    $interviewCompletion = $input['interviewCompletion'];

    include_once '../../../../server/db.php';

    $completeInterviewQuery = "UPDATE adoption_applications SET interview_completed_at = '$interviewCompletion' WHERE id = $applicationID";
    $completeInterviewResult = $conn->query($completeInterviewQuery);

    if (!$completeInterviewResult) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to complete interview']);
        exit();
    }

    $conn->close();
    echo json_encode(['success' => 'Interview completed successfully']);
?>