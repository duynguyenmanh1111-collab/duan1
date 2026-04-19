<?php
session_start();
require_once __DIR__ . '/configs/env.php';
require_once __DIR__ . '/configs/pdo.php';

// 🔒 bắt đăng nhập
if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

$conn = get_connection();
if (!$conn) {
    die('Không thể kết nối database');
}

// 🔥 kiểm tra booking_id
$bookingId = intval($_GET['booking_id'] ?? 0);

if ($bookingId <= 0) {
    die('Booking không hợp lệ');
}

// 👉 lấy thông tin booking
$stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->execute([$bookingId]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    die('Booking không tồn tại');
}

// 👉 xử lý thanh toán
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // cập nhật trạng thái thanh toán
    $stmt = $conn->prepare("UPDATE bookings SET payment_status = 'Đã thanh toán' WHERE id = ?");
    $stmt->execute([$bookingId]);

    $message = "Thanh toán thành công!";

    // reload lại data
    $stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ?");
    $stmt->execute([$bookingId]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thanh toán</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="col-md-6 mx-auto">

            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Thanh toán booking</h5>
                </div>

                <div class="card-body">

                    <?php if ($message): ?>
                        <div class="alert alert-success">
                            <?= $message ?>
                        </div>
                    <?php endif; ?>

                    <p><strong>Tour:</strong> <?= htmlspecialchars($booking['tour_title']) ?></p>
                    <p><strong>Ngày:</strong> <?= date('d/m/Y', strtotime($booking['booking_date'])) ?></p>
                    <p><strong>Số khách:</strong> <?= $booking['quantity'] ?></p>
                    <p><strong>Số tiền:</strong> <?= number_format($booking['amount']) ?>đ</p>

                    <p>
                        <strong>Trạng thái:</strong>
                        <span
                            class="badge <?= $booking['payment_status'] === 'Đã thanh toán' ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $booking['payment_status'] ?>
                        </span>
                    </p>

                    <?php if ($booking['payment_status'] !== 'Đã thanh toán'): ?>

                        <form method="post">
                            <button class="btn btn-success w-100">
                                Thanh toán ngay
                            </button>
                        </form>

                    <?php else: ?>

                        <div class="alert alert-info">
                            Booking này đã được thanh toán.
                        </div>

                    <?php endif; ?>

                    <a href="<?= BASE_URL ?>dashboard.php" class="btn btn-link mt-3">
                        ← Quay lại
                    </a>

                </div>
            </div>

        </div>
    </div>

</body>

</html>