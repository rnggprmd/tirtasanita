<?php
/**
 * Helper function to generate the sidebar with the correct active states
 * 
 * @param string $current_page The current page filename
 * @return string The HTML for the sidebar
 */
function generateSidebar($current_page) {
    // Determine which section is active
    $dashboard_active = ($current_page == 'dashboard.php') ? 'active' : '';
    
    // Reservasi section
    $reservasi_pages = ['reservations.php', 'reservation-detail.php', 'payments.php', 'payment-detail.php', 'send-ticket.php', 'print-ticket.php'];
    $reservasi_active = in_array($current_page, $reservasi_pages) ? 'active' : '';
    $reservasi_expanded = in_array($current_page, $reservasi_pages) ? 'true' : 'false';
    $reservasi_show = in_array($current_page, $reservasi_pages) ? 'show' : '';
    
    $reservations_active = (in_array($current_page, ['reservations.php', 'reservation-detail.php', 'send-ticket.php', 'print-ticket.php'])) ? 'active' : '';
    $payments_active = (in_array($current_page, ['payments.php', 'payment-detail.php'])) ? 'active' : '';
    
    // Paket section
    $paket_pages = ['packages.php', 'package-add.php', 'package-edit.php', 'package-categories.php', 'package-facilities.php', 'facilities.php'];
    $paket_active = in_array($current_page, $paket_pages) ? 'active' : '';
    $paket_expanded = in_array($current_page, $paket_pages) ? 'true' : 'false';
    $paket_show = in_array($current_page, $paket_pages) ? 'show' : '';
    
    $packages_active = (in_array($current_page, ['packages.php', 'package-add.php', 'package-edit.php'])) ? 'active' : '';
    $package_categories_active = ($current_page == 'package-categories.php') ? 'active' : '';
    $facilities_active = ($current_page == 'facilities.php') ? 'active' : '';
    
    // Users section
    $users_pages = ['users.php', 'user-edit.php', 'user-reservations.php'];
    $users_active = in_array($current_page, $users_pages) ? 'active' : '';
    
    // Settings
    $settings_active = ($current_page == 'settings.php') ? 'active' : '';
    
    // Generate the sidebar HTML
    $html = <<<HTML
    <!-- Sidebar Start -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3 class="mb-0">Admin Panel</h3>
            <p class="mb-0">Taman Kopses Ciseeng</p>
        </div>
        <div class="sidebar-menu">
            <ul class="nav flex-column">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link {$dashboard_active}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                
                <!-- Reservasi & Pembayaran Accordion -->
                <li class="nav-item">
                    <a class="nav-link {$reservasi_active}" data-bs-toggle="collapse" href="#reservasiMenu" role="button" aria-expanded="{$reservasi_expanded}" aria-controls="reservasiMenu">
                        <i class="fas fa-calendar-check"></i> Reservasi
                        <i class="fas fa-chevron-down float-end"></i>
                    </a>
                    <div class="collapse {$reservasi_show}" id="reservasiMenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a href="reservations.php" class="nav-link {$reservations_active}">
                                    <i class="fas fa-calendar-alt"></i> Daftar Reservasi
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="payments.php" class="nav-link {$payments_active}">
                                    <i class="fas fa-money-bill-wave"></i> Pembayaran
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                
                <!-- Paket & Fasilitas Accordion -->
                <li class="nav-item">
                    <a class="nav-link {$paket_active}" data-bs-toggle="collapse" href="#paketMenu" role="button" aria-expanded="{$paket_expanded}" aria-controls="paketMenu">
                        <i class="fas fa-box"></i> Paket & Fasilitas
                        <i class="fas fa-chevron-down float-end"></i>
                    </a>
                    <div class="collapse {$paket_show}" id="paketMenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a href="packages.php" class="nav-link {$packages_active}">
                                    <i class="fas fa-box"></i> Daftar Paket
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="package-categories.php" class="nav-link {$package_categories_active}">
                                    <i class="fas fa-tags"></i> Kategori Paket
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="facilities.php" class="nav-link {$facilities_active}">
                                    <i class="fas fa-swimming-pool"></i> Fasilitas
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                
                <!-- Pengguna -->
                <li class="nav-item">
                    <a href="users.php" class="nav-link {$users_active}">
                        <i class="fas fa-users"></i> Pengguna
                    </a>
                </li>
                
                <!-- Pengaturan -->
                <li class="nav-item">
                    <a href="settings.php" class="nav-link {$settings_active}">
                        <i class="fas fa-cog"></i> Pengaturan
                    </a>
                </li>
                
                <!-- Logout -->
                <li class="nav-item mt-3">
                    <a href="logout.php" class="nav-link text-danger">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <!-- Sidebar End -->
HTML;

    return $html;
}
?>
