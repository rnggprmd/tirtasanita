<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Initialize variables
$name = $whatsapp = $email = $password = $confirm_password = '';
$name_err = $whatsapp_err = $password_err = $confirm_password_err = '';

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Silakan masukkan nama lengkap Anda.";
    } else {
        $name = sanitize($_POST["name"]);
    }
    
    // Validate whatsapp
    if (empty(trim($_POST["whatsapp"]))) {
        $whatsapp_err = "Silakan masukkan nomor WhatsApp Anda.";
    } else {
        // Prepare a select statement
        $database = new Database();
        $db = $database->getConnection();
        
        $sql = "SELECT id FROM users WHERE whatsapp = :whatsapp";
        
        if ($stmt = $db->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":whatsapp", $param_whatsapp, PDO::PARAM_STR);
            
            // Set parameters
            $param_whatsapp = trim($_POST["whatsapp"]);
            
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    $whatsapp_err = "Nomor WhatsApp ini sudah terdaftar.";
                } else {
                    $whatsapp = sanitize($_POST["whatsapp"]);
                }
            } else {
                echo "Oops! Terjadi kesalahan. Silakan coba lagi nanti.";
            }

            // Close statement
            unset($stmt);
        }
    }
    
    // Validate email (optional)
    if (!empty(trim($_POST["email"]))) {
        if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $email_err = "Format email tidak valid.";
        } else {
            $email = sanitize($_POST["email"]);
        }
    }
    
    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Silakan masukkan password.";     
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password harus memiliki minimal 6 karakter.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Silakan konfirmasi password.";     
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password tidak cocok.";
        }
    }
    
    // Check input errors before inserting in database
    if (empty($name_err) && empty($whatsapp_err) && empty($password_err) && empty($confirm_password_err)) {
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (name, whatsapp, email, password) VALUES (:name, :whatsapp, :email, :password)";
         
        if ($stmt = $db->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":name", $param_name, PDO::PARAM_STR);
            $stmt->bindParam(":whatsapp", $param_whatsapp, PDO::PARAM_STR);
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            
            // Set parameters
            $param_name = $name;
            $param_whatsapp = $whatsapp;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to login page
                setFlashMessage('message', 'Registrasi berhasil! Silakan login.', 'alert alert-success');
                redirect("login.php");
            } else {
                echo "Oops! Terjadi kesalahan. Silakan coba lagi nanti.";
            }

            // Close statement
            unset($stmt);
        }
    }
    
    // Close connection
    unset($db);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <title>Registrasi - Taman Kopses Ciseeng</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="Taman Kopses Ciseeng, Registrasi, Akun Baru" name="keywords" />
    <meta content="Daftar akun baru untuk memesan tiket di Taman Kopses Ciseeng" name="description" />

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
                    <li class="breadcrumb-item text-white active" aria-current="page">Registrasi</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Registration Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                    <p class="fs-5 fw-bold text-primary">Registrasi Akun</p>
                    <h1 class="display-5 mb-4">Daftar Akun Baru</h1>
                    <p class="mb-4">Silakan daftar untuk membuat akun baru di Taman Kopses Ciseeng. Dengan akun ini, Anda dapat memesan tiket untuk berbagai paket outbound dan camping yang kami sediakan.</p>
                    <div class="d-flex align-items-center gap-4 mb-4">
                        <div class="d-flex flex-shrink-0 align-items-center justify-content-center bg-primary rounded-circle" style="width: 50px; height: 50px">
                            <i class="fa fa-user-plus text-white"></i>
                        </div>
                        <div>
                            <h5>Daftar Akun</h5>
                            <p class="mb-0">Isi formulir dengan data diri Anda</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-4 mb-4">
                        <div class="d-flex flex-shrink-0 align-items-center justify-content-center bg-primary rounded-circle" style="width: 50px; height: 50px">
                            <i class="fa fa-ticket-alt text-white"></i>
                        </div>
                        <div>
                            <h5>Pesan Tiket</h5>
                            <p class="mb-0">Pilih paket dan tanggal kunjungan</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-4">
                        <div class="d-flex flex-shrink-0 align-items-center justify-content-center bg-primary rounded-circle" style="width: 50px; height: 50px">
                            <i class="fa fa-money-bill-wave text-white"></i>
                        </div>
                        <div>
                            <h5>Bayar & Terima E-Ticket</h5>
                            <p class="mb-0">Lakukan pembayaran dan dapatkan e-ticket</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="bg-white shadow-sm round p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold">Buat Akun Baru</h3>
                            <p class="text-muted">Untuk melakukan reservasi di Taman Kopses Ciseeng</p>
                        </div>
                        
                        <?php displayFlashMessage(); ?>
                        
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="mb-4">
                                <div class="input-group ">
                                    <span class="input-group-text bg-white round-5 border-end-0"><i class="fas fa-user text-primary"></i></span>
                                    <input type="text" class="form-control round-5 border-start-0 ps-0 <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo $name; ?>" placeholder="Nama Lengkap" required>
                                </div>
                                <?php if (!empty($name_err)): ?><div class="text-danger small mt-1"><?php echo $name_err; ?></div><?php endif; ?>
                            </div>
                            
                            <div class="mb-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-white round-5 border-end-0"><i class="fab fa-whatsapp text-primary"></i></span>
                                    <input type="text" class="form-control round-5 border-start-0 ps-0 <?php echo (!empty($whatsapp_err)) ? 'is-invalid' : ''; ?>" id="whatsapp" name="whatsapp" value="<?php echo $whatsapp; ?>" placeholder="Nomor WhatsApp" required>
                                </div>
                                <?php if (!empty($whatsapp_err)): ?><div class="text-danger small mt-1"><?php echo $whatsapp_err; ?></div><?php endif; ?>
                                <div class="form-text small">Format: 08xxxxxxxxxx</div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-white round-5 border-end-0"><i class="fas fa-envelope text-primary"></i></span>
                                    <input type="email" class="form-control round-5 border-start-0 ps-0 <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo $email; ?>" placeholder="Email (Opsional)">
                                </div>
                                <?php if (!empty($email_err)): ?><div class="text-danger small mt-1"><?php echo $email_err; ?></div><?php endif; ?>
                            </div>
                            
                            <div class="mb-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-white round-5 border-end-0"><i class="fas fa-lock text-primary"></i></span>
                                    <input type="password" class="form-control round-5 border-start-0 ps-0 <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" id="password" name="password" placeholder="Password" required>
                                </div>
                                <?php if (!empty($password_err)): ?><div class="text-danger small mt-1"><?php echo $password_err; ?></div><?php endif; ?>
                            </div>
                            
                            <div class="mb-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-white round-5 border-end-0"><i class="fas fa-check-circle text-primary"></i></span>
                                    <input type="password" class="form-control round-5 border-start-0 ps-0 <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Password" required>
                                </div>
                                <?php if (!empty($confirm_password_err)): ?><div class="text-danger small mt-1"><?php echo $confirm_password_err; ?></div><?php endif; ?>
                            </div>
                            
                            <div class="d-grid gap-2 mb-4">
                                <button type="submit" class="btn btn-primary py-3 rounded-pill">
                                    <i class="fas fa-user-plus me-2"></i> Daftar Sekarang
                                </button>
                            </div>
                            
                            <div class="text-center">
                                <p class="mb-0">Sudah punya akun? <a href="login.php" class="text-primary fw-bold">Login di sini</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Registration End -->

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
</body>

</html>
