<?php
session_start();
require_once __DIR__ . '/configs/env.php';
require_once __DIR__ . '/configs/pdo.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$conn = get_connection();
$user_id = $_SESSION['user']['id'];

// 2. TRUY VẤN DỮ LIỆU
// Sử dụng t.name thay vì t.title để khớp với database của bạn
$sql = "SELECT b.*, t.name as tour_name 
        FROM bookings b 
        JOIN tour_items t ON b.tour_id = t.id 
        WHERE b.user_id = ? 
        ORDER BY b.booking_date DESC";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$history_bookings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Lịch sử booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .badge {
            font-size: 0.85rem;
            padding: 0.5em 0.8em;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, .03);
        }
    </style>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold m-0">📜 Lịch sử booking</h3>
            <a href="index.php" class="btn btn-outline-secondary btn-sm">Quay lại trang chủ</a>
        </div>

        <div class="card shadow-sm border-0 overflow-hidden">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-4">Tour</th>
                        <th>Ngày đặt</th>
                        <th>Số khách</th>
                        <th>Thanh toán</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($history_bookings)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i>Bạn chưa có lịch sử đặt tour nào.</i>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($history_bookings as $bk): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($bk['tour_name']) ?></div>
                                    <small class="text-muted">Mã đơn: #<?= $bk['id'] ?></small>
                                </td>
                                <td><?= date('d/m/Y', strtotime($bk['booking_date'])) ?></td>
                                <td>
                                    <div class="small">
                                        Lớn: <strong><?= $bk['adult_count'] ?? 1 ?></strong> |
                                        Trẻ: <strong><?= $bk['child_count'] ?? 0 ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <?php if (($bk['payment_status'] ?? '') === 'Đã thanh toán'): ?>
                                        <span class="badge bg-success text-white">Đã thanh toán</span>
                                    <?php else: ?>
                                        <a href="payment.php?booking_id=<?= $bk['id'] ?>"
                                            class="btn btn-warning btn-sm fw-bold shadow-sm">
                                            Thanh toán QR
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $status_class = ($bk['status'] ?? '') === 'Đã xác nhận' ? 'bg-info text-dark' : 'bg-secondary text-white';
                                    ?>
                                    <span class="badge <?= $status_class ?>"><?= $bk['status'] ?? 'Chờ xử lý' ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>