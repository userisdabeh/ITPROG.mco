<?php
session_start();
require '../../../server/db.php';
require_once 'components/statusIcons.php';
require_once 'components/applicationCard.php';

// if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// success/errors
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'withdrawn':
            $success_message = "Application withdrawn successfully.";
            break;
    }
}

if (isset($_GET['error'])) {
    $error_message = htmlspecialchars($_GET['error']);
}

// application withdrawal
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'withdraw') {
    $application_id = (int) $_POST['application_id'];
    
    // is user
    $verify_sql = "SELECT id, status FROM adoption_applications WHERE id = $application_id AND user_id = $user_id AND status IN ('submitted', 'under_review', 'interview_required')";
    $verify_result = $conn->query($verify_sql);
    
    if ($verify_result && $verify_result->num_rows > 0) {
        $withdraw_sql = "UPDATE adoption_applications SET status = 'withdrawn', updated_at = NOW() WHERE id = $application_id";
        if ($conn->query($withdraw_sql)) {
            header("Location: index.php?success=withdrawn");
            exit();
        } else {
            $error_message = "Error withdrawing application. Please try again.";
        }
    } else {
        $error_message = "Invalid application or application cannot be withdrawn.";
    }
}

// user application with pet and status info
$applications_sql = "
    SELECT 
        aa.*,
        p.name as pet_name,
        p.age_years,
        p.age_months,
        p.gender,
        p.adoption_fee,
        p.status as pet_status,
        p.pet_image as image_data,
        p.pet_image_type as image_type,
        b.breed_name,
        pt.type_name
    FROM adoption_applications aa
    INNER JOIN pets p ON aa.pet_id = p.id
    LEFT JOIN breeds b ON p.breed_id = b.id
    INNER JOIN pet_types pt ON p.pet_type_id = pt.id
    WHERE aa.user_id = $user_id
    ORDER BY aa.created_at DESC
";

$applications_result = $conn->query($applications_sql);

// if no applications found
if (!$applications_result) {
    $error_message = "Database error: " . $conn->error;
    $applications = [];
} else {
    $applications = $applications_result->fetch_all(MYSQLI_ASSOC);
}

// group by status
$grouped_applications = [
    'active' => [],
    'completed' => []
];

foreach ($applications as $application) {
    if (in_array($application['status'], ['submitted', 'under_review', 'interview_required', 'approved'])) {
        $grouped_applications['active'][] = $application;
    } else {
        $grouped_applications['completed'][] = $application;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications - WonderPets</title>
    <link rel="stylesheet" href="index.css?v=<?= time() ?>">
</head>
<body>
    <?php require_once('../../components/userHeader.php'); ?>
    
    <div class="applications-container">
        <div class="applications-header">
            <h1>Adoption Applications</h1>
            <p class="applications-subtitle">Track the status of your pet adoption applications</p>
        </div>

        <?php if (!empty($success_message)): ?>
            <div class="success-message">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                </svg>
                <?= $success_message ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                </svg>
                <?= $error_message ?>
            </div>
        <?php endif; ?>

        <?php if (empty($applications)): ?>
            <div class="no-applications">
                <div class="no-applications-icon">
                    <svg width="64" height="64" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4.414A2 2 0 0 0 3 11.586l-2 2V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
                        <path d="M3 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3 6a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 6zm0 2.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z"/>
                    </svg>
                </div>
                <h2>No Applications Yet</h2>
                <p>You haven't submitted any adoption applications yet. Browse our available pets to find your perfect match!</p>
                <div class="no-applications-actions">
                    <a href="../home/index.php" class="btn btn-lg btn-primary">Browse Available Pets</a>
                </div>
            </div>
        <?php else: ?>
            <!-- active applications -->
            <?php if (!empty($grouped_applications['active'])): ?>
                <div class="applications-section">
                    <div class="section-header">
                        <h2>Active Applications</h2>
                        <span class="section-count"><?= count($grouped_applications['active']) ?> application<?= count($grouped_applications['active']) !== 1 ? 's' : '' ?></span>
                    </div>
                    
                    <div class="applications-grid">
                        <?php foreach ($grouped_applications['active'] as $application): ?>
                            <?php 
                                $statusBadge = getStatusBadge($application['status']); 
                                $canWithdraw = in_array($application['status'], ['submitted', 'under_review', 'interview_required']);
                                renderApplicationCard($application, $statusBadge, false, $canWithdraw);
                            ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- completed applications -->
            <?php if (!empty($grouped_applications['completed'])): ?>
                <div class="applications-section">
                    <div class="section-header">
                        <h2>Completed Applications</h2>
                        <span class="section-count"><?= count($grouped_applications['completed']) ?> application<?= count($grouped_applications['completed']) !== 1 ? 's' : '' ?></span>
                    </div>
                    
                    <div class="applications-grid">
                        <?php foreach ($grouped_applications['completed'] as $application): ?>
                            <?php 
                                $statusBadge = getStatusBadge($application['status'], $application['denial_reason']);
                                renderApplicationCard($application, $statusBadge, true, false);
                            ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- withdraw application -->
    <div id="withdrawModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Withdraw Application</h3>
                <button type="button" class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to withdraw your application for <strong id="petNamePlaceholder"></strong>?</p>
                <p class="warning-text">This action cannot be undone. You will need to submit a new application if you change your mind.</p>
            </div>
            <div class="modal-footer">
                <form id="withdrawForm" method="POST">
                    <input type="hidden" name="action" value="withdraw">
                    <input type="hidden" name="application_id" id="applicationIdInput">
                    <button type="button" class="btn btn-md btn-secondary modal-cancel">Cancel</button>
                    <button type="submit" class="btn btn-md btn-outline">Withdraw Application</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('withdrawModal');
        const withdrawBtns = document.querySelectorAll('.withdraw-btn');
        const closeBtn = document.querySelector('.modal-close');
        const cancelBtn = document.querySelector('.modal-cancel');
        const petNamePlaceholder = document.getElementById('petNamePlaceholder');
        const applicationIdInput = document.getElementById('applicationIdInput');

        withdrawBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const applicationId = btn.dataset.applicationId;
                const petName = btn.dataset.petName;
                
                petNamePlaceholder.textContent = petName;
                applicationIdInput.value = applicationId;
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            });
        });

        function closeModal() {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });

        setTimeout(() => {
            const messages = document.querySelectorAll('.success-message, .error-message');
            messages.forEach(message => {
                message.style.opacity = '0';
                setTimeout(() => message.remove(), 300);
            });
        }, 5000);
    </script>
</body>
</html>
