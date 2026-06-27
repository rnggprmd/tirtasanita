<?php
/**
 * Helper function to generate the sidebar with the correct active states
 * 
 * @param string $current_page The current page filename
 * @return string The HTML for the sidebar
 */
function generateSidebar($current_page) {
    // Determine user role
    $is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    $is_cashier = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'cashier';
    
    // Determine which section is active
    $dashboard_active = (in_array($current_page, ['dashboard.php', 'cashier-dashboard.php'])) ? 'active' : '';
    
    // Reservasi section
    $reservasi_pages = ['reservations.php', 'reservation-detail.php', 'payments.php', 'payment-detail.php', 'send-ticket.php', 'print-ticket.php', 'cashier-reservations.php', 'cashier-payments.php', 'cashier-ticket-detail.php'];
    $reservasi_active = in_array($current_page, $reservasi_pages) ? 'active' : '';
    $reservasi_expanded = in_array($current_page, $reservasi_pages) ? 'true' : 'false';
    $reservasi_show = in_array($current_page, $reservasi_pages) ? 'show' : '';
    
    $reservations_active = (in_array($current_page, ['reservations.php', 'reservation-detail.php', 'send-ticket.php', 'print-ticket.php', 'cashier-reservations.php', 'cashier-ticket-detail.php'])) ? 'active' : '';
    $payments_active = (in_array($current_page, ['payments.php', 'payment-detail.php', 'cashier-payments.php'])) ? 'active' : '';
    $ticket_sales_active = (in_array($current_page, ['cashier-ticket-sales.php'])) ? 'active' : '';
    
    // Paket section (Admin only)
    $paket_pages = ['packages.php', 'package-add.php', 'package-edit.php', 'package-categories.php', 'package-facilities.php', 'facilities.php'];
    $paket_active = in_array($current_page, $paket_pages) ? 'active' : '';
    $paket_expanded = in_array($current_page, $paket_pages) ? 'true' : 'false';
    $paket_show = in_array($current_page, $paket_pages) ? 'show' : '';
    
    $packages_active = (in_array($current_page, ['packages.php', 'package-add.php', 'package-edit.php'])) ? 'active' : '';
    $package_categories_active = (in_array($current_page, ['package-categories.php'])) ? 'active' : '';
    $facilities_active = (in_array($current_page, ['facilities.php'])) ? 'active' : '';
    
    // Users section (Admin & Cashier)
    $users_pages = ['users.php', 'user-edit.php', 'user-reservations.php', 'cashier-users.php', 'cashier-user-edit.php'];
    $users_active = in_array($current_page, $users_pages) ? 'active' : '';
    
    // Settings (Admin & Cashier)
    $settings_active = (in_array($current_page, ['settings.php', 'cashier-settings.php'])) ? 'active' : '';
    
    // Get role name
    $role_text = $is_admin ? 'Administrator' : 'Kasir';
    
    // Start building HTML
    $html = '<!-- Sidebar Start -->' . "\n";
    $html .= '<div class="sidebar">' . "\n";
    $dashboard_link = $is_cashier ? 'cashier-dashboard.php' : 'dashboard.php';
    $html .= '    <div class="sidebar-header text-center">' . "\n";
    $html .= '        <a href="' . $dashboard_link . '" class="d-block text-white text-decoration-none">' . "\n";
    $html .= '            <div class="d-inline-block bg-white rounded-circle p-2 mb-2 shadow-sm" style="width: 85px; height: 85px; display: inline-flex; align-items: center; justify-content: center;">' . "\n";
    $html .= '                <img src="../img/logo.png" alt="Tirta Sanita Logo" style="max-width: 100%; max-height: 100%; object-fit: contain;">' . "\n";
    $html .= '            </div>' . "\n";
    $html .= '            <h3 class="mb-0 fs-5 fw-bold text-white" style="color: #ffffff !important;">Admin Panel</h3>' . "\n";
    $html .= '        </a>' . "\n";
    $html .= '        <p class="mb-0 small text-white-50" style="color: rgba(255, 255, 255, 0.85) !important;">' . $role_text . '</p>' . "\n";
    $html .= '    </div>' . "\n";
    $html .= '    <div class="sidebar-menu">' . "\n";
    $html .= '        <ul class="nav flex-column">' . "\n";
    
    // Dashboard
    $dashboard_link = $is_cashier ? 'cashier-dashboard.php' : 'dashboard.php';
    $html .= '            <li class="nav-item">' . "\n";
    $html .= '                <a href="' . $dashboard_link . '" class="nav-link ' . $dashboard_active . '">' . "\n";
    $html .= '                    <i class="fas fa-tachometer-alt"></i> Dashboard' . "\n";
    $html .= '                </a>' . "\n";
    $html .= '            </li>' . "\n";
    
    // Reservasi & Pembayaran
    $html .= '            <li class="nav-item">' . "\n";
    $html .= '                <a class="nav-link ' . $reservasi_active . '" data-bs-toggle="collapse" href="#reservasiMenu" role="button" aria-expanded="' . $reservasi_expanded . '" aria-controls="reservasiMenu">' . "\n";
    $html .= '                    <i class="fas fa-calendar-check"></i> Reservasi' . "\n";
    $html .= '                    <i class="fas fa-chevron-down float-end"></i>' . "\n";
    $html .= '                </a>' . "\n";
    $html .= '                <div class="collapse ' . $reservasi_show . '" id="reservasiMenu">' . "\n";
    $html .= '                    <ul class="nav flex-column ms-3">' . "\n";
    
    if ($is_admin) {
        $html .= '                        <li class="nav-item">' . "\n";
        $html .= '                            <a href="reservations.php" class="nav-link ' . $reservations_active . '">' . "\n";
        $html .= '                                <i class="fas fa-calendar-alt"></i> Daftar Reservasi' . "\n";
        $html .= '                            </a>' . "\n";
        $html .= '                        </li>' . "\n";
        $html .= '                        <li class="nav-item">' . "\n";
        $html .= '                            <a href="payments.php" class="nav-link ' . $payments_active . '">' . "\n";
        $html .= '                                <i class="fas fa-money-bill-wave"></i> Pembayaran' . "\n";
        $html .= '                            </a>' . "\n";
        $html .= '                        </li>' . "\n";
    } elseif ($is_cashier) {
        $html .= '                        <li class="nav-item">' . "\n";
        $html .= '                            <a href="cashier-reservations.php" class="nav-link ' . $reservations_active . '">' . "\n";
        $html .= '                                <i class="fas fa-calendar-alt"></i> Kelola Reservasi' . "\n";
        $html .= '                            </a>' . "\n";
        $html .= '                        </li>' . "\n";
        $html .= '                        <li class="nav-item">' . "\n";
        $html .= '                            <a href="cashier-payments.php" class="nav-link ' . $payments_active . '">' . "\n";
        $html .= '                                <i class="fas fa-credit-card"></i> Terima Pembayaran' . "\n";
        $html .= '                            </a>' . "\n";
        $html .= '                        </li>' . "\n";
    }
    
    $html .= '                    </ul>' . "\n";
    $html .= '                </div>' . "\n";
    $html .= '            </li>' . "\n";
    
    // Penjualan Tiket (Cashier only - TERPISAH)
    if ($is_cashier) {
        $html .= '            <li class="nav-item">' . "\n";
        $html .= '                <a href="cashier-ticket-sales.php" class="nav-link ' . $ticket_sales_active . '">' . "\n";
        $html .= '                    <i class="fas fa-ticket-alt"></i> Penjualan Tiket' . "\n";
        $html .= '                </a>' . "\n";
        $html .= '            </li>' . "\n";
    }
    
    // Paket & Fasilitas (Admin only)
    if ($is_admin) {
        $paket_link_prefix = 'packages.php';
        $package_categories_link = 'package-categories.php';
        $facilities_link = 'facilities.php';
        
        $html .= '            <li class="nav-item">' . "\n";
        $html .= '                <a class="nav-link ' . $paket_active . '" data-bs-toggle="collapse" href="#paketMenu" role="button" aria-expanded="' . $paket_expanded . '" aria-controls="paketMenu">' . "\n";
        $html .= '                    <i class="fas fa-box"></i> Paket & Fasilitas' . "\n";
        $html .= '                    <i class="fas fa-chevron-down float-end"></i>' . "\n";
        $html .= '                </a>' . "\n";
        $html .= '                <div class="collapse ' . $paket_show . '" id="paketMenu">' . "\n";
        $html .= '                    <ul class="nav flex-column ms-3">' . "\n";
        $html .= '                        <li class="nav-item">' . "\n";
        $html .= '                            <a href="' . $paket_link_prefix . '" class="nav-link ' . $packages_active . '">' . "\n";
        $html .= '                                <i class="fas fa-box"></i> Daftar Paket' . "\n";
        $html .= '                            </a>' . "\n";
        $html .= '                        </li>' . "\n";
        $html .= '                        <li class="nav-item">' . "\n";
        $html .= '                            <a href="' . $package_categories_link . '" class="nav-link ' . $package_categories_active . '">' . "\n";
        $html .= '                                <i class="fas fa-tags"></i> Kategori Paket' . "\n";
        $html .= '                            </a>' . "\n";
        $html .= '                        </li>' . "\n";
        $html .= '                        <li class="nav-item">' . "\n";
        $html .= '                            <a href="' . $facilities_link . '" class="nav-link ' . $facilities_active . '">' . "\n";
        $html .= '                                <i class="fas fa-swimming-pool"></i> Fasilitas' . "\n";
        $html .= '                            </a>' . "\n";
        $html .= '                        </li>' . "\n";
        $html .= '                    </ul>' . "\n";
        $html .= '                </div>' . "\n";
        $html .= '            </li>' . "\n";
    } elseif ($is_cashier) {
        $html .= '            <li class="nav-item">' . "\n";
        $html .= '                <a href="cashier-packages.php" class="nav-link ' . $packages_active . '">' . "\n";
        $html .= '                    <i class="fas fa-box"></i> Daftar Paket' . "\n";
        $html .= '                </a>' . "\n";
        $html .= '            </li>' . "\n";
    }
    
    // Pengguna (Admin only)
    if ($is_admin) {
        $html .= '            <li class="nav-item">' . "\n";
        $html .= '                <a href="users.php" class="nav-link ' . $users_active . '">' . "\n";
        $html .= '                    <i class="fas fa-users"></i> Pengguna' . "\n";
        $html .= '                </a>' . "\n";
        $html .= '            </li>' . "\n";
    }
    
    // Pengaturan (Admin only)
    if ($is_admin) {
        $html .= '            <li class="nav-item">' . "\n";
        $html .= '                <a href="settings.php" class="nav-link ' . $settings_active . '">' . "\n";
        $html .= '                    <i class="fas fa-cog"></i> Pengaturan' . "\n";
        $html .= '                </a>' . "\n";
        $html .= '            </li>' . "\n";
    }
    
    $html .= '        </ul>' . "\n";
    $html .= '    </div>' . "\n";
    $html .= '</div>' . "\n";
    $html .= '<!-- Sidebar End -->' . "\n";
    $html .= '<div class="sidebar-overlay"></div>' . "\n";
    $html .= '<script src="sidebar.js"></script>' . "\n";

    return $html;
}
?>
