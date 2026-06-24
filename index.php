<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>Taman Kopses Ciseeng</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <meta content="Taman Kopses Ciseeng, Ciseeng Bogor, Wisata Edukasi, Harga Tiket, Fasilitas" name="keywords" />
  <meta
    content="Informasi lengkap mengenai Harga Tiket, Fasilitas, Wahana, Jam Operasional, dan Lokasi Taman Kopses Ciseeng di Bogor."
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
      background: linear-gradient(rgba(77, 195, 135, 0.9), rgba(77, 195, 135, 0.9)), url('img/bg-tkc.png');
      background-size: cover;
      background-position: center;
    }

    .visiting-hours {
      background: linear-gradient(rgba(77, 195, 135, 0.95), rgba(77, 195, 135, 0.7)), url('img/camp.png');
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
  <div class="container-fluid bg-dark p-0 mb-5">
    <div class="row g-0 flex-column-reverse flex-lg-row">
      <div class="col-lg-6 p-0 wow fadeIn" data-wow-delay="0.1s">
        <div class="header-bg h-100 d-flex flex-column justify-content-center p-5">
          <h1 class="display-4 text-light mb-5">
            Nikmati Hari Menyenangkan Bersama Keluarga di Taman Kopses Ciseeng
          </h1>
          <div class="d-flex align-items-center pt-4 animated slideInDown">
            <a href="" class="btn btn-primary py-sm-3 px-3 px-sm-5 me-5">Read More</a>
            <button type="button" class="btn-play" data-bs-toggle="modal"
              data-src="https://www.youtube.com/embed/VPrDiWSVD-4?si=M9zofoJLcijbeGbW" data-bs-target="#videoModal">
              <span></span>
            </button>
            <h6 class="text-white m-0 ms-4 d-none d-sm-block">Watch Video</h6>
          </div>
        </div>
      </div>
      <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
        <div class="owl-carousel header-carousel">
          <div class="owl-carousel-item">
            <img class="img-fluid" src="img/carousel2.png" alt="" />
          </div>
          <div class="owl-carousel-item">
            <img class="img-fluid" src="img/carousel3.png" alt="" />
          </div>
          <div class="owl-carousel-item">
            <img class="img-fluid" src="img/carousel4.png" alt="" />
          </div>
        </div>
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
          <p><span class="text-primary me-2">#</span>Tentang Taman Kopses Ciseeng</p>
          <h1 class="display-5 mb-4">
            Mengapa Anda Harus Mengunjungi
            <span class="text-primary">Taman Kopses Ciseeng</span>!
          </h1>
          <p class="mb-4">
            Taman Kopses Ciseeng merupakan destinasi wisata baru di Ciseeng Bogor. Awalnya perkebunan, kini diubah
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
          <a class="btn btn-primary py-3 px-5 mt-3" href="">Baca Selengkapnya</a>
        </div>
        <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.5s">
          <div class="img-border">
            <img class="img-fluid" src="img/bg-tkc.png" alt="" />
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
            <span class="text-primary">Pengunjung Taman Kopses Ciseeng</span>
          </h1>
        </div>
        <div class="col-lg-6">
          <div class="bg-primary h-100 d-flex align-items-center py-4 px-4 px-sm-5">
            <i class="fa fa-3x fa-mobile-alt text-white"></i>
            <div class="ms-4">
              <p class="text-white mb-0">Hubungi kami untuk informasi lebih lanjut</p>
              <h2 class="text-white mb-0">0858-8686-3808</h2>
            </div>
          </div>
        </div>
      </div>
      <div class="row gy-5 gx-4">
        <div class="col-lg-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
          <img class="img-fluid mb-3" src="https://img.icons8.com/?size=70&id=e0Uc5D3ZrZmV&format=png&color=4dc387"
            alt="Icon" />
          <h5 class="mb-3">Taman Selfie / Photo Spot</h5>
          <span>Wahana gratis dengan berbagai spot foto unik seperti bingkai Instagram/Facebook, bentuk love, dan spot
            kayu berpayung.</span>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
          <img class="img-fluid mb-3" src="https://img.icons8.com/?size=70&id=826&format=png&color=4dc387" alt="Icon" />
          <h5 class="mb-3">Flying Fox</h5>
          <span>Nikmati sensasi melayang dengan Flying Fox. Tarif: Rp. 20.000/orang.</span>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-delay="0.5s">
          <img class="img-fluid mb-3" src="https://img.icons8.com/?size=70&id=arJ9gRDrPaAf&format=png&color=4dc387"
            alt="Icon" />
          <h5 class="mb-3">Sepeda Gantung</h5>
          <span>Nikmati sensasi melayang dengan Sepeda Gantung di atas kolam</span>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-delay="0.7s">
          <img class="img-fluid mb-3" src="https://img.icons8.com/?size=70&id=EU0betOycOMh&format=png&color=4dc387"
            alt="Icon" />
          <h5 class="mb-3">Panahan</h5>
          <span>Belajar memanah dengan bimbingan instruktur berpengalaman dengan</span>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
          <img class="img-fluid mb-3" src="https://img.icons8.com/?size=70&id=IFvQltoekox2&format=png&color=4dc387"
            alt="Icon" />
          <h5 class="mb-3">Air Terjun & Tubing</h5>
          <span>Berenang di Air Terjun atau coba Tubing dan Perahu Karet.</span>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
          <img class="img-fluid mb-3" src="https://img.icons8.com/?size=70&id=37869&format=png&color=4dc387"
            alt="Icon" />
          <h5 class="mb-3">Wahana Adventure (Gratis)</h5>
          <span>Nikmati permainan seperti spyder, berjalan di atas tambang, melintasi kolam, dan ayunan.</span>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-delay="0.5s">
          <img class="img-fluid mb-3" src="https://img.icons8.com/?size=70&id=x6G1X08IvoOe&format=png&color=4dc387"
            alt="Icon" />
          <h5 class="mb-3">Terapi Ikan & Kolam Anak (Gratis)</h5>
          <span>Akses gratis untuk relaksasi Terapi Ikan dan kesenangan Kolam Anak.</span>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-delay="0.7s">
          <img class="img-fluid mb-3" src="https://img.icons8.com/?size=70&id=9899&format=png&color=4dc387"
            alt="Icon" />
          <h5 class="mb-3">Camping & Kolam Pemancingan</h5>
          <span>Tersedia area Camping gratis dan Kolam Pemancingan.</span>
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
              <span class="badge bg-success rounded-pill">08:00 - 17:00</span>
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
                <p class="mb-0">Kp. Tugu Blok Wetan, Cibeuteung Muara, Ciseeng, Bogor, Jawa Barat</p>
              </div>
            </div>
            <div class="d-flex">
              <div class="flex-shrink-0">
                <i class="fas fa-headset text-primary fs-2"></i>
              </div>
              <div class="ms-3">
                <h5 class="text-white">Reservasi & Informasi</h5>
                <p class="mb-2"><i class="fas fa-phone-alt text-primary me-2"></i>0858-8686-3808</p>
                <p class="mb-0"><i class="fas fa-envelope text-primary me-2"></i>info@tamankopses.com</p>
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
              <div class="display-4 mb-3 fw-bold">Rp 10.000</div>
              <p class="text-muted mb-4">Per Orang</p>
              <hr class="my-4">
              <ul class="list-unstyled">
                <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i>Akses ke Taman Selfie</li>
                <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i>Rakit Bambu</li>
                <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i>Wahana Adventure</li>
                <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i>Kolam Anak</li>
                <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i>Terapi Ikan</li>
              </ul>
              <a href="#" class="btn btn-primary mt-3 w-100">Reservasi Sekarang</a>
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
                    <span class="badge bg-primary rounded-pill">Rp 20.000</span>
                  </div>
                </li>
                <li class="mb-3 p-2 border-bottom">
                  <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-bicycle text-primary me-2"></i>Sepeda Gantung</span>
                    <span class="badge bg-primary rounded-pill">Rp 10.000</span>
                  </div>
                </li>
                <li class="mb-3 p-2 border-bottom">
                  <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-bullseye text-primary me-2"></i>Panahan</span>
                    <span class="badge bg-primary rounded-pill">Rp 15.000</span>
                  </div>
                </li>
                <li class="mb-3 p-2 border-bottom">
                  <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-water text-primary me-2"></i>Air Terjun</span>
                    <span class="badge bg-primary rounded-pill">Rp 5.000</span>
                  </div>
                </li>
                <li class="mb-3 p-2 border-bottom">
                  <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-swimming-pool text-primary me-2"></i>Tubing</span>
                    <span class="badge bg-primary rounded-pill">Rp 30.000</span>
                  </div>
                </li>
                <li class="mb-3 p-2">
                  <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-ship text-primary me-2"></i>Perahu Karet</span>
                    <span class="badge bg-primary rounded-pill">Rp 10.000</span>
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
                    <span><i class="fas fa-fish text-primary me-2"></i>Kolam Pemancingan (1 Kg)</span>
                    <span class="badge bg-primary rounded-pill">Rp 40.000</span>
                  </div>
                </li>
                <li class="mb-3 p-2 border-bottom">
                  <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-fish text-primary me-2"></i>Kolam Pemancingan (3 Kg)</span>
                    <span class="badge bg-primary rounded-pill">Rp 100.000</span>
                  </div>
                </li>
                <li class="mb-3 p-2 border-bottom">
                  <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-seedling text-primary me-2"></i>Pakan Ikan</span>
                    <span class="badge bg-primary rounded-pill">Rp 5.000</span>
                  </div>
                </li>
                <li class="mb-3 p-2 border-bottom">
                  <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-umbrella-beach text-primary me-2"></i>Gazebo</span>
                    <span class="badge bg-primary rounded-pill">Rp 10.000</span>
                  </div>
                </li>
                <li class="mb-3 p-2">
                  <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-campground text-primary me-2"></i>Tikar</span>
                    <span class="badge bg-primary rounded-pill">Rp 5.000</span>
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
            Jelajahi Keindahan <span class="text-primary">Taman Kopses</span> Melalui
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
                <div class="position-relative">
                  <img class="img-fluid" src="img/g-1.jpg" alt="" />
                  <div class="animal-text p-4">
                    <p class="text-white small text-uppercase mb-0">Wahana</p>
                    <h5 class="text-white mb-0">Flying Fox</h5>
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
                <div class="position-relative">
                  <img class="img-fluid" src="img/g-2.jpg" alt="" />
                  <div class="animal-text p-4">
                    <p class="text-white small text-uppercase mb-0">Wahana</p>
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
                <div class="position-relative">
                  <img class="img-fluid" src="img/g-3.jpg" alt="" />
                  <div class="animal-text p-4">
                    <p class="text-white small text-uppercase mb-0">Wahana</p>
                    <h5 class="text-white mb-0">Flying Fox</h5>
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
            <i class="fa fa-map-marker-alt me-3"></i>Kp. Tugu Blok Wetan, Cibeuteung Muara, Ciseeng, Bogor, Jawa Barat
          </p>
          <p class="mb-2">
            <i class="fa fa-phone-alt me-3"></i>0858-8686-3808
          </p>
          <p class="mb-2">
            <i class="fa fa-envelope me-3"></i>info@example.com
          </p>
          <div class="d-flex pt-2">
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