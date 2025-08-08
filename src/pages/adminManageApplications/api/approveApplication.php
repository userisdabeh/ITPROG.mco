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

    if (!isset($input['adminNotes'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Admin notes are required']);
        exit();
    }

    if (!isset($input['approvedBy'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Approved by is required']);
        exit();
    }

    $applicationID = $input['applicationID'];
    $adminNotes = $input['adminNotes'];
    $approvedBy = $input['approvedBy'];

    include_once '../../../../server/db.php';

    $approveApplicationQuery = "UPDATE adoption_applications SET status = 'approved', admin_notes = '$adminNotes', approved_at = NOW(), approved_by = $approvedBy WHERE id = $applicationID;";
    $approveApplicationResult = $conn->query($approveApplicationQuery);

    if (!$approveApplicationResult) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to approve application']);
        exit();
    }

    $conn->close();
    echo json_encode(['success' => 'Application approved successfully']);
?>