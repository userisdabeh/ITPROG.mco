<style>
    .user-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 2rem;
        background-color: #fff;
        border-bottom: 1px solid #eee;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .user-header .logo-link {
        display: flex;
        align-items: center;
        text-decoration: none;
        font-weight: 700;
        font-size: 1.5rem;
        color: #1e1e1e;
    }

    .user-header .brand-icon {
        font-size: 1.5rem;
        margin-right: 0.5rem;
    }

    .user-header .nav-section {
        display: flex;
        gap: 1.5rem;
    }

    .user-header .nav-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        color: #1e1e1e;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s ease-in-out;
    }

    .user-header .nav-item:hover {
        color: #f97316;
    }

    .user-header .nav-icon {
        width: 20px;
        height: 20px;
    }
</style>

<header class="user-header">
    <div class="logo-section">
        <a href="#" class="logo-link">
            <span class="brand-icon">🧡</span>
            <span class="brand-name">WonderPets</span>
        </a>
    </div>

    <nav class="nav-section">
        <a href="#" class="nav-item">
            <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A5.5 5.5 0 0112 15.5a5.5 5.5 0 016.879 2.304M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <a href="../../pages/userProfile/index.php" class="profile-link">
            <span>Profile</span>
        </a>

        <a href="../RegistrationAndLogin/logout.php" class="nav-item">
            <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
            </svg>
            <span>Logout</span>
        </a>
    </nav>
</header>
