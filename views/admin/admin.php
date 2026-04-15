<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Quản lý Tour') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">Trang chủ</a>
            <div class="ms-auto">
                <span class="me-3">Quyền: <strong><?= htmlspecialchars($user['role'] ?? 'admin') ?></strong></span>
                <a href="logout.php" class="text-decoration-none text-danger">Đăng xuất</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>Trang quản lý</h1>
        <p class="text-muted">Xin chào, <strong><?= htmlspecialchars($user['username'] ?? 'duynguyenmanh') ?></strong>
        </p>

        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">Thêm dữ liệu cho user</div>
                    <div class="card-body">
                        <form method="post" action="index.php?act=add-tour">
                            <div class="mb-3">
                                <label class="form-label">Tiêu đề</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mô tả</label>
                                <textarea class="form-control" name="description" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Lưu dữ liệu</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <span class="mb-0">Danh sách dữ liệu đã thêm</span>

                        <form action="index.php" method="GET" class="d-flex gap-1" style="max-width: 300px;">
                            <input type="hidden" name="act" value="list-tour">
                            <div class="input-group input-group-sm">
                                <input type="text" name="keyword" class="form-control" placeholder="Tìm kiếm..."
                                    value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                                <button type="submit" class="btn btn-light">
                                    <i class="bi bi-search"></i> Tìm
                                </button>
                                <?php if (!empty($_GET['keyword'])): ?>
                                    <a href="index.php?act=list-tour" class="btn btn-outline-light">
                                        <i class="bi bi-x-circle"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 50px;">STT</th>
                                        <th>Tiêu đề</th>
                                        <th>Mô tả</th>
                                        <th>Ngày tạo</th>
                                        <th class="text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($items)):
                                        $stt = 1;
                                        foreach ($items as $item): ?>
                                            <tr>
                                                <td class="text-center"><?= $stt++ ?></td>
                                                <td class="fw-bold"><?= htmlspecialchars($item['title']) ?></td>
                                                <td><?= htmlspecialchars($item['description']) ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($item['created_at'])) ?></td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <a href="index.php?act=tour-detail&id=<?= $item['id'] ?>"
                                                            class="btn btn-info btn-sm text-white">Chi tiết</a>
                                                        <button class="btn btn-warning btn-sm text-white" data-bs-toggle="modal"
                                                            data-bs-target="#editModal<?= $item['id'] ?>">Sửa</button>
                                                        <a href="index.php?act=delete-tour&id=<?= $item['id'] ?>"
                                                            class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Xác nhận xóa?')">Xóa</a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <div class="modal fade" id="editModal<?= $item['id'] ?>" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content text-dark">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Sửa Tour #<?= $item['id'] ?></h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form action="index.php?act=update-tour" method="POST">
                                                            <div class="modal-body text-start">
                                                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Tiêu đề</label>
                                                                    <input type="text" name="title" class="form-control"
                                                                        value="<?= htmlspecialchars($item['title']) ?>"
                                                                        required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Mô tả</label>
                                                                    <textarea name="description" class="form-control" rows="4"
                                                                        required><?= htmlspecialchars($item['description']) ?></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Đóng</button>
                                                                <button type="submit" class="btn btn-primary">Lưu thay
                                                                    đổi</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center p-3">Không tìm thấy dữ liệu nào</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>