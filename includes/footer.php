<div class="container-fluid footer bg-dark text-light footer mt-5 pt-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-4 col-md-6">
                <h4 class="text-light mb-4">Alamat</h4>
                <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>Jl. Parung Panjang - Ciseeng, Ciseeng, Bogor, Jawa Barat</p>
                <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>0858-8686-3808</p>
                <p class="mb-2"><i class="fa fa-envelope me-3"></i>@tamankopsesciseeng</p>
                <div class="d-flex pt-2">
                    <a class="btn btn-outline-light btn-social" href="https://www.facebook.com/tamankopsesciseeng" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-outline-light btn-social" href="https://www.instagram.com/tamankopsesciseeng" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a class="btn btn-outline-light btn-social" href="https://api.whatsapp.com/send?phone=6285886863808" target="_blank"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <h4 class="text-light mb-4">Jam Operasional</h4>
                <p class="mb-1">Senin - Jumat</p>
                <h6 class="text-light">08:00 - 17:00</h6>
                <p class="mb-1">Sabtu - Minggu</p>
                <h6 class="text-light">08:00 - 18:00</h6>
                <p class="mb-1">Hari Libur Nasional</p>
                <h6 class="text-light">08:00 - 18:00</h6>
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
                    &copy; <a href="#">Taman Kopses Ciseeng</a>, All Right Reserved.
                </div>
                <div class="col-md-6 text-center text-md-end">
                    Designed By <a href="#">Taman Kopses Ciseeng</a>
                </div>
            </div>
        </div>
    </div>
</div>
