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
                        <span class="navbar-text">Quyền :<strong><?= htmlspecialchars($user['role']) ?></strong></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>logout.php">Đăng xuất</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1><?= htmlspecialchars($pageTitle) ?></h1>
        <p class="text-muted">Xin chào, <strong><?= htmlspecialchars($user['username']) ?></strong></p>
        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <h5 class="card-title">Khu vực quản lý admin</h5>
                <p class="card-text">Trang quản lý dành cho tài khoản admin.</p>
                <p>Cup C1</p>
            </div>
        </div>
    </div>
</body>

</html>