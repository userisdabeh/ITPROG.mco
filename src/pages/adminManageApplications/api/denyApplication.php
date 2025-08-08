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

    if (!isset($input['denialReason'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Denial reason is required']);
        exit();
    }

    if (!isset($input['adminNotes'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Admin notes are required']);
        exit();
    }

    $applicationID = $input['applicationID'];
    $denialReason = $input['denialReason'];
    $adminNotes = $input['adminNotes'];
    
    include_once '../../../../server/db.php';

    $denyApplicationQuery = "UPDATE adoption_applications SET status = 'denied', denial_reason = '$denialReason', admin_notes = '$adminNotes' WHERE id = $applicationID;";
    $denyApplicationResult = $conn->query($denyApplicationQuery);

    if (!$denyApplicationResult) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to deny application']);
        exit();
    }

    $conn->close();
    echo json_encode(['success' => 'Application denied successfully']);
?>