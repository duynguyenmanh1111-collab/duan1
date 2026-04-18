<?php
session_start();
require_once __DIR__ . '/configs/env.php';
require_once __DIR__ . '/configs/helper.php';
require_once __DIR__ . '/configs/pdo.php';

if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

if ($_SESSION['user']['role'] === 'admin') {
    header('Location: ' . BASE_URL . 'admin.php');
    exit;
}

$user = $_SESSION['user'];
$items = [];
$bookings = [];
$message = null;
$bookingError = null;
$error = null;

$conn = get_connection();
if ($conn) {
    ensure_tour_items_table($conn);
    ensure_bookings_table($conn);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'place_booking') {
        $tourId = intval($_POST['tour_id'] ?? 0);
        $bookingDate = trim($_POST['booking_date'] ?? '');
        $quantity = max(1, intval($_POST['quantity'] ?? 1));

        if ($tourId <= 0 || $bookingDate === '') {
            $bookingError = 'Vui lòng chọn tour và ngày khởi hành.';
        } else {
            $tour = get_tour_by_id($conn, $tourId);
            if (!$tour) {
                $bookingError = 'Tour không tồn tại.';
            } else {
                $userId = intval($user['id']);
                $userName = $user['username'] ?? ($user['name'] ?? 'Người dùng');
                if (add_booking($conn, $userId, $userName, $tourId, $tour['title'], $bookingDate, $quantity)) {
                    $message = 'Đặt tour thành công! Admin sẽ liên hệ xác nhận.';
                } else {
                    $bookingError = 'Đặt tour thất bại. Vui lòng thử lại.';
                }
            }
        }
    }

    $items = get_tour_items($conn);
    $bookings = get_user_bookings($conn, intval($user['id']));
} else {
    $error = 'Không thể kết nối cơ sở dữ liệu.';
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel - Khám phá thế giới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0194f3;
            /* Xanh Traveloka */
            --secondary-color: #f7f9fa;
        }

        body {
            background-color: var(--secondary-color);
            font-family: -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto;
        }

        /* Navbar */
        .navbar {
            background: #fff !important;
            box-shadow: 0 1px 7px rgba(0, 0, 0, 0.05);
        }

        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)),
                url('https://images.unsplash.com/photo-1506929113675-b55f248b6d33?auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            border-radius: 0 0 20px 20px;
        }

        /* Search Bar */
        .search-container {
            margin-top: -50px;
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .btn-search {
            background-color: #ff5e1f;
            /* Cam nổi bật */
            color: white;
            border-radius: 10px;
            font-weight: bold;
        }

        /* Tour Cards */
        .tour-card {
            border: none;
            border-radius: 12px;
            transition: transform 0.3s;
            overflow: hidden;
            background: #fff;
        }

        .tour-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .tour-img {
            height: 180px;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ccc;
        }

        .price-tag {
            color: #ff5e1f;
            font-weight: bold;
            font-size: 1.2rem;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fa-solid fa-paper-plane"></i> VIE travele</a>
            <div class="ms-auto d-flex align-items-center">
                <span class="me-3 d-none d-md-block text-muted small">Chào,
                    <?= htmlspecialchars($user['username']) ?></span>
                <a href="<?= BASE_URL ?>logout.php" class="btn btn-outline-primary btn-sm rounded-pill px-4">Đăng
                    xuất</a>
            </div>
        </div>
    </nav>

    <header class="hero">
        <div class="container">
            <h1 class="fw-bold display-4">Bạn muốn đi đâu?</h1>
            <p class="lead">Khám phá hàng ngàn Tour du lịch hấp dẫn với giá tốt nhất.</p>
        </div>
    </header>

    <div class="container">
        <div class="search-container">
            <div class="row g-2">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i
                                class="fa fa-location-dot text-primary"></i></span>
                        <input type="text" class="form-control border-start-0"
                            placeholder="Thành phố, địa điểm du lịch...">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i
                                class="fa fa-calendar text-primary"></i></span>
                        <input type="date" class="form-control border-start-0">
                    </div>
                </div>
                <div class="col-md-3 d-grid">
                    <button class="btn btn-search shadow-sm">Tìm kiếm</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5 pt-3">
        <h3 class="fw-bold mb-4">Tour du lịch nổi bật</h3>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if ($bookingError): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($bookingError) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="row">
            <?php if (empty($items)): ?>
                <div class="col-12 text-center py-5">
                    <img src="https://cdn-icons-png.flaticon.com/512/1053/1053210.png" width="80" class="opacity-25 mb-3">
                    <p class="text-muted">Hiện chưa có tour nào khả dụng. Vui lòng quay lại sau!</p>
                </div>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card tour-card h-100 shadow-sm">
                            <div class="tour-img">
                                <i class="fa-regular fa-image fa-3x"></i>
                            </div>
                            <div class="card-body">
                                <span class="badge bg-light text-primary mb-2">Tour trọn gói</span>
                                <h6 class="card-title fw-bold text-truncate"><?= htmlspecialchars($item['title']) ?></h6>
                                <p class="card-text text-muted small mb-3"
                                    style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?= htmlspecialchars($item['description']) ?>
                                </p>
                                <div class="mb-3">
                                    <div class="price-tag">2.500.000đ</div>
                                </div>
                                <form method="post" action="<?= BASE_URL ?>dashboard.php" class="mb-2">
                                    <input type="hidden" name="action" value="place_booking">
                                    <input type="hidden" name="tour_id" value="<?= htmlspecialchars($item['id']) ?>">
                                    <div class="mb-2">
                                        <input type="date" name="booking_date" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="mb-2">
                                        <input type="number" name="quantity" class="form-control form-control-sm" min="1"
                                            value="1" required placeholder="Số khách">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm w-100">Đặt tour</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (!empty($bookings)): ?>
            <div class="mt-5">
                <h5 class="fw-bold mb-3">Lịch sử booking của bạn</h5>
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tour</th>
                                        <th>Ngày khởi hành</th>
                                        <th>Số khách</th>
                                        <th>Thanh toán</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bookings as $booking): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($booking['tour_title'] ?? '') ?></td>
                                            <td><?= htmlspecialchars(date('d/m/Y', strtotime($booking['booking_date'] ?? ''))) ?>
                                            </td>
                                            <td><?= htmlspecialchars((string) ($booking['quantity'] ?? '')) ?></td>
                                            <td>
                                                <span
                                                    class="badge <?= ($booking['payment_status'] ?? '') === 'Đã thanh toán' ? 'bg-success' : 'bg-secondary text-white' ?>">
                                                    <?= htmlspecialchars($booking['payment_status'] ?? '') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge <?= ($booking['status'] ?? '') === 'Đã thanh toán' ? 'bg-success' : (($booking['status'] ?? '') === 'Đã hủy' ? 'bg-danger' : 'bg-warning text-dark') ?>">
                                                    <?= htmlspecialchars($booking['status'] ?? '') ?>
                                                </span>
                                                <?php if (($booking['payment_status'] ?? '') !== 'Đã thanh toán'): ?>
                                                    <div class="mt-2">
                                                        <a href="<?= BASE_URL ?>payment.php?booking_id=<?= htmlspecialchars($booking['id']) ?>"
                                                            class="btn btn-sm btn-warning">Thanh toán</a>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <footer class="bg-white py-4 mt-5 border-top">
        <div class="container text-center text-muted small">
            &copy; 2026 VIE travele Project. Code by DUY THUAN DAT.
        </div>
    </footer>

</body>

</html>