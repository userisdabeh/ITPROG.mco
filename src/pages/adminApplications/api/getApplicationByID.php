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

    $getApplicationQuery = "SELECT 
                                aa.id AS adoption_id,
                                u.full_name,
                                u.email,
                                u.phone,
                                p.id AS pet_id,
                                p.name,
                                b.breed_name,
                                p.age_years,
                                p.age_months,
                                aa.created_at,
                                aa.status,
                                aa.home_environment,
                                aa.previous_experience,
                                aa.commitment_statement
                            FROM adoption_applications aa
                            JOIN users u ON aa.user_id = u.id
                            JOIN pets p ON p.id = aa.pet_id
                            JOIN breeds b ON b.id = p.breed_id
                            WHERE aa.id = $applicationID;";

    $applicationResult = $conn->query($getApplicationQuery);

    if ($applicationResult->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Application not found']);
        exit();
    }

    $application = $applicationResult->fetch_assoc();

    $conn->close();

    echo json_encode($application);
?>