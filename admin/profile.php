<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in as admin
if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('message', 'Anda tidak memiliki akses ke halaman ini.', 'alert alert-danger');
    redirect("index.php");
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get admin data from session
$admin_id = $_SESSION['user_id'];

// Get admin details
$sql = "SELECT * FROM users WHERE id = :id AND role = 'admin'";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id', $admin_id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    setFlashMessage('message', 'Data admin tidak ditemukan.', 'alert alert-danger');
    redirect("dashboard.php");
}

$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $whatsapp = trim($_POST['whatsapp']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate inputs
    if (empty($name)) {
        $error_message = 'Nama tidak boleh kosong.';
    } elseif (empty($whatsapp)) {
        $error_message = 'Nomor WhatsApp tidak boleh kosong.';
    } else {
        // Check if WhatsApp number is already used by another user
        $check_sql = "SELECT id FROM users WHERE whatsapp = :whatsapp AND id != :id";
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bindParam(':whatsapp', $whatsapp);
        $check_stmt->bindParam(':id', $admin_id);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            $error_message = 'Nomor WhatsApp sudah digunakan oleh pengguna lain.';
        } else if (!empty($email)) {
            // Check if email is already used by another user
            $check_sql = "SELECT id FROM users WHERE email = :email AND id != :id";
            $check_stmt = $db->prepare($check_sql);
            $check_stmt->bindParam(':email', $email);
            $check_stmt->bindParam(':id', $admin_id);
            $check_stmt->execute();
            
            if ($check_stmt->rowCount() > 0) {
                $error_message = 'Email sudah digunakan oleh pengguna lain.';
            }
        }
        
        // If no errors, update profile
        if (empty($error_message)) {
            // If user wants to change password
            if (!empty($current_password) && !empty($new_password)) {
                // Verify current password
                if (!password_verify($current_password, $admin['password'])) {
                    $error_message = 'Password saat ini tidak sesuai.';
                } elseif ($new_password != $confirm_password) {
                    $error_message = 'Password baru dan konfirmasi password tidak sama.';
                } elseif (strlen($new_password) < 6) {
                    $error_message = 'Password baru harus minimal 6 karakter.';
                } else {
                    // Hash new password
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    
                    // Update profile with new password
                    $update_sql = "UPDATE users SET name = :name, email = :email, whatsapp = :whatsapp, password = :password, updated_at = NOW() WHERE id = :id";
                    $update_stmt = $db->prepare($update_sql);
                    $update_stmt->bindParam(':name', $name);
                    $update_stmt->bindParam(':email', $email);
                    $update_stmt->bindParam(':whatsapp', $whatsapp);
                    $update_stmt->bindParam(':password', $hashed_password);
                    $update_stmt->bindParam(':id', $admin_id);
                    
                    if ($update_stmt->execute()) {
                        // Update session data
                        $_SESSION['user_name'] = $name;
                        
                        $success_message = 'Profil berhasil diperbarui dengan password baru.';
                        
                        // Refresh admin data
                        $stmt->execute();
                        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                    } else {
                        $error_message = 'Terjadi kesalahan saat memperbarui profil.';
                    }
                }
            } else {
                // Update profile without changing password
                $update_sql = "UPDATE users SET name = :name, email = :email, whatsapp = :whatsapp, updated_at = NOW() WHERE id = :id";
                $update_stmt = $db->prepare($update_sql);
                $update_stmt->bindParam(':name', $name);
                $update_stmt->bindParam(':email', $email);
                $update_stmt->bindParam(':whatsapp', $whatsapp);
                $update_stmt->bindParam(':id', $admin_id);
                
                if ($update_stmt->execute()) {
                    // Update session data
                    $_SESSION['user_name'] = $name;
                    
                    $success_message = 'Profil berhasil diperbarui.';
                    
                    // Refresh admin data
                    $stmt->execute();
                    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                } else {
                    $error_message = 'Terjadi kesalahan saat memperbarui profil.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Profil Admin - Taman Kopses Ciseeng</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Taman Kopses Ciseeng, Admin, Profil" name="keywords">
    <meta content="Admin panel untuk mengelola profil admin di Taman Kopses Ciseeng" name="description">

    <!-- Favicon -->
    <link href="../img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Quicksand:wght@600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Admin Stylesheet -->
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

        body {
            font-family: 'Open Sans', sans-serif;
            background-color: var(--light-bg);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Quicksand', sans-serif;
            font-weight: 700;
        }

        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 999;
            background-color: var(--white);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }

        .sidebar .sidebar-header {
            padding: 20px;
            background-color: var(--primary-color);
            color: var(--white);
        }

        .sidebar .sidebar-menu {
            padding: 20px 0;
        }

        .sidebar .sidebar-menu .nav-link {
            padding: 12px 20px;
            color: var(--dark-text);
            border-left: 4px solid transparent;
            transition: all 0.3s;
        }

        .sidebar .sidebar-menu .nav-link:hover,
        .sidebar .sidebar-menu .nav-link.active {
            background-color: var(--primary-light);
            border-left-color: var(--primary-color);
            color: var(--primary-color);
        }

        .sidebar .sidebar-menu .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: var(--white);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 15px 20px;
        }

        .card-body {
            padding: 20px;
        }

        .navbar {
            background-color: var(--white);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        .profile-header {
            background-color: var(--primary-color);
            color: var(--white);
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .profile-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin-bottom: 15px;
            color: var(--primary-color);
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-text);
        }

        .input-group-text {
            background-color: var(--primary-light);
            color: var(--primary-color);
            border-color: #ced4da;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                margin-left: -250px;
            }
            
            .sidebar.active {
                margin-left: 0;
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <?php require_once 'sidebar-helper.php'; ?>
    <?php echo generateSidebar(basename($_SERVER['PHP_SELF'])); ?>

    <!-- Main Content Start -->
    <div class="main-content">
        <!-- Navbar Start -->
        <nav class="navbar navbar-expand-lg navbar-light sticky-top mb-4">
            <div class="container-fluid">
                <button class="btn btn-sm btn-primary me-2" id="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <a class="navbar-brand d-none d-lg-block" href="dashboard.php">
                    <span>Taman Kopses Ciseeng</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i> <?php echo $_SESSION['user_name']; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i> Profil</a></li>
                                <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i> Pengaturan</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Navbar End -->

        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <h1 class="mb-0">Profil Admin</h1>
                </div>
            </div>

            <!-- Profile Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="profile-header d-flex flex-column flex-md-row align-items-center">
                        <div class="profile-img me-md-4">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="text-center text-md-start">
                            <h2 class="mb-1"><?php echo htmlspecialchars($admin['name']); ?></h2>
                            <p class="mb-0"><i class="fas fa-user-shield me-2"></i>Administrator</p>
                            <p class="mb-0"><i class="fas fa-calendar-check me-2"></i>Bergabung: <?php echo date('d M Y', strtotime($admin['created_at'])); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Edit Profil</h5>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama Lengkap</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($admin['name']); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>">
                                    </div>
                                    <small class="text-muted">Opsional, namun direkomendasikan untuk pemulihan akun</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="whatsapp" class="form-label">Nomor WhatsApp</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
                                        <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="<?php echo htmlspecialchars($admin['whatsapp']); ?>" required>
                                    </div>
                                    <small class="text-muted">Format: 08xxxxxxxxxx atau +628xxxxxxxxxx</small>
                                </div>
                                
                                <hr class="my-4">
                                
                                <h5 class="mb-3">Ubah Password</h5>
                                <p class="text-muted mb-3">Kosongkan bagian ini jika Anda tidak ingin mengubah password</p>
                                
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Password Saat Ini</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" id="current_password" name="current_password">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Password Baru</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                        <input type="password" class="form-control" id="new_password" name="new_password">
                                    </div>
                                    <small class="text-muted">Minimal 6 karakter</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-check-double"></i></span>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                    <button type="reset" class="btn btn-secondary me-md-2">
                                        <i class="fas fa-undo me-1"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Keamanan Akun</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6><i class="fas fa-user-lock me-2 text-primary"></i>Status Akun</h6>
                                <p class="mb-0">Aktif</p>
                            </div>
                            
                            <div class="mb-3">
                                <h6><i class="fas fa-clock me-2 text-primary"></i>Login Terakhir</h6>
                                <p class="mb-0"><?php echo date('d M Y H:i'); ?></p>
                            </div>
                            
                            <div class="mb-3">
                                <h6><i class="fas fa-key me-2 text-primary"></i>Password</h6>
                                <p class="mb-0">Terakhir diubah: 
                                    <?php 
                                    echo !empty($admin['updated_at']) && $admin['updated_at'] != $admin['created_at'] 
                                        ? date('d M Y', strtotime($admin['updated_at'])) 
                                        : 'Belum pernah diubah'; 
                                    ?>
                                </p>
                            </div>
                            
                            <div class="alert alert-info mt-4">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Tips Keamanan:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Gunakan password yang kuat dengan kombinasi huruf, angka, dan simbol</li>
                                    <li>Ubah password secara berkala</li>
                                    <li>Jangan bagikan informasi login Anda dengan orang lain</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Main Content End -->

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Template Javascript -->
    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
        
        // Password validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const currentPassword = document.getElementById('current_password').value;
            
            // If user is trying to change password
            if (newPassword || confirmPassword || currentPassword) {
                if (!currentPassword) {
                    e.preventDefault();
                    alert('Masukkan password saat ini untuk mengubah password');
                } else if (!newPassword) {
                    e.preventDefault();
                    alert('Masukkan password baru');
                } else if (!confirmPassword) {
                    e.preventDefault();
                    alert('Masukkan konfirmasi password baru');
                } else if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert('Password baru dan konfirmasi password tidak sama');
                } else if (newPassword.length < 6) {
                    e.preventDefault();
                    alert('Password baru harus minimal 6 karakter');
                }
            }
        });
    </script>
</body>
</html>
