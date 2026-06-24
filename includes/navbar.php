<style>
    :root {
        --primary-color: #4dc387;
        --primary-dark: #3da876;
        --primary-light: #e8f5f0;
        --white: #ffffff;
        --light-bg: #f8f9fa;
        --dark-text: #2c3e50;
        --gray-text: #6c757d;
    }
        /* Custom Button Styles */
        .btn {
      border-radius: 25px;
      padding: 8px 20px;
      font-weight: 500;
    }

    .btn-sm {
      border-radius: 20px;
      padding: 5px 15px;
    }

    .round {
      border-radius: 10px;
    }

    .round-5 {
      border-radius: 5px;
    }

    
    body {
            font-family: 'Open Sans', sans-serif;
            background-color: var(--light-bg);
            background-image: url('data:image/svg+xml,%3Csvg width="52" height="26" viewBox="0 0 52 26" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%234dc387" fill-opacity="0.05"%3E%3Cpath d="M10 10c0-2.21-1.79-4-4-4-3.314 0-6-2.686-6-6h2c0 2.21 1.79 4 4 4 3.314 0 6 2.686 6 6 0 2.21 1.79 4 4 4 3.314 0 6 2.686 6 6 0 2.21 1.79 4 4 4v2c-3.314 0-6-2.686-6-6 0-2.21-1.79-4-4-4-3.314 0-6-2.686-6-6zm25.464-1.95l8.486 8.486-1.414 1.414-8.486-8.486 1.414-1.414z" /%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
        }

</style>

<nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top py-lg-0 px-4 px-lg-5 wow fadeIn" data-wow-delay="0.1s">
    <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/user/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../index.php' : 'index.php'; ?>" class="navbar-brand p-0">
        <img class="img-fluid me-3" src="<?php echo (strpos($_SERVER['PHP_SELF'], '/user/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../img/icon/icon-10.png' : 'img/icon/icon-10.png'; ?>" alt="Icon" />
        <h1 class="m-0 text-primary d-none d-md-inline-block">Taman Kopses Ciseeng</h1>
    </a>
    <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse py-4 py-lg-0" id="navbarCollapse">
        <div class="navbar-nav ms-auto">
            <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/user/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../index.php' : 'index.php'; ?>" class="nav-item nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php' && !strpos($_SERVER['PHP_SELF'], '/user/') && !strpos($_SERVER['PHP_SELF'], '/admin/')) ? 'active' : ''; ?>">Home</a>
            <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/user/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../pricelist.php' : 'pricelist.php'; ?>" class="nav-item nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'pricelist.php') ? 'active' : ''; ?>">Pricelist</a>
            <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/user/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../contact.php' : 'contact.php'; ?>" class="nav-item nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'active' : ''; ?>">Contact</a>
            <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
                <?php if (function_exists('isAdmin') && isAdmin()): ?>
                    <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? 'dashboard.php' : 'admin/dashboard.php'; ?>" class="nav-item nav-link">Admin Panel</a>
                <?php else: ?>
                    <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/user/') !== false) ? 'dashboard.php' : 'user/dashboard.php'; ?>" class="nav-item nav-link <?php echo (strpos($_SERVER['PHP_SELF'], '/user/') !== false && basename($_SERVER['PHP_SELF']) != 'login.php' && basename($_SERVER['PHP_SELF']) != 'register.php') ? 'active' : ''; ?>">Dashboard</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle me-2"></i><?php echo $_SESSION['user_name']; ?>
                </a>
                <div class="dropdown-menu m-0">
                    <?php if (function_exists('isAdmin') && isAdmin()): ?>
                        <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? 'dashboard.php' : 'admin/dashboard.php'; ?>" class="dropdown-item">Admin Panel</a>
                        <div class="dropdown-divider"></div>
                        <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? 'logout.php' : 'admin/logout.php'; ?>" class="dropdown-item">Logout</a>
                    <?php else: ?>
                        <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/user/') !== false) ? 'dashboard.php' : 'user/dashboard.php'; ?>" class="dropdown-item">Dashboard</a>
                        <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/user/') !== false) ? 'profile.php' : 'user/profile.php'; ?>" class="dropdown-item">Profile</a>
                        <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/user/') !== false) ? 'my-tickets.php' : 'user/my-tickets.php'; ?>" class="dropdown-item">My Tickets</a>
                        <div class="dropdown-divider"></div>
                        <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/user/') !== false) ? 'logout.php' : 'user/logout.php'; ?>" class="dropdown-item">Logout</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/user/') !== false) ? 'login.php' : 'user/login.php'; ?>" class="btn btn-primary">Reservasi<i class="fa fa-arrow-right ms-3"></i></a>
        <?php endif; ?>
    </div>
</nav>
