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

    $applicationID = $_GET['applicationID'];

    include_once '../../../../server/db.php';

    $updateApplicationQuery = "UPDATE adoption_applications SET status = 'under_review' WHERE id = $applicationID";
    $updateApplicationResult = $conn->query($updateApplicationQuery);

    if (!$updateApplicationResult) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update application status']);
    }

    $conn->close();

    echo json_encode(['success' => 'Application status updated successfully']);
?>