<?php
// Set page title
$pageTitle = "Paket Outbound & Camping - Taman Kopses Ciseeng";

// Include database connection
require_once 'config/database.php';

// Create database connection
$database = new Database();
$conn = $database->getConnection();

// Function to get all package categories
function getPackageCategories($conn)
{
  $query = "SELECT * FROM package_categories ORDER BY id";
  $stmt = $conn->prepare($query);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get packages by category
function getPackagesByCategory($conn, $categoryId)
{
  $query = "SELECT * FROM packages WHERE category_id = :category_id AND is_active = 1";
  $stmt = $conn->prepare($query);
  $stmt->bindParam(':category_id', $categoryId);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get facilities for a package
function getPackageFacilities($conn, $packageId)
{
  $query = "SELECT f.* FROM facilities f 
              JOIN package_facilities pf ON f.id = pf.facility_id 
              WHERE pf.package_id = :package_id";
  $stmt = $conn->prepare($query);
  $stmt->bindParam(':package_id', $packageId);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get all package categories
$categories = getPackageCategories($conn);

// Set proper content type and encoding
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <title><?php echo $pageTitle; ?></title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <meta content="Taman Kopses Ciseeng, Paket Outbound, Camping Ground, Harga, Fasilitas" name="keywords" />
  <meta content="Informasi lengkap mengenai Paket Outbound, Camping Ground, dan Harga di Taman Kopses Ciseeng Bogor."
    name="description" />

  <!-- Favicon -->
  <link href="img/favicon.ico" rel="icon" />

  <!-- Google Web Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Quicksand:wght@600;700&display=swap"
    rel="stylesheet" />

  <!-- Icon Font Stylesheet -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />

  <!-- Libraries Stylesheet -->
  <link href="lib/animate/animate.min.css" rel="stylesheet" />
  <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet" />
  <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet" />

  <!-- Customized Bootstrap Stylesheet -->
  <link href="css/bootstrap.min.css" rel="stylesheet" />

  <!-- Template Stylesheet -->
  <link href="css/style.css" rel="stylesheet" />

  <!-- Custom Color Variables -->
  <style>
    /* General Styles */
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
      background-color: var(--light-bg);
      background-image: url('data:image/svg+xml,%3Csvg width="52" height="26" viewBox="0 0 52 26" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%234dc387" fill-opacity="0.05"%3E%3Cpath d="M10 10c0-2.21-1.79-4-4-4-3.314 0-6-2.686-6-6h2c0 2.21 1.79 4 4 4 3.314 0 6 2.686 6 6 0 2.21 1.79 4 4 4 3.314 0 6 2.686 6 6 0 2.21 1.79 4 4 4v2c-3.314 0-6-2.686-6-6 0-2.21-1.79-4-4-4-3.314 0-6-2.686-6-6zm25.464-1.95l8.486 8.486-1.414 1.414-8.486-8.486 1.414-1.414z" /%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
    }

    .bg-primary {
      background-color: var(--primary-color) !important;
    }

    .text-primary {
      color: var(--primary-color) !important;
    }

    .section-title h2 {
      position: relative;
      display: inline-block;
      margin-bottom: 30px;
      padding-bottom: 10px;
      font-weight: 700;
      color: var(--dark-text);
    }



    /* Card Styles */
    .price-card {
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      margin-bottom: 30px;
      height: 100%;
      transition: all 0.3s ease;
      position: relative;
      background: var(--white);
      border: none;
    }

    .price-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .price-header {
      background-color: var(--primary-light);
      color: var(--primary-color);
      padding: 20px 15px;
      text-align: center;
      font-weight: 600;
      border-radius: 15px 15px 0 0;
    }

    .price-header h3 {
      margin: 0;
      font-size: 1.5rem;
    }

    .price-body {
      padding: 25px;
    }

    .price-amount {
      font-size: 0.9rem;
      color: var(--primary-color);
      margin-bottom: 20px;
      text-align: center;
      background: var(--primary-light);
      padding: 15px;
      border-radius: 10px;
    }

    .price-amount div {
      margin: 5px 0;
    }

    /* Facility List Styles */
    .facility-list {
      list-style: none;
      padding-left: 0;
      margin-top: 20px;
    }

    .facility-list li {
      padding: 10px 15px;
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      color: var(--gray-text);
    }

    .facility-list li:hover {
      background-color: var(--primary-light);
      transform: translateX(5px);
    }

    .facility-list li:last-child {
      border-bottom: none;
    }

    .facility-list i {
      color: var(--primary-color);
      margin-right: 15px;
      font-size: 1.2rem;
      width: 25px;
      height: 25px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* Additional Styles */
    .bg-primary-light {
      background-color: var(--primary-light) !important;
    }

    /* Section Styles */
    .outbound-section {
      background-color: var(--light-color);
      padding: 50px 0;
      margin: 50px 0;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
      position: relative;
      overflow: hidden;
    }

    .outbound-section:before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: url('data:image/svg+xml,%3Csvg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"%3E%3Cpath d="M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z" fill="%23ff6f0f" fill-opacity=".05" fill-rule="evenodd"/%3E%3C/svg%3E');
      opacity: 0.5;
    }

    .section-title {
      position: relative;
      margin-bottom: 40px;
      padding-bottom: 20px;
      text-align: center;
    }

    .section-title h2 {
      font-weight: 700;
      color: var(--dark-color);
      position: relative;
      display: inline-block;
      padding: 0 15px;
    }


    .section-title h2:before {
      left: -50px;
    }

    .section-title h2:after {
      right: -50px;
    }

    .section-title:after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      width: 80px;
      height: 3px;
      background: linear-gradient(to right, transparent, var(--primary-color), transparent);
      transform: translateX(-50%);
    }

    /* Badge and Button Styles */
    .badge-custom {
      padding: 8px 15px;
      border-radius: 50px;
      font-weight: 600;
      letter-spacing: 0.5px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    .btn-custom {
      padding: 12px 25px;
      border-radius: 50px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      z-index: 1;
    }

    .btn-custom:after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.2);
      z-index: -2;
      transition: all 0.3s ease;
      transform: scale(0, 0);
      transform-origin: center;
      border-radius: 50%;
    }

    /* Custom Button Styles */
    .btn {
      border-radius: 25px;
      padding: 8px 20px;
      font-weight: 500;
    }


    .btn-custom:hover:after {
      transform: scale(2, 2);
    }



    /* Responsive Adjustments */
    @media (max-width: 768px) {
      .price-header h3 {
        font-size: 1.2rem;
      }

      .price-amount {
        font-size: 1.2rem;
      }

      .facility-list li {
        padding: 8px 10px;
      }

      .facility-list i {
        margin-right: 10px;
      }
    }
  </style>
</head>

<body>
  <!-- Spinner Start -->
  <div id="spinner"
    class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem" role="status">
      <span class="sr-only">Loading...</span>
    </div>
  </div>
  <!-- Spinner End -->

  <!-- Topbar Start -->
  <?php include_once 'includes/topbar.php'; ?>
  <!-- Topbar End -->

  <!-- Navbar Start -->
  <?php include_once 'includes/navbar.php'; ?>
  <!-- Navbar End -->

  <!-- Page Header Start -->
  <div class="container-fluid header-bg py-5 mb-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-5">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <h1 class="display-4 text-white mb-3 animated slideInDown">
            Paket Outbound & Camping
          </h1>
          <p class="text-white lead mb-4 animated slideInDown">Nikmati pengalaman outbound dan camping yang tak
            terlupakan di Taman Kopses Ciseeng</p>
          <nav aria-label="breadcrumb animated slideInDown">
            <ol class="breadcrumb mb-0">
              <li class="breadcrumb-item">
                <a class="text-white" href="index.php">Home</a>
              </li>
              <li class="breadcrumb-item text-primary active" aria-current="page">
                Paket & Harga
              </li>
            </ol>
          </nav>
          <div class="mt-4 animated slideInUp">
            <a href="#price-list" class="btn btn-primary btn-custom me-3">Lihat Paket</a>
            <a href="#" class="btn btn-outline-light btn-custom">Hubungi Kami</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Page Header End -->

  <!-- Price List Start -->
  <div class="container-xxl py-5" id="price-list">
    <div class="container">
      <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
        <h1 class="display-5 mb-4">Paket Outbound & Camping Ground</h1>
        <p class="text-muted mb-4">Nikmati berbagai paket outbound dan camping di Taman Kopses Ciseeng dengan harga
          terjangkau dan fasilitas lengkap</p>
        <div class="d-flex justify-content-center gap-3 mb-4">
          <a href="#outbound-packages" class="btn btn-primary btn-sm rounded-pill px-4 py-2">Paket Outbound</a>
          <a href="#4" class="btn btn-outline-primary btn-sm rounded-pill px-4 py-2">Camping Ground</a>
          <a href="#5" class="btn btn-outline-primary btn-sm rounded-pill px-4 py-2">Paket Fishing</a>
        </div>
        <div class="bg-light p-4 rounded-3 shadow-sm mb-5">
          <div class="row align-items-center">
            <div class="col-md-2 text-center">
              <i class="fas fa-info-circle fa-3x text-primary mb-3"></i>
            </div>
            <div class="col-md-10 text-start">
              <h5 class="fw-bold">Minimal Peserta: 35 Orang/Pack</h5>
              <p class="mb-0">Untuk reservasi dan informasi lebih lanjut, silahkan hubungi kami di
                <strong>0858-8686-3808</strong>
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Paket Outbound Section -->
      <div class="row g-4 mb-5" id="outbound-packages">
        <div class="col-12">
          <div class="section-title wow fadeInUp" data-wow-delay="0.1s">
            <h2>Rundown Paket Outbound</h2>
          </div>
        </div>
        <div class="col-12 mb-4 wow fadeInUp" data-wow-delay="0.2s">
          <div class="bg-light p-4 rounded-3 border-start border-5 border-primary">
            <div class="d-flex align-items-center">
              <i class="fas fa-lightbulb fa-2x text-primary me-3"></i>
              <div>
                <h5 class="mb-1">Aktivitas Seru untuk Semua Usia</h5>
                <p class="mb-0">Kami menyediakan berbagai aktivitas outbound yang dirancang khusus untuk anak-anak dan
                  dewasa dengan tingkat kesulitan yang berbeda.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Susunan Acara Tingkat Anak -->
        <div class="col-md-6 wow fadeInUp" data-wow-delay="0.1s">
          <div class="price-card">
            <div class="price-header">
              <h3>Susunan Acara Tingkat Anak</h3>
            </div>
            <div class="price-body">
              <h4>WARMING UP</h4>
              <ol>
                <li>Senam Pagi</li>
                <li>Jelajah Sekitar</li>
                <li>Angklung</li>
              </ol>

              <h4>ICE BREAKING</h4>
              <ol>
                <li>Senam Otak/Dance</li>
                <li>Tepuk Bayi Shark</li>
                <li>Kepala Pundak</li>
                <li>Boom Chiki Boom</li>
              </ol>

              <h4>OUTBOUND GAMES</h4>
              <ol>
                <li>Pindah Sarung</li>
                <li>Pindah Air</li>
                <li>Pipa Bocor</li>
                <li>Tower Minef</li>
                <li>Estafet Karet</li>
                <li>Estafet Tepung</li>
                <li>Bakiak</li>
                <li>Saka Bola</li>
              </ol>

              <h4>OUTBOUND TANTANGAN</h4>
              <ol>
                <li>Flying Fox</li>
                <li>Jaring Laba-laba</li>
                <li>Tarzan Swing</li>
                <li>Jembatan Goyang</li>
                <li>Jembatan Tali</li>
                <li>Jembatan Roll</li>
              </ol>

              <h4>OUTBOUND KREATIF</h4>
              <ol>
                <li>Menggambar</li>
                <li>Pewarna Kain</li>
                <li>Finger Art</li>
              </ol>

              <h4>ARMY KIDS</h4>
              <ol>
                <li>Tiarap Jalan</li>
                <li>Merangkak</li>
                <li>Salawatan</li>
                <li>Jembatan Bambu</li>
                <li>Jembatan Ban</li>
                <li>Terowongan Ban</li>
                <li>Titian Papan</li>
              </ol>

              <h4>OUTBOUND WATER ACTIVITY</h4>
              <ol>
                <li>Mencari Mutiara Kambang</li>
                <li>Memindahkan Kelereng</li>
                <li>Memindahkan Air</li>
                <li>Membuat Hujan Buatan</li>
                <li>Estafet Botol Angkat</li>
                <li>Botol Bowling</li>
                <li>Memancing Ikan</li>
                <li>Tubing</li>
                <li>Kolam Renang Anak</li>
              </ol>
            </div>
          </div>
        </div>

        <!-- Susunan Acara Tingkat Dewasa -->
        <div class="col-md-6 wow fadeInUp" data-wow-delay="0.3s">
          <div class="price-card">
            <div class="price-header">
              <h3>Susunan Acara Tingkat Dewasa</h3>
            </div>
            <div class="price-body">
              <h4>WARMING UP</h4>
              <ol>
                <li>Peregangan</li>
                <li>Bersilang</li>
                <li>Head Body</li>
                <li>Chicken Family</li>
                <li>Patung Pancoran</li>
              </ol>

              <h4>ICE BREAKING</h4>
              <ol>
                <li>Senam goyang</li>
                <li>Chicken Dance</li>
                <li>Liper Rusa</li>
                <li>Hello apa kabar</li>
                <li>Grupung</li>
              </ol>

              <h4>OUTBOUND GAMES</h4>
              <ol>
                <li>Pola wajah mercon</li>
                <li>Bola meluncur</li>
                <li>Tangkap belimbing</li>
                <li>Pipa bocor</li>
                <li>Karpaan sapi</li>
                <li>Bakiak</li>
                <li>Rangku jarat</li>
                <li>Target bola</li>
                <li>Pindah air</li>
                <li>Tarik tambang</li>
                <li>Push cup</li>
                <li>Piramida</li>
              </ol>

              <h4>OUTBOUND TANTANGAN</h4>
              <ol>
                <li>Flying Fox</li>
                <li>Jaring Laba-laba</li>
                <li>Burma Bridge</li>
                <li>Elesh wolk</li>
                <li>Two line</li>
              </ol>

              <h4>OUTBOUND WATER ACTIVITY</h4>
              <ol>
                <li>Pipa Bocor</li>
                <li>Pukul Bantal</li>
                <li>Rakit</li>
                <li>Mandi Air</li>
                <li>Menangkap Ikan</li>
                <li>Tubing</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <!-- Price List Dynamic Packages -->
      <?php foreach ($categories as $category): ?>
        <div class="package-category mb-5">
          <div class="row">
            <div class="col-12 text-center mb-4" id="<?php echo htmlspecialchars($category['id']); ?>">
              <h3 class="bg-primary text-white py-3 px-5 d-inline-block rounded-pill">
                <?php echo htmlspecialchars($category['name']); ?>
              </h3>
            </div>
          </div>
          <div class="row g-4">
            <?php
            $packages = getPackagesByCategory($conn, $category['id']);
            foreach ($packages as $index => $package):
              $facilities = getPackageFacilities($conn, $package['id']);
              ?>
              <div class="col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="price-card">
                  <div class="price-header">
                    <h3><?php echo htmlspecialchars($package['name']); ?></h3>
                  </div>
                  <div class="price-body">
                    <div class="bg-success text-white p-4 fw-bold mb-2" style="font-size: 1.5rem; border-radius: 8px;">
                      <div>Weekday <span class="currency">Rp.</span><span
                          class="amount"><?php echo number_format($package['price_weekday'], 0, ',', '.'); ?></span></div>
                      <div>Weekend <span class="currency">Rp.</span><span
                          class="amount"><?php echo number_format($package['price_weekend'], 0, ',', '.'); ?></span></div>
                    </div>

                    <?php if (!empty($package['description'])): ?>
                      <p class="price-amount">
                        <?php echo htmlspecialchars($package['description']); ?>
                      </p>
                    <?php endif; ?>


                    <h5>Fasilitas yang didapat :</h5>
                    <ul class="facility-list">

                      <?php foreach ($facilities as $facility): ?>
                        <li><i class="<?php echo $facility['icon']; ?>"></i><?php echo htmlspecialchars($facility['name']); ?>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>


      <!-- Paket Minimal -->
      <div class="row mb-5">
        <div class="col-12 text-center wow fadeInUp" data-wow-delay="0.1s">
          <div class="bg-warning text-dark p-5 rounded-4 shadow position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 w-100 h-100"
              style="background: url('data:image/svg+xml,%3Csvg width=" 52" height="26" viewBox="0 0 52 26"
              xmlns="http://www.w3.org/2000/svg" %3E%3Cg fill="none" fill-rule="evenodd" %3E%3Cg fill="%23ffffff"
              fill-opacity="0.1" %3E%3Cpath
              d="M10 10c0-2.21-1.79-4-4-4-3.314 0-6-2.686-6-6h2c0 2.21 1.79 4 4 4 3.314 0 6 2.686 6 6 0 2.21 1.79 4 4 4 3.314 0 6 2.686 6 6 0 2.21 1.79 4 4 4v2c-3.314 0-6-2.686-6-6 0-2.21-1.79-4-4-4-3.314 0-6-2.686-6-6zm25.464-1.95l8.486 8.486-1.414 1.414-8.486-8.486 1.414-1.414z"
              /%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); opacity: 0.3;"></div>
            <div class="position-relative">
              <h3 class="fw-bold mb-4">Paket Minimal</h3>
              <div class="display-5 my-3 fw-bold">35 Orang/Pack</div>
              <div class="d-flex justify-content-center mt-4">
                <div class="bg-white text-dark p-3 rounded-pill shadow-sm d-inline-flex align-items-center">
                  <i class="fas fa-users text-warning me-2"></i>
                  <span>Ideal untuk rombongan sekolah, kantor, atau komunitas</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Minuman & Perlengkapan -->
      <div class="row g-4 mb-5">
        <div class="col-md-6 wow fadeInUp" data-wow-delay="0.1s">
          <div class="price-card">
            <div class="price-header">
              <h3>Minuman Dingin & Makanan Tambahan</h3>
            </div>
            <div class="price-body">
              <div class="text-center mb-4">
                <img src="https://img.icons8.com/?size=70&id=ganUGT2B3II6&format=png&color=4dc387" alt="Food"
                  class="img-fluid" style="max-width: 120px;">
              </div>
              <ul class="facility-list">
                <li><i class="fas fa-glass-water"></i>Soda Gembira</li>
                <li><i class="fas fa-glass-water"></i>Bakso Kuah + Air Mineral</li>
                <li><i class="fas fa-glass-water"></i>Nasi Putih, Ayam Goreng Tepung, Mie Goreng, Sambal Saus, Air
                  Mineral</li>
                <li><i class="fas fa-glass-water"></i>Nasi Putih, Ayam Bakar, Sayur Asem, Tahu Tempe, Sambal + Lalapan,
                  Buah, Kerupuk, Air Mineral</li>
              </ul>
            </div>
          </div>
        </div>

        <div class="col-md-6 wow fadeInUp" data-wow-delay="0.3s">
          <div class="price-card">
            <div class="price-header">
              <h3>Peralatan Yang Harus Dibawa</h3>
            </div>
            <div class="price-body">
              <div class="text-center mb-4">
                <img src="https://img.icons8.com/?size=70&id=2969&format=png&color=4dc387" alt="Equipment"
                  class="img-fluid" style="max-width: 120px;">
              </div>
              <div class="bg-light p-4 rounded-3">
                <div class="d-flex align-items-center mb-3">
                  <div class="bg-primary rounded-circle p-2 me-3">
                    <i class="fas fa-umbrella-beach text-white"></i>
                  </div>
                  <span>Perlengkapan Puyung</span>
                </div>
                <div class="d-flex align-items-center mb-3">
                  <div class="bg-primary rounded-circle p-2 me-3">
                    <i class="fas fa-cloud-rain text-white"></i>
                  </div>
                  <span>Jas Hujan</span>
                </div>
                <div class="d-flex align-items-center mb-3">
                  <div class="bg-primary rounded-circle p-2 me-3">
                    <i class="fas fa-first-aid text-white"></i>
                  </div>
                  <span>Obat Obatan Khusus</span>
                </div>
                <div class="d-flex align-items-center mb-3">
                  <div class="bg-primary rounded-circle p-2 me-3">
                    <i class="fas fa-socks text-white"></i>
                  </div>
                  <span>Kaos Kaki</span>
                </div>
                <div class="d-flex align-items-center">
                  <div class="bg-primary rounded-circle p-2 me-3">
                    <i class="fas fa-tshirt text-white"></i>
                  </div>
                  <span>Pakaian Ganti</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- CTA Section -->
      <div class="row my-5">
        <div class="col-12 wow fadeInUp" data-wow-delay="0.1s">
          <div class="bg-primary text-white p-5 rounded-4 shadow position-relative overflow-hidden">
            <div class="position-absolute top-0 end-0 p-3">
              <img src="img/icon/icon-10.png" alt="" class="img-fluid" style="max-width: 150px; opacity: 0.2;">
            </div>
            <div class="row align-items-center">
              <div class="col-lg-8">
                <h2 class="mb-3">Siap Untuk Petualangan Seru di Taman Kopses?</h2>
                <p class="lead mb-0">Hubungi kami sekarang untuk reservasi dan dapatkan pengalaman outbound dan camping
                  yang tak terlupakan!</p>
              </div>
              <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <a href="#" class="btn btn-light btn-lg rounded-pill px-5 py-3 shadow-sm">Reservasi Sekarang</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Price List End -->

  <!-- Footer Start -->
  <div class="container-fluid footer bg-dark text-light footer mt-5 pt-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-5">
      <div class="row g-5">
        <div class="col-lg-3 col-md-6">
          <h5 class="text-light mb-4">Alamat</h5>
          <p class="mb-2">
            <i class="fa fa-map-marker-alt me-3"></i>Kp. Tugu Blok Wetan, Cibeuteung Muara, Ciseeng, Bogor, Jawa Barat
          </p>
          <p class="mb-2">
            <i class="fa fa-phone-alt me-3"></i>0858-8686-3808
          </p>
          <p class="mb-2">
            <i class="fa fa-envelope me-3"></i>info@tamankopses.com
          </p>
          <div class="d-flex pt-2">
            <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-twitter"></i></a>
            <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-facebook-f"></i></a>
            <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-youtube"></i></a>
            <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-linkedin-in"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <h5 class="text-light mb-4">Link Cepat</h5>
          <a class="btn btn-link" href="">Tentang Kami</a>
          <a class="btn btn-link" href="">Kontak Kami</a>
          <a class="btn btn-link" href="">Wahana & Fasilitas</a>
          <a class="btn btn-link" href="">Jam Operasional</a>
          <a class="btn btn-link" href="">Rute</a>
        </div>
        <div class="col-lg-3 col-md-6">
          <h5 class="text-light mb-4">Link Populer</h5>
          <a class="btn btn-link" href="">Tentang Kami</a>
          <a class="btn btn-link" href="">Kontak Kami</a>
          <a class="btn btn-link" href="">Wahana & Fasilitas</a>
          <a class="btn btn-link" href="">Jam Operasional</a>
          <a class="btn btn-link" href="">Rute</a>
        </div>
        <div class="col-lg-3 col-md-6">
          <h5 class="text-light mb-4">Newsletter</h5>
          <p>Dapatkan info terbaru dan promo menarik dari Taman Kopses Ciseeng.</p>
          <div class="position-relative mx-auto" style="max-width: 400px">
            <input class="form-control border-0 w-100 py-3 ps-4 pe-5" type="text" placeholder="Email Anda" />
            <button type="button" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2">
              Daftar
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="copyright">
        <div class="row">
          <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
            &copy; <a class="border-bottom" href="#">Taman Kopses Ciseeng</a>, All
            Right Reserved.
          </div>
          <div class="col-md-6 text-center text-md-end">
            Dikembangkan oleh
            <a class="border-bottom" href="#">Tim Taman Kopses</a>
            <br />Bogor, Jawa Barat
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Footer End -->

  <!-- Back to Top -->
  <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

  <!-- JavaScript Libraries -->
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="lib/wow/wow.min.js"></script>
  <script src="lib/easing/easing.min.js"></script>
  <script src="lib/waypoints/waypoints.min.js"></script>
  <script src="lib/counterup/counterup.min.js"></script>
  <script src="lib/owlcarousel/owl.carousel.min.js"></script>
  <script src="lib/lightbox/js/lightbox.min.js"></script>

  <!-- Template Javascript -->
  <script src="js/main.js"></script>
</body>

</html>