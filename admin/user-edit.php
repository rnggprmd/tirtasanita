<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Set page title
$page_title = 'Edit Pengguna';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Check if user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setFlashMessage('message', 'ID pengguna tidak valid.', 'alert alert-danger');
    redirect("users.php");
}

$user_id = $_GET['id'];

// Get user details
$sql = "SELECT * FROM users WHERE id = :id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id', $user_id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    setFlashMessage('message', 'Pengguna tidak ditemukan.', 'alert alert-danger');
    redirect("users.php");
}

$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $whatsapp = trim($_POST['whatsapp'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'user';
    $new_password = trim($_POST['new_password'] ?? '');
    
    // Validate input
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Nama tidak boleh kosong.';
    }
    
    if (empty($whatsapp)) {
        $errors[] = 'Nomor WhatsApp tidak boleh kosong.';
    } elseif (!preg_match('/^[0-9\+\-\(\)\s]{10,15}$/', $whatsapp)) {
        $errors[] = 'Format nomor WhatsApp tidak valid.';
    }
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }
    
    // Check if WhatsApp is already used by another user
    $stmt = $db->prepare("SELECT id FROM users WHERE whatsapp = :whatsapp AND id != :id");
    $stmt->bindParam(':whatsapp', $whatsapp);
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $errors[] = 'Nomor WhatsApp sudah digunakan oleh pengguna lain.';
    }
    
    // Check if email is already used by another user
    if (!empty($email)) {
        $stmt = $db->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $errors[] = 'Email sudah digunakan oleh pengguna lain.';
        }
    }
    
    if (empty($errors)) {
        try {
            // Update user
            if (!empty($new_password)) {
                // Update with new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE users SET name = :name, whatsapp = :whatsapp, email = :email, role = :role, password = :password, updated_at = NOW() WHERE id = :id");
                $stmt->bindParam(':password', $hashed_password);
            } else {
                // Update without changing password
                $stmt = $db->prepare("UPDATE users SET name = :name, whatsapp = :whatsapp, email = :email, role = :role, updated_at = NOW() WHERE id = :id");
            }
            
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':whatsapp', $whatsapp);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':id', $user_id);
            $stmt->execute();
            
            setFlashMessage('message', 'Data pengguna berhasil diperbarui.', 'alert alert-success');
            redirect("users.php");
        } catch (PDOException $e) {
            $error_message = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Kelola Paket - Taman Kopses Ciseeng</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Taman Kopses Ciseeng, Admin, Paket" name="keywords">
    <meta content="Admin panel untuk mengelola paket di Taman Kopses Ciseeng" name="description">

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

        .navbar-brand {
            font-family: 'Quicksand', sans-serif;
            font-weight: 700;
        }

        .navbar-brand img {
            width: 30px;
            margin-right: 10px;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        .dropdown-item:hover {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        .dropdown-item.active {
            background-color: var(--primary-color);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .facility-check {
            margin-bottom: 10px;
        }

        .facility-check .form-check-label {
            display: flex;
            align-items: center;
        }

        .facility-check .facility-icon {
            margin-right: 10px;
            width: 24px;
            text-align: center;
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
            .main-content.active {
                margin-left: 250px;
            }
        }
    </style>
</head>

<?php require_once 'sidebar-helper.php'; ?>
<?php echo generateSidebar(basename($_SERVER['PHP_SELF'])); ?>

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
                    <h1 class="mb-0">Edit Pengguna</h1>
                    <div>
                        <a href="users.php" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                        <a href="user-reservations.php?id=<?php echo $user_id; ?>" class="btn btn-info">
                            <i class="fas fa-calendar-alt me-1"></i> Lihat Reservasi
                        </a>
                    </div>
                </div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Informasi Pengguna</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="whatsapp" class="form-label">WhatsApp <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="<?php echo htmlspecialchars($user['whatsapp']); ?>" required>
                                    <small class="text-muted">Format: 08xxxxxxxxxx</small>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="role" class="form-label">Peran <span class="text-danger">*</span></label>
                                    <select class="form-select" id="role" name="role" required>
                                        <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>Pengguna</option>
                                        <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="new_password" class="form-label">Password Baru</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password">
                                    <small class="text-muted">Biarkan kosong jika tidak ingin mengubah password</small>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="users.php" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i> Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Template Javascript -->
    <script>
        $(document).ready(function() {
            // Toggle sidebar
            $('#sidebar-toggle').click(function() {
                $('.sidebar').toggleClass('active');
                $('.main-content').toggleClass('active');
            });

            // Auto-hide sidebar on mobile
            $(window).resize(function() {
                if ($(window).width() < 992) {
                    $('.sidebar').removeClass('active');
                    $('.main-content').removeClass('active');
                } else {
                    $('.sidebar').addClass('active');
                    $('.main-content').addClass('active');
                }
            }).trigger('resize');
        });
    </script>
</body>

</html>

