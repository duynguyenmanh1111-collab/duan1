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

    <div class="container mt-5">
        <h1><?= htmlspecialchars($pageTitle) ?></h1>
        <p class="text-muted">Xin chào, <strong><?= htmlspecialchars($user['username']) ?></strong></p>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <h5 class="card-title">Nội dung admin vừa thêm</h5>
                <p class="card-text">Những nội dung này được admin tạo và lưu trong cơ sở dữ liệu.</p>
            </div>
        </div>

        <div class="row mt-4">
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
                                Thêm lúc: <?= htmlspecialchars(date('d/m/Y H:i', strtotime($item['created_at']))) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>