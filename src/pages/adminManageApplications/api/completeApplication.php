<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
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

$applicationID = (int)$input['applicationID'];
$adminId = (int)$_SESSION['user_id'];

require_once '../../../../server/db.php';

$conn->begin_transaction();

try {
    // Load application + pet info (lock the row)
    $stmt = $conn->prepare("
        SELECT aa.id, aa.user_id, aa.pet_id, aa.status, aa.completed_at, aa.updated_at,
               p.status AS pet_status
        FROM adoption_applications aa
        JOIN pets p ON p.id = aa.pet_id
        WHERE aa.id = ?
        FOR UPDATE
    ");
    $stmt->bind_param('i', $applicationID);
    $stmt->execute();
    $app = $stmt->get_result()->fetch_assoc();
    if (!$app) {
        throw new Exception('Application not found');
    }

    $oldStatus = $app['status'];
    $userId    = (int)$app['user_id'];
    $petId     = (int)$app['pet_id'];

    // Update application -> completed
    $upd = $conn->prepare("
        UPDATE adoption_applications
        SET status = 'completed', completed_at = NOW(), updated_at = NOW()
        WHERE id = ?
    ");
    $upd->bind_param('i', $applicationID);
    if (!$upd->execute()) {
        throw new Exception('Failed to mark application completed');
    }

    // Write history
    $hist = $conn->prepare("
        INSERT INTO adoption_history (application_id, old_status, new_status, notes, changed_by)
        VALUES (?, ?, 'completed', ?, ?)
    ");
    $notes = 'Application marked as completed by admin';
    $hist->bind_param('issi', $applicationID, $oldStatus, $notes, $adminId);
    if (!$hist->execute()) {
        throw new Exception('Failed to write adoption history');
    }

    // 4) Ensure an adoptions row exists
    $check = $conn->prepare("SELECT id FROM adoptions WHERE application_id = ? LIMIT 1");
    $check->bind_param('i', $applicationID);
    $check->execute();
    $exists = $check->get_result()->fetch_assoc();

    if (!$exists) {
        $ins = $conn->prepare("
            INSERT INTO adoptions (application_id, user_id, pet_id, adoption_date, adoption_fee_paid, processed_by, created_at)
            VALUES (?, ?, ?, CURDATE(), 0.00, ?, NOW())
        ");
        $ins->bind_param('iiii', $applicationID, $userId, $petId, $adminId);
        if (!$ins->execute()) {
            throw new Exception('Failed to create adoptions record');
        }
    }

    // Mark pet as adopted
    $petUpd = $conn->prepare("UPDATE pets SET status = 'adopted', updated_at = NOW() WHERE id = ?");
    $petUpd->bind_param('i', $petId);
    if (!$petUpd->execute()) {
        throw new Exception('Failed to update pet status');
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Application completed; pet marked as adopted.']);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    $conn->close();
}
