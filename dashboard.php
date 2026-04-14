<?php
session_start();
require_once __DIR__ . '/configs/env.php';
require_once __DIR__ . '/configs/helper.php';

if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

if ($_SESSION['user']['role'] === 'admin') {
    header('Location: ' . BASE_URL . 'admin.php');
    exit;
}

$pageTitle = 'Dashboard';
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>">Trang chủ</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item me-3">
                        <span class="navbar-text">Quyền: <strong><?= htmlspecialchars($user['role']) ?></strong></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>logout.php">Đăng xuất</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1><?= htmlspecialchars($pageTitle) ?></h1>
        <div class="container-fluid py-4">
            <h2 class="mb-4">Hệ thống Quản trị Du lịch của VIE TRAVEL</h2>

            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div
                        class="card border-left-primary shadow h-100 py-2 border-0 border-start border-primary border-4">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng số Tour
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">128 Tour</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div
                        class="card border-left-success shadow h-100 py-2 border-0 border-start border-success border-4">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Booking mới
                                        (24h)
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">12 đơn</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2 border-0 border-start border-info border-4">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Doanh thu tháng
                                        này
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">450.000.000đ</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div
                        class="card border-left-warning shadow h-100 py-2 border-0 border-start border-warning border-4">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Phản hồi chờ
                                        xử lý
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">8 tin nhắn</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-8 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Danh sách Booking mới nhất</h6>
                            <a href="#" class="btn btn-sm btn-primary">Xem tất cả</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Khách hàng</th>
                                            <th>Tên Tour</th>
                                            <th>Khởi hành</th>
                                            <th>Trạng thái</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Lê Văn C</td>
                                            <td>Tour Đà Nẵng 3N2Đ</td>
                                            <td>25/12/2024</td>
                                            <td><span class="badge bg-warning text-dark">Chờ thanh toán</span></td>
                                            <td><button class="btn btn-sm btn-outline-info">Chi tiết</button></td>
                                        </tr>
                                        <tr>
                                            <td>Phạm Thị D</td>
                                            <td>Tour Sapa - Fansipan</td>
                                            <td>01/01/2025</td>
                                            <td><span class="badge bg-success">Đã xác nhận</span><s /td>
                                            <td><button class="btn btn-sm btn-outline-info">Chi tiết</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-white py-3 text-primary font-weight-bold">
                            Top Tour bán chạy
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Phú Quốc - Đảo Ngọc
                                <span class="badge bg-primary rounded-pill">45 lượt</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Hạ Long - Ngủ đêm trên vịnh
                                <span class="badge bg-primary rounded-pill">38 lượt</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Đà Lạt - Thành phố mộng mơ
                                <span class="badge bg-primary rounded-pill">32 lượt</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>