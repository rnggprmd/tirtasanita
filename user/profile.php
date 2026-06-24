<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    setFlashMessage('message', 'Silakan login terlebih dahulu.', 'alert alert-danger');
    redirect("login.php");
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get user data from session
$user_id = $_SESSION['user_id'];

// Get user details
$sql = "SELECT * FROM users WHERE id = :id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id', $user_id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    setFlashMessage('message', 'Data pengguna tidak ditemukan.', 'alert alert-danger');
    redirect("dashboard.php");
}

$user = $stmt->fetch(PDO::FETCH_ASSOC);

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
        $check_stmt->bindParam(':id', $user_id);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            $error_message = 'Nomor WhatsApp sudah digunakan oleh pengguna lain.';
        } else if (!empty($email)) {
            // Check if email is already used by another user
            $check_sql = "SELECT id FROM users WHERE email = :email AND id != :id";
            $check_stmt = $db->prepare($check_sql);
            $check_stmt->bindParam(':email', $email);
            $check_stmt->bindParam(':id', $user_id);
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
                if (!password_verify($current_password, $user['password'])) {
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
                    $update_stmt->bindParam(':id', $user_id);
                    
                    if ($update_stmt->execute()) {
                        // Update session data
                        $_SESSION['user_name'] = $name;
                        $_SESSION['user_whatsapp'] = $whatsapp;
                        
                        $success_message = 'Profil berhasil diperbarui dengan password baru.';
                        
                        // Refresh user data
                        $stmt->execute();
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);
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
                $update_stmt->bindParam(':id', $user_id);
                
                if ($update_stmt->execute()) {
                    // Update session data
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_whatsapp'] = $whatsapp;
                    
                    $success_message = 'Profil berhasil diperbarui.';
                    
                    // Refresh user data
                    $stmt->execute();
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                } else {
                    $error_message = 'Terjadi kesalahan saat memperbarui profil.';
                }
            }
        }
    }
}

// Get user's recent reservations
$sql = "SELECT r.*, p.name as package_name, 
        CASE 
            WHEN r.status = 'pending' THEN 'Menunggu Pembayaran'
            WHEN r.status = 'confirmed' THEN 'Dikonfirmasi'
            WHEN r.status = 'completed' THEN 'Selesai'
            WHEN r.status = 'cancelled' THEN 'Dibatalkan'
            ELSE r.status
        END as status_text,
        CASE 
            WHEN r.status = 'pending' THEN 'warning'
            WHEN r.status = 'confirmed' THEN 'success'
            WHEN r.status = 'completed' THEN 'info'
            WHEN r.status = 'cancelled' THEN 'danger'
            ELSE 'secondary'
        END as status_class
        FROM reservations r 
        JOIN packages p ON r.package_id = p.id 
        WHERE r.user_id = :user_id 
        ORDER BY r.created_at DESC 
        LIMIT 5";
$stmt = $db->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$recent_reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <title>Profil Saya - Taman Kopses Ciseeng</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="Taman Kopses Ciseeng, Profil, Akun" name="keywords" />
    <meta content="Kelola profil pengguna di Taman Kopses Ciseeng" name="description" />

    <!-- Favicon -->
    <link href="../img/favicon.ico" rel="icon" />

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Quicksand:wght@600;700&display=swap" rel="stylesheet" />

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- Libraries Stylesheet -->
    <link href="../lib/animate/animate.min.css" rel="stylesheet" />
    <link href="../lib/lightbox/css/lightbox.min.css" rel="stylesheet" />
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../css/bootstrap.min.css" rel="stylesheet" />

    <!-- Template Stylesheet -->
    <link href="../css/style.css" rel="stylesheet" />
    
    <style>
        .profile-header {
            padding: 1.5rem;
            border-radius: 0.5rem;
            background-color: #fff;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 2rem;
        }
        
        .profile-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        
        .bg-primary-light {
            background-color: rgba(77, 195, 135, 0.1);
        }
        
        .round {
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    <!-- Topbar Start -->
    <?php include_once '../includes/topbar.php'; ?>
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <?php include_once '../includes/navbar.php'; ?>
    <!-- Navbar End -->

    <!-- Page Header Start -->
    <div class="container-fluid wow bg-primary fadeIn" data-wow-delay="0.1s">
        <div class="container">
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a class="text-white" href="../index.php">Home</a></li>
                    <li class="breadcrumb-item text-white active" aria-current="page">Profil Saya</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Profile Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <!-- Profile Header -->
            <div class="profile-header d-flex flex-column flex-md-row align-items-center mb-4">
                <div class="profile-img me-md-4 mb-3 mb-md-0">
                    <i class="fas fa-user"></i>
                </div>
                <div class="text-center text-md-start">
                    <h2 class="mb-1"><?php echo htmlspecialchars($user['name']); ?></h2>
                    <p class="mb-0"><i class="fas fa-user me-2"></i>Pengguna</p>
                    <p class="mb-0"><i class="fas fa-calendar-check me-2"></i>Bergabung: <?php echo date('d M Y', strtotime($user['created_at'])); ?></p>
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
            
            <?php displayFlashMessage(); ?>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="bg-white shadow-sm round p-4 p-lg-5">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="fw-bold mb-0"><i class="fas fa-user-edit me-2 text-primary"></i>Edit Profil</h3>
                        </div>
                        
                        <form action="" method="POST">
                            <div class="mb-4">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white round-5 border-end-0"><i class="fas fa-user text-primary"></i></span>
                                    <input type="text" class="form-control round-5 border-start-0 ps-0" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white round-5 border-end-0"><i class="fas fa-envelope text-primary"></i></span>
                                    <input type="email" class="form-control round-5 border-start-0 ps-0" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                                </div>
                                <div class="form-text small">Opsional, namun direkomendasikan untuk pemulihan akun</div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="whatsapp" class="form-label">Nomor WhatsApp</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white round-5 border-end-0"><i class="fab fa-whatsapp text-primary"></i></span>
                                    <input type="text" class="form-control round-5 border-start-0 ps-0" id="whatsapp" name="whatsapp" value="<?php echo htmlspecialchars($user['whatsapp']); ?>" required>
                                </div>
                                <div class="form-text small">Format: 08xxxxxxxxxx atau +628xxxxxxxxxx</div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <h4 class="fw-bold mb-3">Ubah Password</h4>
                            <p class="text-muted mb-3">Kosongkan bagian ini jika Anda tidak ingin mengubah password</p>
                            
                            <div class="mb-4">
                                <label for="current_password" class="form-label">Password Saat Ini</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white round-5 border-end-0"><i class="fas fa-lock text-primary"></i></span>
                                    <input type="password" class="form-control round-5 border-start-0 ps-0" id="current_password" name="current_password">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="new_password" class="form-label">Password Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white round-5 border-end-0"><i class="fas fa-key text-primary"></i></span>
                                    <input type="password" class="form-control round-5 border-start-0 ps-0" id="new_password" name="new_password">
                                </div>
                                <div class="form-text small">Minimal 6 karakter</div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white round-5 border-end-0"><i class="fas fa-check-double text-primary"></i></span>
                                    <input type="password" class="form-control round-5 border-start-0 ps-0" id="confirm_password" name="confirm_password">
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <button type="reset" class="btn btn-secondary rounded-pill me-md-2">
                                    <i class="fas fa-undo me-1"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary rounded-pill">
                                    <i class="fas fa-save me-1"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="bg-white shadow-sm round p-4 mb-4">
                        <h3 class="fw-bold mb-3"><i class="fas fa-shield-alt me-2 text-primary"></i>Keamanan Akun</h3>
                        
                        <div class="mb-3">
                            <h6 class="mb-2"><i class="fas fa-user-lock me-2 text-primary"></i>Status Akun</h6>
                            <p class="mb-0">Aktif</p>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="mb-2"><i class="fas fa-key me-2 text-primary"></i>Password</h6>
                            <p class="mb-0">Terakhir diubah: 
                                <?php 
                                echo !empty($user['updated_at']) && $user['updated_at'] != $user['created_at'] 
                                    ? date('d M Y', strtotime($user['updated_at'])) 
                                    : 'Belum pernah diubah'; 
                                ?>
                            </p>
                        </div>
                        
                        <div class="alert alert-info mt-4 mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Tips Keamanan:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Gunakan password yang kuat dengan kombinasi huruf, angka, dan simbol</li>
                                <li>Ubah password secara berkala</li>
                                <li>Jangan bagikan informasi login Anda dengan orang lain</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="bg-white shadow-sm round p-4">
                        <h3 class="fw-bold mb-3"><i class="fas fa-ticket-alt me-2 text-primary"></i>Reservasi Terbaru</h3>
                        
                        <?php if (count($recent_reservations) > 0): ?>
                            <div class="list-group">
                                <?php foreach ($recent_reservations as $reservation): ?>
                                    <a href="reservation-detail.php?id=<?php echo $reservation['id']; ?>" class="list-group-item list-group-item-action mb-2 round">
                                        <div class="d-flex w-100 justify-content-between align-items-center">
                                            <h6 class="mb-1">#<?php echo $reservation['id']; ?> - <?php echo $reservation['package_name']; ?></h6>
                                            <span class="badge bg-<?php echo $reservation['status_class']; ?>"><?php echo $reservation['status_text']; ?></span>
                                        </div>
                                        <p class="mb-1">Tanggal Kunjungan: <?php echo date('d M Y', strtotime($reservation['visit_date'])); ?></p>
                                        <small>Rp <?php echo number_format($reservation['total_price'], 0, ',', '.'); ?> - <?php echo $reservation['num_visitors']; ?> orang</small>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                            <div class="text-center mt-3">
                                <a href="my-tickets.php" class="btn btn-outline-primary btn-sm rounded-pill">
                                    <i class="fas fa-list me-1"></i> Lihat Semua Reservasi
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> Anda belum memiliki reservasi.
                                <div class="mt-2">
                                    <a href="reservation.php" class="btn btn-primary btn-sm rounded-pill">
                                        <i class="fas fa-plus me-1"></i> Buat Reservasi Baru
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Profile End -->

    <!-- Footer Start -->
    <?php include_once '../includes/footer.php'; ?>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/wow/wow.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/counterup/counterup.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="../lib/lightbox/js/lightbox.min.js"></script>

    <!-- Template Javascript -->
    <script src="../js/main.js"></script>
    
    <!-- Password validation -->
    <script>
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
