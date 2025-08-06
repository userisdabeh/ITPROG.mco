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

    $query = "DELETE FROM users WHERE id = $userId";
    $result = mysqli_query($conn, $query);

    if (mysqli_affected_rows($conn) === 0) {
        http_response_code(404);
        echo json_encode(['error' => true, 'message' => 'Failed to delete user']);
        exit;
    }

    mysqli_close($conn);
    echo json_encode(['message' => 'User deleted successfully']);
?>