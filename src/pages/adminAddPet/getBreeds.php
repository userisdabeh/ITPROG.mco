<?php

include_once '../../../server/db.php';

if(isset($_GET['type_id'])) {
    $type_id = $_GET['type_id'];

    $getBreedsQuery = "SELECT * FROM breeds WHERE pet_type_id = ?";

    $getBreedsResult = $conn->prepare($getBreedsQuery);
    $getBreedsResult->bind_param('i', $type_id);
    $getBreedsResult->execute();
    
    $result = $getBreedsResult->get_result();
    $breeds = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode($breeds);
} else {
    echo json_encode([]);
}

?>