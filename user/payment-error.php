<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect("login.php");
}

$order_id = $_GET['order_id'] ?? null;

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <title>Pembayaran Gagal - Tirta Sanita Outbound</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <link href="../img/favicon.ico" rel="icon" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Quicksand:wght@600;700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="../css/bootstrap.min.css" rel="stylesheet" />
    <link href="../css/style.css" rel="stylesheet" />
    <style>
        :root {
            --danger-color: #dc3545;
        }

        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            padding: 20px;
        }

        .error-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 50px;
            text-align: center;
            max-width: 500px;
        }

        .error-icon {
            width: 80px;
            height: 80px;
            background: #ff6b6b;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: shake 0.6s ease;
        }

        .error-icon i {
            color: white;
            font-size: 40px;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .error-card h2 {
            color: #ff6b6b;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .error-card p {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn {
            padding: 12px 30px;
            border-radius: 25px;
            border: none;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #4dc387;
            color: white;
        }

        .btn-primary:hover {
            background: #3da876;
            color: white;
        }

        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }

        .btn-secondary:hover {
            background: #d0d0d0;
            color: #333;
        }
    </style>
</head>

<body>
    <div class="error-container">
        <div class="error-card">
            <div class="error-icon">
                <i class="fas fa-times"></i>
            </div>

            <h2>Pembayaran Gagal</h2>
            <p>Maaf, pembayaran Anda gagal diproses. Silakan coba lagi atau hubungi layanan pelanggan kami untuk bantuan.</p>

            <p style="color: #999; font-size: 14px;">No. Pesanan: <strong><?php echo htmlspecialchars($order_id); ?></strong></p>

            <div class="btn-group">
                <a href="dashboard.php" class="btn btn-primary">Kembali ke Dashboard</a>
                <a href="../index.php" class="btn btn-secondary">Kembali ke Home</a>
            </div>
        </div>
    </div>
</body>

</html>
