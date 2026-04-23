<?php
session_start();
require_once __DIR__ . '/configs/env.php';
require_once __DIR__ . '/configs/helper.php';
require_once __DIR__ . '/configs/pdo.php';

if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

$user = $_SESSION['user'];

$conn = get_connection();
$bookings = [];

if ($conn) {
    $bookings = get_user_bookings($conn, intval($user['id']));
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Lịch sử booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <h3 class="mb-4">📜 Lịch sử booking</h3>

        <?php if (empty($bookings)): ?>
            <div class="alert alert-info">Bạn chưa có booking nào.</div>
        <?php else: ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tour</th>
                        <th>Ngày</th>
                        <th>Số khách</th>
                        <th>Thanh toán</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $b): ?>
                        <tr>
                            <td><?= $b['tour_title'] ?></td>
                            <td><?= $b['booking_date'] ?></td>
                            <td><?= $b['quantity'] ?></td>
                            <td><?= $b['payment_status'] ?></td>
                            <td><?= $b['status'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="dashboard.php" class="btn btn-primary mt-3">⬅ Quay lại trang chính</a>
    </div>

</body>

</html>