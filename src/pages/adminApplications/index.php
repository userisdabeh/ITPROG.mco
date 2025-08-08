<?php
    session_start();

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('Location: ../adminDashboard');
        exit();
    }

    $activeAdminPage = 'applications';

    include_once '../../../server/db.php';

    $getApplicationsQuery = "SELECT 
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
                                aa.interview_scheduled_at,
                                aa.interview_completed_at
                            FROM adoption_applications aa
                            JOIN users u ON aa.user_id = u.id
                            JOIN pets p ON p.id = aa.pet_id
                            JOIN breeds b ON b.id = p.breed_id
                            WHERE aa.status != 'withdrawn'
                            ORDER BY aa.created_at DESC;";
    $applicationsResult = $conn->query($getApplicationsQuery);

    $applications = $applicationsResult->fetch_all(MYSQLI_ASSOC);

    $pendingApplications = 0;
    foreach ($applications as $application) {
        if ($application['status'] === 'submitted') {
            $pendingApplications++;
        } else if ($application['status'] === 'under_review') {
            $pendingApplications++;
        } else if ($application['status'] === 'interview_required') {
            $pendingApplications++;
        }
    }

    $conn->close();

    $displayStatus = [
        'submitted' => 'Submitted',
        'under_review' => 'Under Review',
        'interview_required' => 'Interview Required',
        'approved' => 'Approved',
        'denied' => 'Denied',
        'completed' => 'Completed',
        'withdrawn' => 'Withdrawn'
    ];

    $statusColors = [
        'submitted' => 'bg-primary',
        'under_review' => 'bg-warning',
        'interview_required' => 'bg-info',
        'approved' => 'bg-success',
        'denied' => 'bg-danger',
        'completed' => 'bg-success',
        'withdrawn' => 'bg-secondary'
    ];
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Wonderpets Admin - Manage Applications</title>

        <!-- For Bootstrap Icons, Modals, and other functionalities -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous" defer></script>

        <!-- For the global admin styles -->
        <link rel="stylesheet" href="index.css?v=<?php echo time(); ?>">

        <!-- For the navigation bar -->
        <link rel="stylesheet" href="../../components/admin/nav.css?v=<?php echo time(); ?>">

        <script src="index.js?v=<?php echo time(); ?>" defer></script>
    </head>
    <body>
        <?php include '../../components/admin/nav.php'; ?>
        <main>
            <section class="main-header mb-3">
                <div class="main-header-title">
                    <h3>Manage Applications</h3>
                    <p class="text-muted">Review and manage adoption applications.</p>
                </div>
                <span class="pending-applications d-flex align-items-center gap-2">
                    <span>Pending Applications</span>
                    <span class="badge bg-warning"><?php echo $pendingApplications; ?></span>
                </span>
            </section>
            <section class="table-responsive table-container">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Applicant</th>
                            <th scope="col">Pet</th>
                            <th scope="col">Application Date</th>
                            <th scope="col">Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $application) : ?>
                        <tr>
                            <td scope="row"><?php echo $application['full_name']; ?></td>
                            <td><?php echo $application['name']; ?></td>
                            <td><?php echo $application['created_at']; ?></td>
                            <td>
                                <span class="badge <?php echo $statusColors[$application['status']]; ?> text-capitalize">
                                    <?php echo $displayStatus[$application['status']]; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($application['status'] === 'submitted') : ?>
                                    <button type="button" class="btn btn-sm btn-primary review-application-button" id="startReviewApplication" data-bs-application-id="<?php echo $application['adoption_id']; ?>">
                                        Review Application
                                    </button>
                                <?php else : ?>
                                    <a href="../adminManageApplications/index.php?applicationID=<?php echo $application['adoption_id']; ?>" class="btn btn-sm btn-primary">View Application</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </body>
</html>