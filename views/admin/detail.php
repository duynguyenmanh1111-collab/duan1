<?php
session_start();
require_once __DIR__ . '/configs/env.php';
require_once __DIR__ . '/configs/pdo.php';

$conn = get_connection();
$user = $_SESSION['user'] ?? null;
$tourId = intval($_GET['id'] ?? 0);

// Lấy thông tin chi tiết tour từ database
$stmt = $conn->prepare("SELECT * FROM tour_items WHERE id = ?");
$stmt->execute([$tourId]);
$tour = $stmt->fetch();

if (!$tour) {
    die("Không tìm thấy dữ liệu tour!");
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Tour #<?= $tour['id'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #2c3e50;
            --primary-color: #3498db;
        }

        body {
            background-color: #f4f7f6;
        }

        .sidebar {
            min-height: 100vh;
            background: var(--sidebar-bg);
            color: white;
            padding-top: 20px;
            position: fixed;
            width: inherit;
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
            border-left: 4px solid var(--primary-color);
        }

        .main-content {
            margin-left: 16.66667%;
            padding: 30px;
        }

        .card-custom {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }

        .card-header {
            background: white !important;
            font-weight: bold;
            border-bottom: 1px solid #eee;
            padding: 15px 20px;
            color: #2c3e50;
        }

        .form-label-small {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 5px;
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
                        <hr class="text-secondary">
                    </div>
                    <nav>
                        <?php if ($user && $user['role'] === 'admin'): ?>
                            <a href="admin.php"><i class="fa fa-tachometer-alt me-2"></i> Quản lý Tour</a>
                            <a href="admin.php?tab=booking"><i class="fa fa-shopping-cart me-2"></i> Quản lý Booking</a>
                        <?php else: ?>
                            <a href="dashboard.php"><i class="fa fa-home me-2"></i> Trang chủ</a>
                            <a href="booking.php"><i class="fa fa-history me-2"></i> Tour của tôi</a>
                        <?php endif; ?>
                        <a href="#" class="active"><i class="fa fa-info-circle me-2"></i> Chi Tiết Tour</a>
                        <a href="logout.php" class="text-danger mt-5"><i class="fa fa-sign-out-alt me-2"></i> Đăng
                            xuất</a>
                    </nav>
                </div>
            </div>

            <div class="col-md-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold">Chi Tiết Tour: <?= htmlspecialchars($tour['name']) ?></h3>
                    <?php if ($user && $user['role'] === 'admin'): ?>
                        <button class="btn btn-primary px-4 shadow-sm"><i class="fa fa-save me-2"></i>Cập nhật Tour</button>
                    <?php endif; ?>
                </div>

                <div class="card card-custom">
                    <div class="card-header">#1. Thông Tin Cơ Bản</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label-small">Tên Tour du lịch</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($tour['name']) ?>"
                                    <?= $user['role'] === 'admin' ? '' : 'readonly' ?>>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-small">Mã số Tour</label>
                                <input type="text" class="form-control bg-light" value="TOUR-00<?= $tour['id'] ?>"
                                    readonly>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label-small">Mô tả ngắn</label>
                                <textarea class="form-control" rows="3" <?= $user['role'] === 'admin' ? '' : 'readonly' ?>><?= htmlspecialchars($tour['description'] ?? 'Chưa có mô tả') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-custom">
                    <div class="card-header">#2. Thông Tin Giá Cả (VND)</div>
                    <div class="card-body">
                        <div class="row g-3 text-center">
                            <div class="col-md-4">
                                <label class="form-label-small">Giá Người Lớn</label>
                                <input type="text" class="form-control text-center fw-bold text-danger"
                                    value="<?= number_format($tour['price_adult'] ?? 0) ?>" <?= $user['role'] === 'admin' ? '' : 'readonly' ?>>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-small">Giá Trẻ Em</label>
                                <input type="text" class="form-control text-center fw-bold"
                                    value="<?= number_format($tour['price_child'] ?? 0) ?>" <?= $user['role'] === 'admin' ? '' : 'readonly' ?>>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-small">Giá Em Bé</label>
                                <input type="text" class="form-control text-center"
                                    value="<?= number_format($tour['price_baby'] ?? 0) ?>" <?= $user['role'] === 'admin' ? '' : 'readonly' ?>>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-custom">
                    <div class="card-header">#3. Hình Ảnh & Trạng Thái</div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <img src="uploads/<?= $tour['image'] ?>" class="img-fluid rounded border shadow-sm"
                                    style="max-height: 250px; width: 100%; object-fit: cover;"
                                    onerror="this.src='https://placehold.co/600x300?text=No+Image'">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-small">Trạng thái hiện tại trên hệ thống</label>
                                <select class="form-select mb-3" <?= $user['role'] === 'admin' ? '' : 'disabled' ?>>
                                    <option <?= ($tour['status_tour'] == 'Sắp khởi hành') ? 'selected' : '' ?>>Sắp khởi
                                        hành</option>
                                    <option <?= ($tour['status_tour'] == 'Hết chỗ') ? 'selected' : '' ?>>Hết chỗ</option>
                                </select>
                                <div class="alert alert-info py-2 small">
                                    <i class="fa fa-info-circle me-2"></i> Lưu ý: Chỉ Admin mới có quyền thay đổi trạng
                                    thái này.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-custom">
                    <div class="card-header">#4. Thao Tác Chỉnh Sửa / Đặt Chỗ</div>
                    <div class="card-body text-center">
                        <?php if ($user['role'] === 'admin'): ?>
                            <p class="text-muted">Bạn đang xem tour này dưới quyền Quản trị viên.</p>
                            <button class="btn btn-outline-danger me-2"><i class="fa fa-trash me-2"></i>Xóa Tour</button>
                            <a href="admin.php" class="btn btn-secondary">Quay lại danh sách</a>
                        <?php else: ?>
                            <p class="mb-3">Mọi thông tin đã chính xác? Hãy tiến hành đặt chỗ ngay.</p>
                            <a href="payment.php?id=<?= $tour['id'] ?>" class="btn btn-success btn-lg px-5 shadow-sm">Đặt
                                Tour Ngay</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>