<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        http_response_code(400);
        echo json_encode(['error' => true, 'message' => 'Unauthorized access']);
        exit;
    }

    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => true, 'message' => 'User ID is required']);
        exit;
    }

    include_once '../../../../server/db.php';

    $userId = intval($_GET['id']);

    $query = "SELECT * FROM users WHERE id = $userId";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 0) {
        http_response_code(404);
        echo json_encode(['error' => true, 'message' => 'User not found']);
        exit;
    }

    $user = mysqli_fetch_assoc($result);
    echo json_encode($user);

    mysqli_close($conn);
    
?>