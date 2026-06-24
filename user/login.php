<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is already logged in
if (isLoggedIn()) {
    redirect("dashboard.php");
}

// Initialize variables
$whatsapp = $password = "";
$whatsapp_err = $password_err = $login_err = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Check if whatsapp is empty
    if (empty(trim($_POST["whatsapp"]))) {
        $whatsapp_err = "Silakan masukkan nomor WhatsApp Anda.";
    } else {
        $whatsapp = trim($_POST["whatsapp"]);
    }
    
    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Silakan masukkan password Anda.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if (empty($whatsapp_err) && empty($password_err)) {
        // Prepare a select statement
        $database = new Database();
        $db = $database->getConnection();
        
        $sql = "SELECT id, name, whatsapp, password, role FROM users WHERE whatsapp = :whatsapp";
        
        if ($stmt = $db->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":whatsapp", $param_whatsapp, PDO::PARAM_STR);
            
            // Set parameters
            $param_whatsapp = $whatsapp;
            
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Check if whatsapp exists, if yes then verify password
                if ($stmt->rowCount() == 1) {
                    if ($row = $stmt->fetch()) {
                        $id = $row["id"];
                        $name = $row["name"];
                        $whatsapp = $row["whatsapp"];
                        $hashed_password = $row["password"];
                        $role = $row["role"];
                        
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["user_id"] = $id;
                            $_SESSION["user_name"] = $name;
                            $_SESSION["user_whatsapp"] = $whatsapp;
                            $_SESSION["user_role"] = $role;
                            
                            // Redirect user to appropriate dashboard
                            if ($role === 'admin') {
                                redirect("../admin/index.php");
                            } else {
                                redirect("dashboard.php");
                            }
                        } else {
                            // Password is not valid
                            $login_err = "Password yang Anda masukkan salah.";
                        }
                    }
                } else {
                    // WhatsApp number doesn't exist
                    $login_err = "Nomor WhatsApp tidak terdaftar.";
                }
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
    <title>Login - Taman Kopses Ciseeng</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="Taman Kopses Ciseeng, Login, Akun" name="keywords" />
    <meta content="Login ke akun Anda untuk memesan tiket di Taman Kopses Ciseeng" name="description" />

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
                    <li class="breadcrumb-item text-white active" aria-current="page">Login</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Login Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5 justify-content-center">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="bg-white shadow-sm round p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold">Masuk ke Akun Anda</h3>
                            <p class="text-muted">Untuk melakukan reservasi dan melihat tiket Anda</p>
                        </div>
                        
                        <?php displayFlashMessage(); ?>
                        <?php 
                        if (!empty($login_err)) {
                            echo '<div class="alert alert-danger d-flex align-items-center" role="alert">'
                                . '<i class="fas fa-exclamation-circle me-2"></i>'
                                . '<div>' . $login_err . '</div>'
                                . '</div>';
                        }
                        ?>
                        
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="mb-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-white round-5 border-end-0"><i class="fab fa-whatsapp text-primary"></i></span>
                                    <input type="text" class="form-control round-5 border-start-0 ps-0 <?php echo (!empty($whatsapp_err)) ? 'is-invalid' : ''; ?>" id="whatsapp" name="whatsapp" value="<?php echo $whatsapp; ?>" placeholder="Nomor WhatsApp" required>
                                </div>
                                <?php if (!empty($whatsapp_err)): ?><div class="text-danger small mt-1"><?php echo $whatsapp_err; ?></div><?php endif; ?>
                                <div class="form-text small">Masukkan nomor yang digunakan saat pendaftaran</div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-white round-5 border-end-0"><i class="fas fa-lock text-primary"></i></span>
                                    <input type="password" class="form-control round-5 border-start-0 ps-0 <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" id="password" name="password" placeholder="Password" required>
                                </div>
                                <?php if (!empty($password_err)): ?><div class="text-danger small mt-1"><?php echo $password_err; ?></div><?php endif; ?>
                            </div>
                            
                            <div class="d-grid gap-2 mb-4">
                                <button type="submit" class="btn btn-primary py-3 rounded-pill">
                                    <i class="fas fa-sign-in-alt me-2"></i> Masuk
                                </button>
                            </div>
                            
                            <div class="text-center">
                                <p class="mb-0">Belum punya akun? <a href="register.php" class="text-primary fw-bold">Daftar di sini</a></p>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="h-100 d-flex flex-column justify-content-center">
                        <div class="bg-primary-light p-4 mb-4 round">
                            <h4 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Informasi Login</h4>
                            <p class="mb-2">Untuk masuk ke akun Anda, gunakan:</p>
                            <ul class="list-unstyled mb-0">
                                <li><i class="fas fa-check-circle text-primary me-2"></i>Nomor WhatsApp yang digunakan saat pendaftaran</li>
                                <li><i class="fas fa-check-circle text-primary me-2"></i>Password yang Anda buat</li>
                            </ul>
                        </div>
                        
                        <div class="bg-primary-light p-4 round">
                            <h4 class="text-primary mb-3"><i class="fas fa-question-circle me-2"></i>Butuh Bantuan?</h4>
                            <p class="mb-2">Jika Anda mengalami kesulitan saat login:</p>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><i class="fas fa-phone-alt text-primary me-2"></i>Hubungi kami di 0858-8686-3808</li>
                                <li><i class="fas fa-envelope text-primary me-2"></i>Email: info@tamankopsesciseeng.com</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Login End -->

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
