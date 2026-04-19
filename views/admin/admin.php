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

        <?php if (!empty($message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">Thêm dữ liệu cho user</div>
                    <div class="card-body">
                        <!-- 🔥 thêm enctype -->
                        <form method="post" action="admin.php" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="add_tour">

                            <div class="mb-3">
                                <label class="form-label">Tiêu đề</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Mô tả</label>
                                <textarea class="form-control" name="description" rows="5" required></textarea>
                            </div>

                            <!-- ✅ THÊM -->
                            <div class="mb-3">
                                <label class="form-label">Giá tour</label>
                                <input type="number" class="form-control" name="price">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Danh mục</label>
                                <select class="form-select" name="category">
                                    <option value="Nội địa">Nội địa</option>
                                    <option value="Quốc tế">Quốc tế</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Hình ảnh</label>
                                <input type="file" class="form-control" name="image">
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

                                        <!-- ✅ THÊM -->
                                        <th>Danh mục</th>
                                        <th>Giá</th>
                                        <th>Ảnh</th>

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

                                                <!-- ✅ HIỂN THỊ -->
                                                <td><?= htmlspecialchars($item['category'] ?? '') ?></td>
                                                <td><?= number_format($item['price'] ?? 0) ?>đ</td>
                                                <td>
                                                    <?php if (!empty($item['image'])): ?>
                                                        <img src="uploads/<?= $item['image'] ?>" width="60">
                                                    <?php endif; ?>
                                                </td>

                                                <td><?= date('d/m/Y H:i', strtotime($item['created_at'])) ?></td>

                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <a href="index.php?act=tour-detail&id=<?= $item['id'] ?>"
                                                            class="btn btn-info btn-sm text-white">Chi tiết</a>

                                                        <button class="btn btn-warning btn-sm text-white" data-bs-toggle="modal"
                                                            data-bs-target="#editModal<?= $item['id'] ?>">Sửa</button>

                                                        <a href="index.php?act=delete-tour&id=<?= $item['id'] ?>"
                                                            class="btn btn-danger btn-sm">Xóa</a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- MODAL -->
                                            <div class="modal fade" id="editModal<?= $item['id'] ?>">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="index.php?act=update-tour" method="POST"
                                                            enctype="multipart/form-data">
                                                            <div class="modal-body">

                                                                <input type="hidden" name="id" value="<?= $item['id'] ?>">

                                                                <input type="text" name="title" value="<?= $item['title'] ?>"
                                                                    class="form-control mb-2">

                                                                <textarea name="description"
                                                                    class="form-control mb-2"><?= $item['description'] ?></textarea>

                                                                <!-- ✅ THÊM -->
                                                                <input type="number" name="price"
                                                                    value="<?= $item['price'] ?? '' ?>"
                                                                    class="form-control mb-2">

                                                                <select name="category" class="form-select mb-2">
                                                                    <option <?= ($item['category'] ?? '') == 'Nội địa' ? 'selected' : '' ?>>Nội địa</option>
                                                                    <option <?= ($item['category'] ?? '') == 'Quốc tế' ? 'selected' : '' ?>>Quốc tế</option>
                                                                </select>

                                                                <input type="file" name="image" class="form-control">

                                                            </div>

                                                            <div class="modal-footer">
                                                                <button class="btn btn-primary">Lưu</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                        <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- giữ nguyên phần booking của bạn -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <span class="mb-0">Danh sách booking</span>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($bookings)): ?>
                            <div class="p-4 text-center text-muted">Chưa có booking nào.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Khách hàng</th>
                                            <th>Tour</th>
                                            <th>Ngày khởi hành</th>
                                            <th>Số khách</th>
                                            <th>Thanh toán</th>
                                            <th>Trạng thái</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($bookings as $booking): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($booking['user_name'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($booking['tour_title'] ?? '') ?></td>
                                                <td><?= htmlspecialchars(date('d/m/Y', strtotime($booking['booking_date'] ?? ''))) ?>
                                                </td>
                                                <td><?= htmlspecialchars((string) ($booking['quantity'] ?? '')) ?></td>
                                                <td>
                                                    <span
                                                        class="badge <?= ($booking['payment_status'] ?? '') === 'Đã thanh toán' ? 'bg-success' : 'bg-secondary text-white' ?>">
                                                        <?= htmlspecialchars($booking['payment_status'] ?? 'Chưa thanh toán') ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge <?= ($booking['status'] ?? '') === 'Đã xác nhận' ? 'bg-success' : (($booking['status'] ?? '') === 'Đã hủy' ? 'bg-danger' : 'bg-warning text-dark') ?>">
                                                        <?= htmlspecialchars($booking['status'] ?? '') ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <!-- FORM UPDATE STATUS (giữ nguyên) -->
                                                    <form method="post" class="d-flex gap-1 mb-1">
                                                        <input type="hidden" name="action" value="update_booking_status">
                                                        <input type="hidden" name="booking_id"
                                                            value="<?= htmlspecialchars($booking['id']) ?>">

                                                        <button type="submit" name="status" value="Chờ xác nhận"
                                                            class="btn btn-sm btn-outline-secondary">Chờ</button>

                                                        <button type="submit" name="status" value="Đã xác nhận"
                                                            class="btn btn-sm btn-outline-success">Xác nhận</button>

                                                        <button type="submit" name="status" value="Đã hủy"
                                                            class="btn btn-sm btn-outline-danger">Hủy</button>
                                                    </form>

                                                    <!-- 🔥 THÊM FORM XÓA -->
                                                    <form method="post"
                                                        onsubmit="return confirm('Bạn có chắc muốn xóa booking này?')">

                                                        <input type="hidden" name="action" value="delete_booking">
                                                        <input type="hidden" name="booking_id"
                                                            value="<?= htmlspecialchars($booking['id']) ?>">

                                                        <button type="submit" class="btn btn-sm btn-danger w-100">
                                                            Xóa
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>