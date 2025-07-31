<nav>
    <div class="brand">
        <span class="brand-icon">
            <i class="bi bi-heart-fill"></i>
        </span>
        <span class="brand-name">Wonderpets</span>
    </div>
    <ul class="nav-links nav-wrapper">
        <li>
            <a href="../adminDashboard" class="nav-link <?php echo $activeAdminPage === 'dashboard' ? 'active' : '' ?>">
                <i class="bi bi-columns-gap"></i>
                Dashboard
            </a>
        </li>
        <li>
            <a href="../adminPetManager" class="nav-link <?php echo $activeAdminPage === 'pets' ? 'active' : '' ?>">
                <i class="bi bi-house-door"></i>
                Pet Manager
            </a>
        </li>
        <li>
            <a href="../adminUserManager" class="nav-link <?php echo $activeAdminPage === 'users' ? 'active' : '' ?>">
                <i class="bi bi-person"></i>
                User Manager
            </a>
        </li>
        <li>
            <a href="../adminReports" class="nav-link <?php echo $activeAdminPage === 'reports' ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-bar-graph"></i>
                Reports
            </a>
        </li>
        <li>
            <a href="../adminApplications" class="nav-link <?php echo $activeAdminPage === 'applications' ? 'active' : '' ?>">
                <i class="bi bi-heart"></i>
                Applications
            </a>
        </li>
    </ul>
    <div class="logout nav-wrapper">
        <a href="#" class="nav-link">
            <i class="bi bi-box-arrow-right"></i>
            Logout
        </a>
    </div>
</nav>