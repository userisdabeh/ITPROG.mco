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

    if (!isset($input['interviewSchedule'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Interview schedule is required']);
        exit();
    }

    $applicationID = $input['applicationID'];
    $interviewSchedule = $input['interviewSchedule'];

    include_once '../../../../server/db.php';

    $scheduleInterviewQuery = "UPDATE adoption_applications SET status = 'interview_required', interview_scheduled_at = '$interviewSchedule' WHERE id = $applicationID";
    $scheduleInterviewResult = $conn->query($scheduleInterviewQuery);

    if (!$scheduleInterviewResult) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to schedule interview']);
        exit();
    }

    $conn->close();
    echo json_encode(['success' => 'Interview scheduled successfully']);
?>