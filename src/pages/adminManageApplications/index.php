<?php
    session_start();
    $activeAdminPage = 'applications';

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('Location: ../adminDashboard');
        exit();
    }

    if (!isset($_GET['applicationID'])) {
        header('Location: ../adminApplications');
        exit();
    }

    $applicationID = $_GET['applicationID'];

    include_once '../../../server/db.php';

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
                                aa.interview_scheduled_at,
                                aa.interview_completed_at,
                                aa.home_environment,
                                aa.previous_experience,
                                aa.commitment_statement
                            FROM adoption_applications aa
                            JOIN users u ON aa.user_id = u.id
                            JOIN pets p ON p.id = aa.pet_id
                            JOIN breeds b ON b.id = p.breed_id
                            WHERE aa.id = $applicationID;";

    $getApplicationResult = $conn->query($getApplicationQuery);

    $application = $getApplicationResult->fetch_assoc();

    $conn->close();

    if ($application['status'] === 'withdrawn') {
        header('Location: ../adminApplications');
        exit();
    }

    $statusMap = [
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
        <title>Wonderpets Admin - Manage Application</title>

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
                <div class="main-header-title d-flex align-items-center gap-3">
                    <a href="javascript:history.back()" class="btn btn-sm btn-primary">
                        <i class="bi bi-arrow-left"></i>
                        Back
                    </a>
                    <div class="d-flex flex-column gap-2">
                        <h3>Manage Application</h3>
                        <p class="text-muted">Review and manage adoption applications.</p>
                    </div>
                </div>
                <span class="badge <?php echo $statusColors[$application['status']]; ?>" id="application-status" data-interview-completed="<?php echo $application['interview_completed_at'] === null ? 'false' : 'true'; ?>">
                    <?php echo $statusMap[$application['status']]; ?>
                </span>
            </section>
            <section class="details me-3">
                <div class="detail">
                    <h4 class="mb-3"><strong>Applicant Details</strong></h4>
                    <div class="detail-list">
                        <div class="applicant-info">
                            <h6>Name</h6>
                            <p><?php echo $application['full_name']; ?></p>
                        </div>
                        <div class="applicant-info">
                            <h6>Email</h6>
                            <p><?php echo $application['email']; ?></p>
                        </div>
                        <div class="applicant-info">
                            <h6>Phone</h6>
                            <p><?php echo $application['phone']; ?></p>
                        </div>
                        <div class="applicant-info">
                            <h6>Application Date</h6>
                            <p><?php echo date('F j, Y', strtotime($application['created_at'])); ?></p>
                        </div>
                    </div>
                </div>
                <div class="detail">
                    <h4 class="mb-3"><strong>Pet Details</strong></h4>
                    <div class="detail-list">
                        <div class="applicant-info">
                            <h6>Pet Name</h6>
                            <p><?php echo $application['name']; ?></p>
                        </div>
                        <div class="applicant-info">
                            <h6>Pet ID</h6>
                            <p><?php echo $application['pet_id']; ?></p>
                        </div>
                        <div class="applicant-info">
                            <h6>Breed</h6>
                            <p><?php echo $application['breed_name']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="detail">
                    <h4 class="mb-3"><strong>Application Details</strong></h4>
                    <div class="d-flex flex-column gap-3">
                        <div class="applicant-info">
                            <h6><strong>Home Environment</strong></h6>
                            <p><?php echo $application['home_environment']; ?></p>
                        </div>
                        <div class="applicant-info">
                            <h6><strong>Previous Experience</strong></h6>
                            <p><?php echo $application['previous_experience']; ?></p>
                        </div>
                        <div class="applicant-info">
                            <h6><strong>Commitment Statement</strong></h6>
                            <p><?php echo $application['commitment_statement']; ?></p>
                        </div>
                    </div>
                </div>
            </section>
            <section class="admin-actions">
                <h4 class="mb-3"><strong>Admin Actions</strong></h4>
                <div class="mb-3 d-flex flex-column gap-2">
                    <button class="btn btn-primary" id="schedule-interview-btn">Schedule Interview</button>
                    <button class="btn btn-primary" id="complete-interview-btn">Complete Interview</button>
                    <button class="btn btn-success" id="approve-application-btn">Approve Application</button>
                    <button class="btn btn-danger" id="deny-application-btn">Deny Application</button>
                    <button class="btn btn-dark" id="complete-application-btn">Complete Application</button>
                </div>
                <form method="post" id="admin-actions-form">
                    <input type="hidden" name="applicationID" id="applicationID" value="<?php echo $applicationID; ?>">
                    <input type="hidden" name="approvedBy" id="approvedBy" value="<?php echo $_SESSION['user_id']; ?>">
                    <div class="mb-3" id="interview-schedule-container">
                        <label for="interview-schedule" class="form-label">Interview Schedule</label>
                        <input type="datetime-local" name="interview-schedule" id="interview-schedule" class="form-control">
                    </div>
                    <div class="mb-3" id="interview-completion-container">
                        <label for="interview-completion" class="form-label">Interview Completion</label>
                        <input type="datetime-local" name="interview-completion" id="interview-completion" class="form-control">
                    </div>
                    <div class="mb-3" id="denial-reason-container">
                        <label for="denial-reason" class="form-label">Denial Reason</label>
                        <textarea name="denial-reason" id="denial-reason" class="form-control"></textarea>
                    </div>
                    <div class="mb-3" id="admin-notes-container">
                        <label for="admin-notes" class="form-label">Admin Notes</label>
                        <textarea name="admin-notes" id="admin-notes" class="form-control"></textarea>
                    </div>
                    <div class="row d-flex justify-content-end gap-2" id="submit-container">
                        <div class="col m-0 pe-0">
                            <button type="submit" class="btn btn-dark w-100 m-0">Submit</button>
                        </div>
                        <div class="col m-0 ps-0">
                            <button type="button" class="btn btn-secondary w-100 m-0" id="cancel-btn">Cancel</button>
                        </div>
                    </div>
                </form>
            </section>
        </main>
    </body>
</html>