<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || (!isCashier() && !isAdmin())) {
    setFlashMessage('message', 'Anda tidak memiliki akses ke halaman ini.', 'alert alert-danger');
    redirect("index.php");
}

$database = new Database();
$db = $database->getConnection();

// Get all users
$sql = "SELECT * FROM users ORDER BY name";
$stmt = $db->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Kelola Pengguna - Tirta Sanita Outbound</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="../img/favicon.ico" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Quicksand:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --primary-color: #4dc387; --primary-dark: #3da876; --primary-light: #e8f5f0; }
        body { font-family: 'Open Sans', sans-serif; background-color: #f8f9fa; min-height: 100vh; display: flex; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Quicksand', sans-serif; font-weight: 700; }
        .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
        .btn-primary:hover { background-color: var(--primary-dark); border-color: var(--primary-dark); }
        .sidebar { width: 250px; position: fixed; top: 0; left: 0; height: 100vh; z-index: 999; background-color: white; box-shadow: 0 0 15px rgba(0,0,0,0.05); }
        .sidebar .sidebar-header { padding: 20px; background-color: var(--primary-color); color: white; }
        .sidebar .sidebar-menu { padding: 20px 0; }
        .sidebar .sidebar-menu .nav-link { padding: 12px 20px; color: #2c3e50; border-left: 4px solid transparent; }
        .sidebar .sidebar-menu .nav-link:hover, .sidebar .sidebar-menu .nav-link.active { background-color: var(--primary-light); border-left-color: var(--primary-color); color: var(--primary-color); }
        .sidebar .sidebar-menu .nav-link i { margin-right: 10px; width: 20px; }
        .main-content { margin-left: 250px; padding: 20px; flex: 1; }
        .card { border: none; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.05); }
        .card-header { background-color: white; border-bottom: 1px solid rgba(0,0,0,0.05); padding: 15px 20px; }
        .card-body { padding: 20px; }
        .navbar { background-color: white; box-shadow: 0 0 15px rgba(0,0,0,0.05); }
        .navbar-brand { font-family: 'Quicksand', sans-serif; font-weight: 700; }
        .badge-readonly { display: inline-block; padding: 4px 8px; background-color: #e2e3e5; color: #6c757d; border-radius: 4px; font-size: 12px; }
        @media (max-width: 991.98px) {
            .sidebar { margin-left: -250px; }
            .sidebar.active { margin-left: 0; }
            .main-content { margin-left: 0; }
            .main-content.active { margin-left: 250px; }
        }
    </style>
</head>
<body>
    <?php require_once 'sidebar-helper.php'; ?>
    <?php echo generateSidebar(basename($_SERVER['PHP_SELF'])); ?>

    <div class="main-content">
        <nav class="navbar navbar-expand-lg navbar-light sticky-top mb-4">
            <div class="container-fluid">
                <button class="btn btn-sm btn-primary me-2" id="sidebar-toggle"><i class="fas fa-bars"></i></button>
                <a class="navbar-brand d-none d-lg-block" href="cashier-dashboard.php"><span>Tirta Sanita Outbound</span></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i> <?php echo $_SESSION['user_name']; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <h1 class="mb-0">Kelola Pengguna <span class="badge-readonly">Lihat Saja</span></h1>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header"><h5 class="mb-0">Daftar Pengguna</h5></div>
                        <div class="card-body">
                            <?php if (empty($users)): ?>
                                <div class="text-center py-3">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <p class="mb-0">Belum ada pengguna</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nama</th>
                                                <th>WhatsApp</th>
                                                <th>Role</th>
                                                <th>Status</th>
                                                <th>Terdaftar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($users as $user): ?>
                                                <tr>
                                                    <td><?php echo $user['id']; ?></td>
                                                    <td><?php echo $user['name']; ?></td>
                                                    <td><?php echo $user['whatsapp']; ?></td>
                                                    <td>
                                                        <?php 
                                                        if ($user['role'] === 'admin') {
                                                            echo '<span class="badge bg-danger">Admin</span>';
                                                        } elseif ($user['role'] === 'cashier') {
                                                            echo '<span class="badge bg-warning">Kasir</span>';
                                                        } elseif ($user['role'] === 'staff') {
                                                            echo '<span class="badge bg-info">Staff</span>';
                                                        } else {
                                                            echo '<span class="badge bg-secondary">User</span>';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $user['is_active'] ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Nonaktif</span>'; ?>
                                                    </td>
                                                    <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#sidebar-toggle').click(function() {
                $('.sidebar').toggleClass('active');
                $('.main-content').toggleClass('active');
            });
        });
    </script>
</body>
</html>
