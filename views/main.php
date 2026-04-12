<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Trang chủ') ?></title>
    <base href="<?= htmlspecialchars(BASE_URL) ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>">Trang chủ</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>login.php?action=logout">Đăng xuất</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="card-title"><?= htmlspecialchars($title ?? 'Trang chủ') ?></h1>

                <?php if (isset($_SESSION['user'])): ?>
                    <p class="lead">Xin chào, <strong><?= htmlspecialchars($user['username'] ?? 'Khách') ?></strong>!</p>
                    <p>Bạn đã đăng nhập và đang ở trang chủ.</p>
                <?php else: ?>
                    <p class="lead">Bạn chưa đăng nhập.</p>
                    <p>
                        <a href="<?= BASE_URL ?>login.php" class="btn btn-primary me-2">Đăng nhập</a>
                        <a href="<?= BASE_URL ?>register.php" class="btn btn-secondary">Đăng ký</a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>