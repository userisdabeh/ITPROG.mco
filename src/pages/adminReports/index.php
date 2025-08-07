<?php
    session_start();
    $activeAdminPage = 'reports';

    if (!isset($_SESSION['user_id'])) {
        header('Location: ../auth/login.php');
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Wonderpets - Admin Reports</title>

        <!-- For Bootstrap Icons, Modals, and other functionalities -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous" defer></script>

        <!-- For the global admin styles -->
        <link rel="stylesheet" href="index.css?v=<?php echo time(); ?>">

        <!-- For the navigation bar -->
        <link rel="stylesheet" href="../../components/admin/nav.css?v=<?php echo time(); ?>">

        <!-- For dynamic adding of reports based on selectedfilter -->
        <script src="index.js?v=<?php echo time(); ?>" defer></script>
    </head>
    <body>
        <?php include '../../components/admin/nav.php'; ?>
        <main>
            <section class="main-header mb-3">
                <div class="main-header-title">
                    <h3>Reports</h3>
                    <p>Generate reports and analytics for the website.</p>
                </div>
                <div class="input-group" id="filter-select">
                    <span class="input-group-text">
                        <i class="bi bi-funnel"></i>
                    </span>
                    <select class="form-select" id="report-filter">
                        <option value="overall" selected>Overall</option>
                        <option value="monthly">Monthly</option>
                        <option value="six_months">Last 6 Months</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>
            </section>
            <section class="summary-cards">
                <article class="summary-card">
                    <div class="card-header mb-2">
                        <h6 class="card-title">Submitted Applications</h6>
                        <i class="bi bi-graph-up-arrow text-primary"></i>
                    </div>
                    <div class="card-body">
                        <h3 class="card-value text-primary fw-bold" id="total-submitted">0</h3>
                        <span class="card-fractional text-muted" id="total-submitted-percentage">
                            0% of total applications
                        </span>
                    </div>
                </article>
                <article class="summary-card">
                    <div class="card-header mb-2">
                        <h6 class="card-title">Under Review</h6>
                        <i class="bi bi-people text-primary"></i>
                    </div>
                    <div class="card-body">
                        <h3 class="card-value text-primary fw-bold" id="total-for-review">0</h3>
                        <span class="card-fractional text-muted" id="total-for-review-percentage">
                            0% of total applications
                        </span>
                    </div>
                </article>
                <article class="summary-card">
                    <div class="card-header mb-2">
                        <h6 class="card-title">Interview Required</h6>
                        <i class="bi bi-calendar-check text-warning"></i>
                    </div>
                    <div class="card-body">
                        <h3 class="card-value text-warning fw-bold" id="total-for-interview">0</h3>
                        <span class="card-fractional text-muted" id="total-for-interview-percentage">
                            0% of total applications
                        </span>
                    </div>
                </article>
                <article class="summary-card">
                    <div class="card-header mb-2">
                        <h6 class="card-title">Approved Applications</h6>
                        <i class="bi bi-check-circle-fill text-success"></i>
                    </div>
                    <div class="card-body">
                        <h3 class="card-value text-success fw-bold" id="total-approved">0</h3>
                        <span class="card-fractional text-muted" id="total-approved-percentage">
                            0% of total applications
                        </span>
                    </div>
                </article>
                <article class="summary-card">
                    <div class="card-header mb-2">
                        <h6 class="card-title">Denied Applications</h6>
                        <i class="bi bi-x-circle-fill text-danger"></i>
                    </div>
                    <div class="card-body">
                        <h3 class="card-value text-danger fw-bold" id="total-denied">0</h3>
                        <span class="card-fractional text-muted" id="total-denied-percentage">
                            0% of total applications
                        </span>
                    </div>
                </article>
                <article class="summary-card">
                    <div class="card-header mb-2">
                        <h6 class="card-title">Completed Applications</h6>
                        <i class="bi bi-heart-fill text-success"></i>
                    </div>
                    <div class="card-body">
                        <h3 class="card-value text-success fw-bold" id="total-completed">0</h3>
                        <span class="card-fractional text-muted" id="total-completed-percentage">
                            0% of total applications
                        </span>
                    </div>
                </article>
                <article class="summary-card">
                    <div class="card-header mb-2">
                        <h6 class="card-title">Withdrawn Applications</h6>
                        <i class="bi bi-dash-circle-fill text-danger"></i>
                    </div>
                    <div class="card-body">
                        <h3 class="card-value text-danger fw-bold" id="total-withdrawn">0</h3> 
                        <span class="card-fractional text-muted" id="total-withdrawn-percentage">
                            0% of total applications
                        </span>
                    </div>
                </article>
                <article class="summary-card">
                    <div class="card-header mb-2">
                        <h6 class="card-title">Total Applications</h6>
                        <i class="bi bi-house-heart-fill text-primary"></i>
                    </div>
                    <div class="card-body">
                        <h3 class="card-value text-primary fw-bold" id="total-applications">0</h3>
                        <span class="card-fractional text-muted" id="total-applications-percentage">
                            Overall
                        </span>
                    </div>
                </article>
            </section>
        </main>
    </body>
</html>