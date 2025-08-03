<?php
    session_start();
    $activeAdminPage = 'dashboard';

    include_once '../../../server/db.php';

    if(!isset($_SESSION['user_id'])) {
        header('Location: ../auth/login.php');
        exit;
    }

    $user_id = $_SESSION['user_id'];

    // Get total pets
    try {
        $getTotalPets = $conn->prepare("SELECT COUNT(*) AS total_pets FROM pets");
        if ($getTotalPets) {
            $getTotalPets->execute();
            $result = $getTotalPets->get_result();
            $totalPets = $result->fetch_assoc();
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    // Get total users
    try {
        $getTotalUsers = $conn->prepare("SELECT COUNT(*) AS total_users FROM users");
        if ($getTotalUsers) {
            $getTotalUsers->execute();
            $result = $getTotalUsers->get_result();
            $totalUsers = $result->fetch_assoc();
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    // Get pending applications
    try {
        $getPendingApplications = $conn->prepare("SELECT COUNT(*) AS pendings FROM adoption_applications WHERE status = 'pending'");
        if ($getPendingApplications) {
            $getPendingApplications->execute();
            $result = $getPendingApplications->get_result();
            $pendingApplications = $result->fetch_assoc();
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    // Get total adoptions
    try {
        $getTotalAdoptions = $conn->prepare("SELECT COUNT(*) AS total_adoptions FROM adoptions");
        if ($getTotalAdoptions) {
            $getTotalAdoptions->execute();
            $result = $getTotalAdoptions->get_result();
            $totalAdoptions = $result->fetch_assoc();
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- For Bootstrap Icons, Modals, and other functionalities -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous" defer></script>

        <!-- For the global admin styles -->
        <link rel="stylesheet" href="index.css?v=<?php echo time(); ?>">

        <!-- For the navigation bar -->
        <link rel="stylesheet" href="../../components/admin/nav.css?v=<?php echo time(); ?>">

        <title>Wonderpets Admin Dashboard</title>
    </head>
    <body>
        <?php include '../../components/admin/nav.php' ?>
        <main>
            <section class="main-header mb-4">
                <h3 class="page-title">Admin Dashboard</h3>
                <p class="page-description">Welcome to the admin dashboard. Here you can manage the website and the users.</p>
            </section>
            <section class="summary-cards mb-4">
                <article class="summary-card">
                    <div class="card-header">
                        <h6 class="card-title">Total Pets</h6>
                        <i class="bi bi-house-door"></i>
                    </div>
                    <div class="card-body">
                        <h2 class="card-value"><?php echo $totalPets['total_pets']; ?></h2>
                        <p class="card-delta-value">+ 12 this month</p>
                    </div>
                </article>
                <article class="summary-card">
                    <div class="card-header">
                        <h6 class="card-title">Total Users</h6>
                        <i class="bi bi-person"></i>
                    </div>
                    <div class="card-body">
                        <h2 class="card-value"><?php echo $totalUsers['total_users']; ?></h2>
                        <p class="card-delta-value">+ 12 this month</p>
                    </div>
                </article>
                <article class="summary-card">
                    <div class="card-header">
                        <h6 class="card-title">Pending Applications</h6>
                        <i class="bi bi-person-lines-fill"></i>
                    </div>
                    <div class="card-body">
                        <h2 class="card-value"><?php echo $pendingApplications['pendings']; ?></h2>
                        <p class="card-delta-value">+ 12 this month</p>
                    </div>
                </article>
                <article class="summary-card">
                    <div class="card-header">
                        <h6 class="card-title">Total Adoptions</h6>
                        <i class="bi bi-heart"></i>
                    </div>
                    <div class="card-body">
                        <h2 class="card-value"><?php echo $totalAdoptions['total_adoptions']; ?></h2>
                        <p class="card-delta-value">+ 12 this month</p>
                    </div>
                </article>
            </section>
            <section class="activity-plus-actions">
                <section class="recent-activity">
                    <h5 class="section-title mb-4">Recent Activity</h5>
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-alert">
                                <h6 class="activity-title">New Pet Added</h6>
                                <p class="activity-details">Golden Retriever - Max</p>
                            </div>
                            <span class="activity-time">2 hours ago</span>
                        </div>
                        <div class="activity-item">
                            <div class="activity-alert">
                                <h6 class="activity-title">Application Approved</h6>
                                <p class="activity-details">Dwayne Wade - Golden Retriever</p>
                            </div>
                            <span class="activity-time">7 hours ago</span>
                        </div>
                        <div class="activity-item">
                            <div class="activity-alert">
                                <h6 class="activity-title">Application Rejected</h6>
                                <p class="activity-details">Hassan Whiteside - Doberman</p>
                            </div>
                            <span class="activity-time">1 day ago</span>
                        </div>
                        <div class="activity-item">
                            <div class="activity-alert">
                                <h6 class="activity-title">Pet Adopted</h6>
                                <p class="activity-details">Tim Duncan - Dalmatian</p>
                            </div>
                            <span class="activity-time">1 day ago</span>
                        </div>
                    </div>
                </section>
                <section class="quick-actions">
                    <h5 class="section-title mb-4">Quick Actions</h5>
                    <ul class="quick-actions-list">
                        <li>
                            <a href="../adminAddPet" class="quick-action-item">
                                <i class="bi bi-plus-circle"></i>
                                <span>Add Pet</span>
                            </a>
                        </li>
                        <li>
                            <a href="../adminApplications" class="quick-action-item">
                                <i class="bi bi-heart"></i>
                                <span>Review Applications</span>
                            </a>
                        </li>
                        <li>
                            <a href="../adminReports" class="quick-action-item">
                                <i class="bi bi-file-earmark-bar-graph"></i>
                                <span>Generate Report</span>
                            </a>
                        </li>
                        <li>
                            <a href="../adminUserManager" class="quick-action-item">
                                <i class="bi bi-people"></i>
                                <span>Manage Users</span>
                            </a>
                        </li>
                    </ul>
                </section>
            </section>
        </main>
    </body>
</html>