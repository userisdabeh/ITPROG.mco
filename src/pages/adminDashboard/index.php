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
        $getPendingApplications = $conn->prepare("SELECT COUNT(*) AS pendings FROM adoption_applications WHERE status = 'submitted'");
        if ($getPendingApplications) {
            $getPendingApplications->execute();
            $result = $getPendingApplications->get_result();
            $pendingApplications = $result->fetch_assoc();
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    // Total adoptions = real adoptions + completed apps
    try {
        $getTotalAdoptions = $conn->query("
            SELECT COUNT(*) AS total_adoptions
            FROM (
                SELECT id, created_at AS event_time FROM adoptions
                UNION ALL
                SELECT id, COALESCE(completed_at, updated_at, created_at) AS event_time
                FROM adoption_applications
                WHERE status = 'completed'
            ) t
    ");
    $totalAdoptions = $getTotalAdoptions->fetch_assoc();
    } catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
    }


    $month = date('m');
    $year = date('Y');

    // Pets added this month
    $getPetsThisMonth = $conn->prepare("SELECT COUNT(*) AS pets_this_month FROM pets WHERE MONTH(created_at) = ? AND YEAR(created_at) = ?");
    $getPetsThisMonth->bind_param("ii", $month, $year);
    $getPetsThisMonth->execute();
    $petsMonth = $getPetsThisMonth->get_result()->fetch_assoc();

    // Users registered this month
    $getUsersThisMonth = $conn->prepare("SELECT COUNT(*) AS users_this_month FROM users WHERE MONTH(created_at) = ? AND YEAR(created_at) = ?");
    $getUsersThisMonth->bind_param("ii", $month, $year);
    $getUsersThisMonth->execute();
    $usersMonth = $getUsersThisMonth->get_result()->fetch_assoc();

    // New pending apps this month
    $getPendingAppsThisMonth = $conn->prepare("SELECT COUNT(*) AS pendings_this_month FROM adoption_applications WHERE status = 'submitted' AND MONTH(created_at) = ? AND YEAR(created_at) = ?");
    $getPendingAppsThisMonth->bind_param("ii", $month, $year);
    $getPendingAppsThisMonth->execute();
    $pendingMonth = $getPendingAppsThisMonth->get_result()->fetch_assoc();

    // Adoptions this month = real adoptions + completed apps in month
    $getAdoptionsThisMonth = $conn->prepare("
        SELECT COUNT(*) AS adoptions_this_month
        FROM (
            SELECT created_at AS event_time FROM adoptions
            WHERE MONTH(created_at) = ? AND YEAR(created_at) = ?
            UNION ALL
            SELECT COALESCE(completed_at, updated_at, created_at) AS event_time
            FROM adoption_applications
            WHERE status = 'completed'
                AND MONTH(COALESCE(completed_at, updated_at, created_at)) = ?
                AND YEAR(COALESCE(completed_at, updated_at, created_at)) = ?
        ) t
    ");
    $getAdoptionsThisMonth->bind_param("iiii", $month, $year, $month, $year);
    $getAdoptionsThisMonth->execute();
    $adoptionsMonth = $getAdoptionsThisMonth->get_result()->fetch_assoc();


    // Latest added pet
    $getRecentPet = $conn->query("
        SELECT 
            pets.name AS pet_name,
            breeds.breed_name,
            pets.created_at
        FROM pets
        LEFT JOIN breeds ON pets.breed_id = breeds.id
        ORDER BY pets.created_at DESC
        LIMIT 1
    ");
    $recentPet = $getRecentPet->fetch_assoc();

    // Latest approved application
    $getRecentApproved = $conn->query("
        SELECT u.full_name, p.name AS pet_name, aa.updated_at 
        FROM adoption_applications aa 
        JOIN users u ON aa.user_id = u.id 
        JOIN pets p ON aa.pet_id = p.id 
        WHERE aa.status = 'approved' 
        ORDER BY aa.updated_at DESC 
        LIMIT 1
    ");
    $recentApproved = $getRecentApproved->fetch_assoc();

    // Latest rejected application
    $getRecentRejected = $conn->query("
        SELECT u.full_name, p.name AS pet_name, aa.updated_at 
        FROM adoption_applications aa 
        JOIN users u ON aa.user_id = u.id 
        JOIN pets p ON aa.pet_id = p.id 
        WHERE aa.status = 'denied' 
        ORDER BY aa.updated_at DESC 
        LIMIT 1
    ");
    $recentRejected = $getRecentRejected->fetch_assoc();

    // Latest adoption OR completed application
    $getRecentAdoption = $conn->query("
        SELECT u.full_name, p.name AS pet_name, ev.event_time
        FROM (
            SELECT a.user_id, a.pet_id, a.created_at AS event_time
            FROM adoptions a
            UNION ALL
            SELECT aa.user_id, aa.pet_id,
                   COALESCE(aa.completed_at, aa.updated_at, aa.created_at) AS event_time
            FROM adoption_applications aa
            WHERE aa.status = 'completed'
        ) ev
        JOIN users u ON ev.user_id = u.id
        JOIN pets p ON ev.pet_id = p.id
        ORDER BY ev.event_time DESC
        LIMIT 1
    ");
    $recentAdoption = $getRecentAdoption->fetch_assoc();


    function timeAgo($datetime) {
        $timestamp = strtotime($datetime);
        $difference = time() - $timestamp;

        if ($difference < 60)
            return $difference . ' seconds ago';
        elseif ($difference < 3600)
            return floor($difference / 60) . ' minutes ago';
        elseif ($difference < 86400)
            return floor($difference / 3600) . ' hours ago';
        else
            return floor($difference / 86400) . ' days ago';
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
                        <p class="card-delta-value">+ <?php echo $petsMonth['pets_this_month']; ?> this month</p>
                    </div>
                </article>
                <article class="summary-card">
                    <div class="card-header">
                        <h6 class="card-title">Total Users</h6>
                        <i class="bi bi-person"></i>
                    </div>
                    <div class="card-body">
                        <h2 class="card-value"><?php echo $totalUsers['total_users']; ?></h2>
                        <p class="card-delta-value">+ <?php echo $usersMonth['users_this_month']; ?> this month</p>
                    </div>
                </article>
                <article class="summary-card">
                    <div class="card-header">
                        <h6 class="card-title">Pending Applications</h6>
                        <i class="bi bi-person-lines-fill"></i>
                    </div>
                    <div class="card-body">
                        <h2 class="card-value"><?php echo $pendingApplications['pendings']; ?></h2>
                        <p class="card-delta-value">+ <?php echo $pendingMonth['pendings_this_month']; ?> this month</p>
                    </div>
                </article>
                <article class="summary-card">
                    <div class="card-header">
                        <h6 class="card-title">Total Adoptions</h6>
                        <i class="bi bi-heart"></i>
                    </div>
                    <div class="card-body">
                        <h2 class="card-value"><?php echo $totalAdoptions['total_adoptions']; ?></h2>
                        <p class="card-delta-value">+ <?php echo $adoptionsMonth['adoptions_this_month']; ?> this month</p>
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
                                <?= $recentPet ? $recentPet['breed_name'] . " - " . $recentPet['pet_name'] : 'No recent pet' ?>
                            </div>
                            <span class="activity-time"><?= $recentPet ? timeAgo($recentPet['created_at']) : '' ?></span>
                        </div>
                        <div class="activity-item">
                            <div class="activity-alert">
                                <h6 class="activity-title">Application Approved</h6>
                                <p class="activity-details">
                                    <?= $recentApproved ? htmlspecialchars($recentApproved['full_name'] . ' - ' . $recentApproved['pet_name']) : 'No recent approval' ?>
                                </p>
                            </div>
                            <span class="activity-time"><?= $recentApproved ? timeAgo($recentApproved['updated_at']) : 'N/A' ?></span>
                        </div>
                        <div class="activity-item">
                            <div class="activity-alert">
                                <h6 class="activity-title">Application Denied</h6>
                                <p class="activity-details">
                                    <?= $recentRejected ? htmlspecialchars($recentRejected['full_name'] . ' - ' . $recentRejected['pet_name']) : 'No recent rejection' ?>
                                </p>
                            </div>
                            <span class="activity-time"><?= $recentRejected ? timeAgo($recentRejected['updated_at']) : 'N/A' ?></span>
                        </div>
                        <div class="activity-item">
                            <div class="activity-alert">
                                <h6 class="activity-title">Pet Adopted</h6>
                                <p class="activity-details">
                                    <?= $recentAdoption ? htmlspecialchars($recentAdoption['full_name'] . ' - ' . $recentAdoption['pet_name']) : 'No recent adoption' ?>
                                </p>
                            </div>
                            <span class="activity-time"><?= $recentAdoption ? timeAgo($recentAdoption['event_time']) : 'N/A' ?></span>
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