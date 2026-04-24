<?php
session_start();
require_once __DIR__ . '/configs/env.php';
require_once __DIR__ . '/configs/helper.php';
require_once __DIR__ . '/configs/pdo.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

// Chuyển hướng nếu là admin
if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') {
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
    // Xử lý đặt tour
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'place_booking') {
        $tourId = intval($_POST['tour_id'] ?? 0);
        $bookingDate = trim($_POST['booking_date'] ?? '');
        $quantity = max(1, intval($_POST['quantity'] ?? 1));

        if ($tourId <= 0 || $bookingDate === '') {
            $bookingError = 'Vui lòng chọn tour và ngày khởi hành.';
        } else {
            // Lấy thông tin tour để lấy tên và giá
            $stmt = $conn->prepare("SELECT * FROM tour_items WHERE id = ?");
            $stmt->execute([$tourId]);
            $tour = $stmt->fetch();

            if (!$tour) {
                $bookingError = 'Tour không tồn tại.';
            } else {
                $userId = intval($user['id']);
                $userName = $user['username'] ?? ($user['name'] ?? 'Người dùng');
                $tourTitle = $tour['name'] ?? 'Tour không tên';

                // Gọi hàm thêm booking (Đảm bảo hàm add_booking trong helper.php xử lý đúng số lượng cột)
                if (add_booking($conn, $userId, $userName, $tourId, $tourTitle, $bookingDate, $quantity)) {
                    $message = 'Đặt tour thành công! Admin sẽ liên hệ xác nhận.';
                } else {
                    $bookingError = 'Đặt tour thất bại. Vui lòng thử lại.';
                }
            }
        }
    }

    // Lấy danh sách tour - Sắp xếp mới nhất lên đầu
    $items = $conn->query("SELECT * FROM tour_items ORDER BY id DESC")->fetchAll();
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
            --secondary-color: #f7f9fa;
        }

        body {
            background-color: var(--secondary-color);
            font-family: -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        .navbar {
            background: #fff !important;
            box-shadow: 0 1px 7px rgba(0, 0, 0, 0.05);
        }

        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
        }

        .hero-slider {
            position: relative;
            height: 400px;
            overflow: hidden;
            border-radius: 0 0 20px 20px;
        }

        .hero-slide {
            position: absolute;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

        .hero-slide.active {
            opacity: 1;
        }

        .hero-overlay {
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }

        .search-container {
            position: relative;
            z-index: 10;
            margin-top: -70px;
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .btn-search {
            background-color: #ff5e1f;
            color: white;
            border-radius: 10px;
            font-weight: bold;
        }

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

        .tour-img-container {
            height: 180px;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .tour-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .price-tag {
            color: #ff5e1f;
            font-weight: bold;
            font-size: 1.2rem;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-light bg-white shadow-sm px-2">
        <div class="d-flex align-items-center w-100">
            <button class="btn btn-outline-primary me-2" data-bs-toggle="offcanvas" data-bs-target="#menuSidebar"
                style="margin-left:5px;">
                ☰
            </button>
            <span class="navbar-brand mb-0 fw-bold text-primary">VIE Travel</span>
            <div class="ms-auto d-flex align-items-center">
                <span class="me-3 d-none d-md-block text-muted small">Chào,
                    <?= htmlspecialchars($user['username']) ?></span>
                <a href="<?= BASE_URL ?>logout.php" class="btn btn-outline-primary btn-sm rounded-pill px-4">Đăng
                    xuất</a>
            </div>
        </div>
    </nav>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="menuSidebar">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <a href="dashboard.php" class="btn btn-outline-primary w-100 mb-2">Trang chủ</a>
            <a href="booking.php" class="btn btn-outline-success w-100 mb-2">Lịch sử booking</a>
            <a href="payment.php" class="btn btn-outline-warning w-100 mb-2">Thanh toán</a>
        </div>
    </div>

    <header class="hero-slider">
        <div class="hero-slide active"
            style="background-image: url('https://images.unsplash.com/photo-1506929113675-b55f248b6d33?auto=format&fit=crop&w=1350&q=80');">
        </div>
        <div class="hero-slide"
            style="background-image: url('https://images.unsplash.com/photo-1493558103817-58b2924bce98?auto=format&fit=crop&w=1350&q=80');">
        </div>
        <div class="hero-slide"
            style="background-image: url('https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=1350&q=80');">
        </div>
        <div class="hero-overlay">
            <div>
                <h1 class="fw-bold display-4 text-white">Bạn muốn đi đâu?</h1>
                <p class="lead text-white">Khám phá hàng ngàn Tour du lịch hấp dẫn với giá tốt nhất.</p>
            </div>
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
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($bookingError): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($bookingError) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <?php if (empty($items)): ?>
                <div class="col-12 text-center py-5">
                    <img src="https://cdn-icons-png.flaticon.com/512/1053/1053210.png" width="80" class="opacity-25 mb-3"
                        alt="No data">
                    <p class="text-muted">Hiện chưa có tour nào khả dụng. Vui lòng quay lại sau!</p>
                </div>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card tour-card h-100 shadow-sm">
                            <div class="tour-img-container">
                                <?php
                                // FIX LỖI ẢNH: Kiểm tra và hiển thị ảnh từ thư mục uploads
                                $imageName = $item['image'] ?? '';
                                if (!empty($imageName)): ?>
                                    <img src="uploads/<?= htmlspecialchars($imageName) ?>"
                                        alt="<?= htmlspecialchars($item['name']) ?>"
                                        onerror="this.src='https://placehold.co/600x400?text=No+Image'">
                                <?php else: ?>
                                    <i class="fa-regular fa-image fa-3x text-light"></i>
                                <?php endif; ?>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <span class="badge bg-info mb-2 align-self-start">
                                    <?= htmlspecialchars($item['category'] ?? 'Du lịch') ?>
                                </span>
                                <h6 class="card-title fw-bold text-truncate" title="<?= htmlspecialchars($item['name']) ?>">
                                    <?= htmlspecialchars($item['name']) ?>
                                </h6>
                                <p class="card-text text-muted small mb-3"
                                    style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?= htmlspecialchars($item['description'] ?? '') ?>
                                </p>

                                <div class="price-tag mb-3">
                                    <?= number_format($item['price_adult'] ?? 0, 0, ',', '.') ?>đ
                                </div>

                                <form method="post" class="mt-auto">
                                    <input type="hidden" name="action" value="place_booking">
                                    <input type="hidden" name="tour_id" value="<?= $item['id'] ?>">
                                    <div class="mb-2">
                                        <label class="small text-muted">Ngày khởi hành:</label>
                                        <input type="date" name="booking_date" class="form-control form-control-sm" required
                                            min="<?= date('Y-m-d') ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="small text-muted">Số lượng khách:</label>
                                        <input type="number" name="quantity" class="form-control form-control-sm" min="1"
                                            value="1" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm w-100">Đặt tour ngay</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <footer class="bg-white py-4 mt-5 border-top">
        <div class="container text-center text-muted small">
            &copy; 2026 VIE Travel Project. Code by DUY THUAN DAT.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Hero Slider Logic
        const slides = document.querySelectorAll('.hero-slide');
        let current = 0;
        if (slides.length > 0) {
            setInterval(() => {
                slides[current].classList.remove('active');
                current = (current + 1) % slides.length;
                slides[current].classList.add('active');
            }, 4000);
        }
    </script>
</body>

</html>