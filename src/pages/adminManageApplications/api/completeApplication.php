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

    $applicationID = $input['applicationID'];

    include_once '../../../../server/db.php';

    $completeApplicationQuery = "UPDATE adoption_applications SET status = 'completed', completed_at = NOW() WHERE id = $applicationID;";
    $completeApplicationResult = $conn->query($completeApplicationQuery);

    if (!$completeApplicationResult) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to complete application']);
        exit();
    }

    $conn->close();
    echo json_encode(['success' => 'Application completed successfully']);
?>