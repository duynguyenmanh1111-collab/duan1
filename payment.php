<?php
session_start();
require_once __DIR__ . '/configs/env.php';
require_once __DIR__ . '/configs/pdo.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$conn = get_connection();
$user = $_SESSION['user'];
$bookingId = intval($_GET['id'] ?? 0);

// Lấy thông tin đơn hàng
if ($bookingId > 0) {
    $stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ?");
    $stmt->execute([$bookingId, $user['id']]);
    $booking = $stmt->fetch();
} else {
    $stmt = $conn->prepare("SELECT * FROM bookings WHERE user_id = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$user['id']]);
    $booking = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thanh Toán VNPAY - VIE Travel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --vnpay-blue: #005baa;
            --vnpay-red: #ed1c24;
            --sidebar-bg: #2c3e50;
        }

        body {
            background-color: #f0f2f5;
            font-family: 'Inter', sans-serif;
        }

        /* Sidebar Menu */
        .sidebar {
            min-height: 100vh;
            background: var(--sidebar-bg);
            color: white;
            padding-top: 20px;
            position: fixed;
            width: 16.66667%;
        }

        .sidebar a {
            color: #bdc3c7;
            text-decoration: none;
            padding: 15px 25px;
            display: block;
            transition: 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #34495e;
            color: white;
            border-left: 4px solid var(--vnpay-blue);
        }

        .main-content {
            margin-left: 16.66667%;
            padding: 40px;
        }

        /* VNPAY Card Style */
        .vnpay-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            background: white;
        }

        .vnpay-header {
            background: var(--vnpay-blue);
            color: white;
            padding: 25px;
            text-align: center;
        }

        .qr-section {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            border: 2px dashed #dee2e6;
            position: relative;
        }

        .price-tag {
            color: var(--vnpay-red);
            font-size: 2rem;
            font-weight: 800;
        }

        .btn-vnpay {
            background: var(--vnpay-blue);
            color: white;
            border-radius: 10px;
            padding: 12px;
            font-weight: bold;
            border: none;
            transition: 0.3s;
        }

        .btn-vnpay:hover {
            background: #004a8a;
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 p-0">
                <div class="sidebar">
                    <div class="px-4 mb-4 text-center">
                        <h4 class="fw-bold text-primary">VIE TRAVEL</h4>
                    </div>
                    <nav>
                        <a href="dashboard.php"><i class="fa fa-home me-2"></i> Trang chủ</a>
                        <a href="booking.php"><i class="fa fa-history me-2"></i> Đơn hàng</a>
                        <a href="#" class="active"><i class="fa fa-credit-card me-2"></i> Thanh toán</a>
                    </nav>
                </div>
            </div>

            <div class="col-md-10 main-content">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card vnpay-card">
                            <div class="vnpay-header">
                                <h4 class="mb-0">CỔNG THANH TOÁN VNPAY</h4>
                                <p class="small opacity-75 mb-0">An toàn - Nhanh chóng - Tiện lợi</p>
                            </div>

                            <div class="card-body p-4 text-center">
                                    <?php if (!$booking): ?>
                                    <div class="py-5">
                                        <i class="fa fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                        <p class="text-muted">Không tìm thấy mã đơn hàng hợp lệ</p>
                                        <a href="dashboard.php" class="btn btn-outline-primary btn-sm">Quay lại</a>
                                    </div>
                                    <?php else: ?>
                                    <p class="text-muted mb-1">Số tiền cần thanh toán</p>
                                    <div class="price-tag mb-4"><?= number_format($booking['amount']) ?>đ</div>

                                    <div class="qr-section mb-4">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=VNPAY-VIE-TRAVEL-<?= $booking['id'] ?>"
                                            alt="QR Code" class="img-fluid mb-3">
                                        <div class="d-flex justify-content-center align-items-center mb-2">
                                            <img src="https://vnpay.vn/wp-content/uploads/2020/07/Logo-VNPAYQR-update.png"
                                                height="30">
                                        </div>
                                        <p class="small text-muted mb-0">Quét mã QR bằng ứng dụng ngân hàng</p>
                                    </div>

                                    <div class="text-start bg-light p-3 rounded-3 mb-4">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="small text-muted">Nhà cung cấp:</span>
                                            <span class="small fw-bold">VIE TRAVEL PROJECT</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="small text-muted">Mã đơn hàng:</span>
                                            <span class="small fw-bold text-primary">#<?= $booking['id'] ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="small text-muted">Nội dung:</span>
                                            <span class="small fw-bold">VETRAVEL <?= $booking['id'] ?></span>
                                        </div>
                                    </div>

                                    <div class="d-grid">
                                        <button onclick="confirmPayment()" class="btn btn-vnpay btn-lg mb-3">
                                            XÁC NHẬN ĐÃ CHUYỂN KHOẢN
                                        </button>
                                        <a href="dashboard.php" class="text-decoration-none text-muted small">Hủy giao dịch
                                            và quay lại</a>
                                    </div>
                                    <?php endif; ?>
                            </div>
                            <div class="card-footer bg-white border-0 text-center pb-4">
                                <img src="https://vnpay.vn/wp-content/uploads/2020/05/Icon-VNPAY-QR.png" height="20"
                                    class="me-2">
                                <span class="text-muted" style="font-size: 12px;">Powered by VNPAY-QR</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmPayment() {
            alert("VNPAY Thông báo:\n\nChúng tôi đã nhận được yêu cầu xác thực giao dịch cho đơn hàng #<?= $booking['id'] ?>.\n\nNhân viên sẽ kiểm tra và phản hồi trong ít phút. Cảm ơn quý khách!");
            window.location.href = "booking.php";
        }
    </script>

</body>

</html>