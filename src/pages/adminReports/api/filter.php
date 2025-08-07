<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        http_response_code(400);
        echo json_encode(['error' => true, 'message' => 'Unauthorized access']);
        exit;
    }

    if (!isset($_GET['filter'])) {
        http_response_code(400);
        echo json_encode(['error' => true, 'message' => 'Filter is required']);
        exit;
    }

    $filter = $_GET['filter'];
    $whereClause = '';

    switch ($filter) {
        case 'monthly':
            $whereClause = "WHERE aa.created_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
            break;
        case 'six_months':
            $whereClause = "WHERE aa.created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)";
            break;
        case 'yearly':
            $whereClause = "WHERE aa.created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
            break;
        default:
        case 'overall':
            $whereClause = '';
            break;
    }

    include_once '../../../../server/db.php';

    $getSummaryDataQuery = "SELECT COUNT(CASE WHEN status = 'submitted' THEN 1 END) AS total_submitted, 
                                COUNT(CASE WHEN status = 'under_review' THEN 1 END) AS total_for_review, 
                                COUNT(CASE WHEN status = 'interview_required' THEN 1 END) AS total_for_interview, 
                                COUNT(CASE WHEN status = 'approved' THEN 1 END) AS total_approved, 
                                COUNT(CASE WHEN status = 'denied' THEN 1 END) AS total_denied, 
                                COUNT(CASE WHEN status = 'completed' THEN 1 END) AS total_completed, 
                                COUNT(CASE WHEN status = 'withdrawn' THEN 1 END) AS total_withdrawn, 
                                COUNT(*) AS total_applications 
                            FROM adoption_applications aa
                            $whereClause";
    $getSummaryDataResult = $conn->query($getSummaryDataQuery);

    if (!$getSummaryDataResult) {
        http_response_code(500);
        echo json_encode(['error' => true, 'message' => 'Failed to fetch data']);
        exit;
    }

    $summaryData = $getSummaryDataResult->fetch_assoc();

    echo json_encode(['error' => false, 'data' => $summaryData]);

    $conn->close();
?>