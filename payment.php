<?php
session_start();
require_once __DIR__ . '/configs/env.php';
require_once __DIR__ . '/configs/pdo.php';

if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

// check id
if (!isset($_GET['booking_id']) || empty($_GET['booking_id'])) {
    die("Thanh toán không hợp lệ (thiếu ID)");
}

$booking_id = intval($_GET['booking_id']);
$conn = get_connection();

// lấy booking
$stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch();

if (!$booking) {
    die("Không tìm thấy booking");
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("UPDATE bookings SET payment_status='Đã thanh toán' WHERE id=?");
    $stmt->execute([$booking_id]);

    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thanh toán</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-5">

    <h3>Thanh toán tour</h3>

    <div class="card p-4">
        <p><strong>Tour:</strong> <?= $booking['tour_title'] ?></p>
        <p><strong>Ngày:</strong> <?= $booking['booking_date'] ?></p>
        <p><strong>Số khách:</strong> <?= $booking['quantity'] ?></p>

        <form method="post">
            <button class="btn btn-success">Xác nhận thanh toán</button>
        </form>
    </div>

</body>

</html>