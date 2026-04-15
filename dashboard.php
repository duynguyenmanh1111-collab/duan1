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

$pageTitle = 'Dashboard';
$user = $_SESSION['user'];
$items = [];
$error = null;

$conn = get_connection();
if ($conn) {
    ensure_tour_items_table($conn);
    $items = get_tour_items($conn);
} else {
    $error = 'Không thể kết nối tới cơ sở dữ liệu để tải dữ liệu.';
}
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
        <p class="text-muted">Xin chào, <strong><?= htmlspecialchars($user['username']) ?></strong></p>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Nội dung admin vừa thêm</h5>
                        <p class="card-text">Dữ liệu do admin thêm sẽ xuất hiện ở đây cho người dùng xem.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <?php if (empty($items)): ?>
                <div class="col-12">
                    <div class="alert alert-info">Hiện chưa có dữ liệu nào được admin thêm.</div>
                </div>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-primary text-white">
                                <?= htmlspecialchars($item['title']) ?>
                            </div>
                            <div class="card-body">
                                <p class="card-text"><?= nl2br(htmlspecialchars($item['description'])) ?></p>
                            </div>
                            <div class="card-footer text-muted small">
                                Thêm vào: <?= htmlspecialchars(date('d/m/Y H:i', strtotime($item['created_at']))) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>