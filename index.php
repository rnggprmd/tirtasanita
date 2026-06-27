<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>Tirta Sanita Outbound</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <meta content="Tirta Sanita Outbound, Ciseeng Bogor, Wisata Edukasi, Harga Tiket, Fasilitas" name="keywords" />
  <meta
    content="Informasi lengkap mengenai Harga Tiket, Fasilitas, Wahana, Jam Operasional, dan Lokasi Tirta Sanita Outbound di Bogor."
    name="description" />

  <!-- Favicon -->
  <link href="img/logo.png" rel="icon" />

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
    :root {
      --primary-color: #4dc387;
      --primary-dark: #3da876;
      --primary-light: #e8f5f0;
      --white: #ffffff;
      --light-bg: #f8f9fa;
      --dark-text: #2c3e50;
      --gray-text: #6c757d;
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

    .card {
      border-radius: 15px;
      transition: all 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .hover-scale {
      transition: transform 0.3s ease;
    }

    .hover-scale:hover {
      transform: scale(1.02);
    }

    .badge {
      padding: 8px 12px;
      font-weight: 500;
    }

    .list-group-item {
      border: none;
      background-color: var(--light-bg);
      margin-bottom: 8px;
      border-radius: 8px !important;
    }

    .header-bg {
      background: linear-gradient(rgba(77, 195, 135, 0.9), rgba(77, 195, 135, 0.9));
      background-size: cover;
      background-position: center;
    }

    .visiting-hours {
      background: linear-gradient(rgba(77, 195, 135, 0.95), rgba(77, 195, 135, 0.7)), url('img/foto1.png');
    }

    .footer {
      background-color: var(--dark-text) !important;
    }

    .btn-outline-light:hover {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }

    .navbar {
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .navbar-brand h1 {
      font-weight: 700;
      color: var(--primary-color) !important;
    }

    .display-4,
    .display-5,
    .display-6 {
      font-weight: 700;
    }

    .card-header {
      border-radius: 15px 15px 0 0 !important;
      background-color: var(--primary-color) !important;
    }

    .testimonial-text {
      background-color: var(--light-bg);
      border-radius: 15px;
    }

    .animal-text {
      background: linear-gradient(rgba(77, 195, 135, 0.9), rgba(77, 195, 135, 0.9));
      border-radius: 0 0 15px 15px;
    }

    .back-to-top {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }

    .back-to-top:hover {
      background-color: var(--primary-dark);
      border-color: var(--primary-dark);
    }

    /* Custom Animations */
    .wow.fadeInUp {
      animation-duration: 0.8s;
    }

    /* Custom Card Styles */
    .price-card {
      background-color: var(--white);
      border: none;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .price-card .card-header {
      background-color: var(--primary-light);
      color: var(--primary-color);
      font-weight: 600;
    }

    .price-card .display-4 {
      color: var(--primary-color);
    }

    .price-card .list-unstyled li {
      color: var(--gray-text);
    }

    .price-card .btn-primary {
      background-color: var(--primary-color);
      border-radius: 25px;
      padding: 10px 25px;
      font-weight: 500;
    }

    /* Custom Badge Styles */
    .badge.bg-primary {
      background-color: var(--primary-light) !important;
      color: var(--primary-color);
    }

    .badge.bg-success {
      background-color: #e8f5e9 !important;
      color: #2e7d32;
    }

    .badge.bg-danger {
      background-color: #ffebee !important;
      color: #c62828;
    }

    /* Custom Icon Styles */
    .text-warning {
      color: var(--primary-color) !important;
    }

    /* Custom Button Styles */
    .btn {
      border-radius: 25px;
      padding: 8px 20px;
      font-weight: 500;
    }

    .btn-sm {
      border-radius: 20px;
      padding: 5px 15px;
    }

    /* Custom Form Styles */
    .form-control {
      border-radius: 25px;
      border: 1px solid #e0e0e0;
      padding: 12px 20px;
    }

    .form-control:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.2rem rgba(77, 195, 135, 0.25);
    }

    /* Header Carousel Responsive */
    .header-carousel-container {
      height: 500px;
      overflow: hidden;
    }

    .owl-carousel-item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    @media (max-width: 768px) {
      .header-carousel-container {
        height: 300px;
      }

      .header-carousel-container .display-4 {
        font-size: 1.5rem !important;
        margin-bottom: 1rem !important;
      }

      .header-carousel-container .btn {
        font-size: 0.875rem;
        padding: 0.5rem 1rem !important;
      }
    }

    @media (max-width: 576px) {
      .header-carousel-container {
        height: 240px;
      }

      .owl-carousel-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
      }

      .header-carousel-container .display-4 {
        font-size: 1.25rem !important;
        margin-bottom: 0.75rem !important;
      }

      .header-carousel-container .btn-play {
        width: 40px !important;
        height: 40px !important;
      }

      .header-carousel-container h6 {
        display: none !important;
      }

      .header-carousel-container .ps-5 {
        padding-left: 1rem !important;
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

  <!-- Header Start -->
  <div class="container-fluid p-0 mb-5">
    <div class="row g-0">
      <div class="col-lg-12 position-relative header-carousel-container">
        <!-- Fixed Text Content -->
        <div class="position-absolute top-0 start-0 h-100 d-flex flex-column justify-content-center ps-5" style="z-index: 10; max-width: 600px;">
          <h1 class="display-4 text-light mb-4">
            Nikmati Hari Menyenangkan Bersama Keluarga di Tirta Sanita Outbound
          </h1>
          <div class="d-flex align-items-center animated slideInDown gap-3">
            <a href="" class="btn btn-primary py-sm-3 px-3 px-sm-5">Read More</a>
            <button type="button" class="btn-play" data-bs-toggle="modal"
              data-src="https://www.youtube.com/embed/nTBccz0uH_M" data-bs-target="#videoModal">
              <span></span>
            </button>
            <h6 class="text-white m-0 d-none d-sm-block">Watch Video</h6>
          </div>
        </div>

        <!-- Carousel Background -->
        <div class="owl-carousel header-carousel" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; width: 100%; height: 100%;">
          <div class="owl-carousel-item" style="height: 100%; display: flex; align-items: center; justify-content: center; overflow: hidden;">
            <img class="img-fluid" src="img/foto1.png" alt="" />
          </div>
          <div class="owl-carousel-item" style="height: 100%; display: flex; align-items: center; justify-content: center; overflow: hidden;">
            <img class="img-fluid" src="img/foto2.png" alt="" />
          </div>
          <div class="owl-carousel-item" style="height: 100%; display: flex; align-items: center; justify-content: center; overflow: hidden;">
            <img class="img-fluid" src="img/foto3.png" alt="" />
          </div>
        </div>

        <!-- Gradient Overlay -->
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(90deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.2) 50%, transparent 100%); z-index: 5;"></div>
      </div>
    </div>
  </div>
  <!-- Header End -->

  <!-- Video Modal Start -->
  <div class="modal modal-video fade" id="videoModal" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content rounded-0">
        <div class="modal-header">
          <h3 class="modal-title" id="exampleModalLabel">Youtube Video</h3>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- 16:9 aspect ratio -->
          <div class="ratio ratio-16x9">
            <iframe class="embed-responsive-item" src="" id="video" allowfullscreen allowscriptaccess="always"
              allow="autoplay"></iframe>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Video Modal End -->

  <!-- About Start -->
  <div class="container-xxl py-5">
    <div class="container">
      <div class="row g-5">
        <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
          <p><span class="text-primary me-2">#</span>Tentang Tirta Sanita Outbound</p>
          <h1 class="display-5 mb-4">
            Mengapa Anda Harus Mengunjungi
            <span class="text-primary">Tirta Sanita Outbound</span>!
          </h1>
          <p class="mb-4">
            Tirta Sanita Outbound merupakan destinasi wisata baru di Ciseeng Bogor. Awalnya perkebunan, kini diubah
            menjadi objek wisata edukasi yang masih terus dikembangkan. Meskipun begitu, pengunjung terus meningkat
            karena lokasinya yang asri dan berbagai wahana edukatif yang tersedia.
          </p>
          <h5 class="mb-3">
            <i class="far fa-check-circle text-primary me-3"></i>Lokasi Hijau dan Asri
          </h5>
          <h5 class="mb-3">
            <i class="far fa-check-circle text-primary me-3"></i>Wahana Bermain dan Belajar Anak
          </h5>
          <h5 class="mb-3">
            <i class="far fa-check-circle text-primary me-3"></i>Fasilitas Pendukung Lengkap
          </h5>
          <h5 class="mb-3">
            <i class="far fa-check-circle text-primary me-3"></i>Harga Terjangkau
          </h5>
        </div>
        <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.5s">
          <div class="img-border">
            <img class="img-fluid" src="img/bg-tirtasanita.png" alt="" />
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- About End -->

  <!-- Facts Start -->
  <!-- Removing this section as the new text does not provide equivalent statistics -->
  <!-- Facts End -->

  <!-- Service Start -->
  <div class="container-xxl py-5">
    <div class="container">
      <div class="row g-5 mb-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="col-lg-6">
          <p><span class="text-primary me-2">#</span>Wahana & Fasilitas Kami</p>
          <h1 class="display-5 mb-0">
            Wahana dan Fasilitas Spesial Untuk
            <span class="text-primary">Pengunjung Tirta Sanita Outbound</span>
          </h1>
        </div>
        <div class="col-lg-6">
          <div class="bg-primary h-100 d-flex align-items-center py-4 px-4 px-sm-5">
            <i class="fa fa-3x fa-mobile-alt text-white"></i>
            <div class="ms-4">
              <p class="text-white mb-0">Hubungi kami untuk informasi lebih lanjut</p>
              <h2 class="text-white mb-0">0858-1077-1107</h2>
            </div>
          </div>
        </div>
      </div>
      <div class="row gy-5 gx-4">
        <div class="col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
          <i class="fas fa-bolt fa-3x text-primary mb-3"></i>
          <h5 class="mb-3">Flying Fox</h5>
          <span>Nikmati sensasi melayang dengan Flying Fox.</span>
        </div>
        <div class="col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
          <i class="fas fa-fish fa-3x text-primary mb-3"></i>
          <h5 class="mb-3">Terapi Ikan & Kolam Anak (Gratis)</h5>
          <span>Akses gratis untuk relaksasi Terapi Ikan dan kesenangan Kolam Anak.</span>
        </div>
        <div class="col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
          <i class="fas fa-campground fa-3x text-primary mb-3"></i>
          <h5 class="mb-3">Camping & Kolam Pemancingan</h5>
          <span>Tersedia area Camping gratis dan Kolam Pemancingan.</span>
        </div>
        <div class="col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="0.7s">
          <i class="fas fa-building fa-3x text-primary mb-3"></i>
          <h5 class="mb-3">Gedung Serbaguna</h5>
          <span>Fasilitas lengkap untuk acara, seminar, gathering, dan meeting dengan kapasitas besar.</span>
        </div>
        <div class="col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
          <i class="fas fa-hotel fa-3x text-primary mb-3"></i>
          <h5 class="mb-3">Penginapan</h5>
          <span>Nikmati penginapan nyaman dengan pemandangan alam yang indah untuk pengalaman menginap yang tak terlupakan.</span>
        </div>
      </div>
    </div>
  </div>
  <!-- Service End -->

  <!-- Animal Start -->
  <!-- Removing this section as the new content is not about animals -->
  <!-- Animal End -->

  <!-- Visiting Hours Start -->
  <div class="container-xxl bg-primary visiting-hours my-5 py-5 wow fadeInUp" data-wow-delay="0.1s">
    <div class="container py-5">
      <div class="row g-5">
        <div class="col-md-6 wow fadeIn" data-wow-delay="0.3s">
          <h1 class="display-6 text-white mb-5"><i class="far fa-clock me-3"></i>Jam Operasional</h1>
          <ul class="list-group list-group-flush rounded shadow-sm">
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span><i class="fas fa-calendar-day text-primary me-2"></i>Sabtu - Kamis</span>
              <span class="badge bg-success rounded-pill">07:00 - 17:00</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span><i class="fas fa-calendar-day text-primary me-2"></i>Jumat</span>
              <span class="badge bg-danger rounded-pill">TUTUP</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span><i class="fas fa-calendar-day text-primary me-2"></i>Hari Libur Nasional</span>
              <span class="badge bg-success rounded-pill">BUKA *Kecuali jika jatuh di hari Jumat</span>
            </li>
          </ul>
        </div>
        <div class="col-md-6 text-light wow fadeIn" data-wow-delay="0.5s">
          <h1 class="display-6 text-white mb-5"><i class="fas fa-info-circle me-3"></i>Info Kontak</h1>
          <div class="bg-dark rounded p-4 shadow-sm">
            <div class="d-flex mb-4">
              <div class="flex-shrink-0">
                <i class="fas fa-map-marker-alt text-primary fs-2"></i>
              </div>
              <div class="ms-3">
                <h5 class="text-white">Alamat</h5>
                <p class="mb-0">Jl. Raya Gunung Kapur Parung - Bogor</p>
              </div>
            </div>
            <div class="d-flex">
              <div class="flex-shrink-0">
                <i class="fas fa-headset text-primary fs-2"></i>
              </div>
              <div class="ms-3">
                <h5 class="text-white">Reservasi & Informasi</h5>
                <p class="mb-2"><i class="fas fa-phone-alt text-primary me-2"></i>0858-1077-1107</p>
                <p class="mb-0"><i class="fas fa-envelope text-primary me-2"></i>info@tirtasanita.com</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tiket & Wahana Section -->
      <div class="row mt-5">
        <div class="col-12 text-center wow fadeIn" data-wow-delay="0.1s">
          <h1 class="display-6 text-white mb-3"><i class="fas fa-ticket-alt me-3"></i>Tiket & Wahana</h1>
          <p class="text-white-50 mb-5">Nikmati berbagai fasilitas dan wahana seru dengan harga terjangkau</p>
        </div>

        <!-- Tiket Masuk -->
        <div class="col-lg-4 col-md-6 mb-4 wow fadeInUp" data-wow-delay="0.1s">
          <div class="card h-100 price-card hover-scale">
            <div class="card-header text-center py-4">
              <h3 class="card-title mb-0">Tiket Masuk</h3>
            </div>
            <div class="card-body text-center p-4">
              <div class="bg-success text-white p-4 fw-bold mb-2" style="font-size: 1.5rem; border-radius: 8px;">
                <div>Anak <span class="currency">Rp.</span><span class="amount">10.000</span></div>
                <div>Dewasa <span class="currency">Rp.</span><span class="amount">15.000</span></div>
              </div>
              <p class="text-muted mb-4">Per Orang</p>
              <hr class="my-4">
              <a href="user/login.php" class="btn btn-primary mt-3 w-100">Reservasi Sekarang</a>
            </div>
          </div>
        </div>

        <!-- Wahana Berbayar -->
        <div class="col-lg-4 col-md-6 mb-4 wow fadeInUp" data-wow-delay="0.3s">
          <div class="card h-100 price-card hover-scale">
            <div class="card-header text-center py-4">
              <h3 class="card-title mb-0">Wahana Berbayar</h3>
            </div>
            <div class="card-body p-4">
              <ul class="list-unstyled">
                <li class="mb-3 p-2 border-bottom">
                  <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-bolt text-primary me-2"></i>Flying Fox</span>
                    <span class="badge bg-primary rounded-pill">Rp 30.000</span>
                  </div>
                </li>
                <li class="mb-3 p-2 border-bottom">
                  <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-ship text-primary me-2"></i>Terapi Ikan</span>
                    <span class="badge bg-primary rounded-pill">Rp 10.000</span>
                  </div>
                </li>
                <li class="mb-3 p-2 border-bottom">
                  <div class="d-flex justify-content-between align-items-center">
                    <span></i>Dan lain-lain</span>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Fasilitas Tambahan -->
        <div class="col-lg-4 col-md-6 mb-4 wow fadeInUp" data-wow-delay="0.5s">
          <div class="card h-100 price-card hover-scale">
            <div class="card-header text-center py-4">
              <h3 class="card-title mb-0">Fasilitas Tambahan</h3>
            </div>
            <div class="card-body p-4">
              <ul class="list-unstyled">
                <li class="mb-3 p-2 border-bottom">
                  <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-motorcycle text-primary me-2"></i>Parkir Motor</span>
                    <span class="badge bg-primary rounded-pill">Rp 3.000</span>
                  </div>
                </li>
                <li class="mb-3 p-2 border-bottom">
                  <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-car text-primary me-2"></i>Parkir Mobil</span>
                    <span class="badge bg-primary rounded-pill">Rp 5.000</span>
                  </div>
                </li>
                <li class="mb-3 p-2 border-bottom">
                  <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-building text-primary me-2"></i>Gedung Serbaguna</span>
                    <span class="badge bg-primary rounded-pill">150 Pax / Rp 5.000.000</span>
                  </div>
                </li>
                <li class="mb-3 p-2 border-bottom">
                  <div class="d-flex justify-content-between align-items-center">
                    <span></i>Dan lain-lain</span>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Visiting Hours End -->


  <!-- Animal Start -->
  <div class="container-xxl py-5">
    <div class="container">
      <div class="row g-5 mb-5 align-items-end wow fadeInUp" data-wow-delay="0.1s">
        <div class="col-lg-6">
          <p><span class="text-primary me-2">#</span>Galeri Foto</p>
          <h1 class="display-5 mb-0">
            Jelajahi Keindahan <span class="text-primary">Tirta Sanita Outbound</span> Melalui
            Foto
          </h1>
        </div>
        <div class="col-lg-6 text-lg-end">
        </div>
      </div>
      <div class="row g-4">
        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
          <div class="row g-4">
            <div class="col-12">
              <a class="animal-item" href="img/g-1.jpg" data-lightbox="animal">
                <div class="position-relative" style="height: 300px; overflow: hidden;">
                  <img class="img-fluid w-100 h-100" src="img/g-1.jpg" alt="" style="object-fit: cover;" />
                  <div class="animal-text p-4">
                    <h5 class="text-white mb-0">Outbound</h5>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
          <div class="row g-4">
            <div class="col-12">
              <a class="animal-item" href="img/g-2.jpg" data-lightbox="animal">
                <div class="position-relative" style="height: 300px; overflow: hidden;">
                  <img class="img-fluid w-100 h-100" src="img/g-2.jpg" alt="" style="object-fit: cover;" />
                  <div class="animal-text p-4">
                    <h5 class="text-white mb-0">Memancing</h5>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
          <div class="row g-4">
            <div class="col-12">
              <a class="animal-item" href="img/g-3.jpg" data-lightbox="animal">
                <div class="position-relative" style="height: 300px; overflow: hidden;">
                  <img class="img-fluid w-100 h-100" src="img/g-3.jpg" alt="" style="object-fit: cover;" />
                  <div class="animal-text p-4">
                    <h5 class="text-white mb-0">Flying Fox</h5>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
          <div class="row g-4">
            <div class="col-12">
              <a class="animal-item" href="img/g-3.jpg" data-lightbox="animal">
                <div class="position-relative" style="height: 300px; overflow: hidden;">
                  <img class="img-fluid w-100 h-100" src="img/foto2.png" alt="" style="object-fit: cover;" />
                  <div class="animal-text p-4">
                    <h5 class="text-white mb-0">Melukis</h5>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
          <div class="row g-4">
            <div class="col-12">
              <a class="animal-item" href="img/g-3.jpg" data-lightbox="animal">
                <div class="position-relative" style="height: 300px; overflow: hidden;">
                  <img class="img-fluid w-100 h-100" src="img/foto3.png" alt="" style="object-fit: cover;" />
                  <div class="animal-text p-4">
                    <h5 class="text-white mb-0">Gedung Serbaguna</h5>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
          <div class="row g-4">
            <div class="col-12">
              <a class="animal-item" href="img/g-3.jpg" data-lightbox="animal">
                <div class="position-relative" style="height: 300px; overflow: hidden;">
                  <img class="img-fluid w-100 h-100" src="img/foto9.png" alt="" style="object-fit: cover;" />
                  <div class="animal-text p-4">
                    <h5 class="text-white mb-0">Penginapan</h5>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
          <div class="row g-4">
            <div class="col-12">
              <a class="animal-item" href="img/g-3.jpg" data-lightbox="animal">
                <div class="position-relative" style="height: 300px; overflow: hidden;">
                  <img class="img-fluid w-100 h-100" src="img/foto1.png" alt="" style="object-fit: cover;" />
                  <div class="animal-text p-4">
                    <h5 class="text-white mb-0">Camp Area</h5>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
          <div class="row g-4">
            <div class="col-12">
              <a class="animal-item" href="img/g-3.jpg" data-lightbox="animal">
                <div class="position-relative" style="height: 300px; overflow: hidden;">
                  <img class="img-fluid w-100 h-100" src="img/foto4.png" alt="" style="object-fit: cover;" />
                  <div class="animal-text p-4">
                    <h5 class="text-white mb-0">Terapi Ikan</h5>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
          <div class="row g-4">
            <div class="col-12">
              <a class="animal-item" href="img/g-3.jpg" data-lightbox="animal">
                <div class="position-relative" style="height: 300px; overflow: hidden;">
                  <img class="img-fluid w-100 h-100" src="img/bg-tirtasanita.png" alt="" style="object-fit: cover;" />
                  <div class="animal-text p-4">
                    <h5 class="text-white mb-0">Halaman Depan</h5>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Animal End -->



  <!-- Membership Start -->
  <!-- Removing this section as the new content does not provide membership details -->
  <!-- Membership End -->

  <!-- Testimonial Start -->
  <!-- Removing this section as the new content does not provide testimonials -->
  <!-- Testimonial End -->

  <!-- Footer Start -->
  <div class="container-fluid footer bg-dark text-light footer mt-5 pt-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-5">
      <div class="row g-5">
        <div class="col-lg-3 col-md-6">
          <h5 class="text-light mb-4">Alamat</h5>
          <p class="mb-2">
            <i class="fa fa-map-marker-alt me-3"></i>Jl. Raya Gunung Kapur Parung - Bogor
          </p>
          <p class="mb-2">
            <i class="fa fa-phone-alt me-3"></i>0858-1077-1107
          </p>
          <p class="mb-2">
            <i class="fa fa-envelope me-3"></i>info@tirtasanita.com
          </p>
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
          <p>Dapatkan info terbaru dan promo menarik dari Tirta Sanita Outbound.</p>
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
            &copy; <a class="border-bottom" href="#">Tirta Sanita Outbound</a>, All
            Right Reserved.
          </div>
          <div class="col-md-6 text-center text-md-end">
            Dikembangkan oleh
            <a class="border-bottom" href="#">Tim Tirta Sanita</a>
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