<div class="container-fluid footer bg-dark text-light footer mt-5 pt-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-4 col-md-6">
                <h4 class="text-light mb-4">Alamat</h4>
                <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>Jl. Raya Gunung Kapur Parung - Bogor</p>
                <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>0858-1077-1107</p>
                <p class="mb-2"><i class="fa fa-envelope me-3"></i>info@tirtasanitaoutbound.com</p>
            </div>
            <div class="col-lg-4 col-md-6">
                <h4 class="text-light mb-4">Jam Operasional</h4>
                <p class="mb-1">Sabtu - Kamis</p>
                <h6 class="text-light">07:00 - 17:00</h6>
                <p class="mb-1">Hari Libur Nasional</p>
                <h6 class="text-light">07:00 - 18:00</h6>
            </div>
            <div class="col-lg-4 col-md-6">
                <h4 class="text-light mb-4">Tautan Cepat</h4>
                <a class="btn btn-link" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/user/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../index.php' : 'index.php'; ?>">Home</a>
                <a class="btn btn-link" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/user/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../pricelist.php' : 'pricelist.php'; ?>">Pricelist</a>
                <a class="btn btn-link" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/user/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../contact.php' : 'contact.php'; ?>">Contact</a>
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <a class="btn btn-link" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? 'dashboard.php' : 'admin/dashboard.php'; ?>">Admin Panel</a>
                    <?php else: ?>
                        <a class="btn btn-link" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/user/') !== false) ? 'dashboard.php' : 'user/dashboard.php'; ?>">Dashboard</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a class="btn btn-link" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/user/') !== false) ? 'login.php' : 'user/login.php'; ?>">Login</a>
                    <a class="btn btn-link" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/user/') !== false) ? 'register.php' : 'user/register.php'; ?>">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="copyright">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    &copy; <a href="#">Tirta Sanita Outbound</a>, All Right Reserved.
                </div>
                <div class="col-md-6 text-center text-md-end">
                    Designed By <a href="#">Tirta Sanita Outbound</a>
                </div>
            </div>
        </div>
    </div>
</div>
