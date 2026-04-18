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

        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if ($bookingError): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($bookingError) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <h5 class="card-title">Nội dung admin vừa thêm</h5>
                <p class="card-text">Những nội dung này được admin tạo và lưu trong cơ sở dữ liệu.</p>
            </div>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-header bg-success text-white">Lịch sử đặt tour của bạn</div>
            <div class="card-body p-0">
                <?php if (empty($bookings)): ?>
                    <div class="p-4 text-center text-muted">Bạn chưa đặt tour nào.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
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
                                        <td><?= htmlspecialchars($booking['tour_title']) ?></td>
                                        <td><?= htmlspecialchars(date('d/m/Y', strtotime($booking['booking_date']))) ?></td>
                                        <td><?= htmlspecialchars($booking['quantity']) ?></td>
                                        <td>
                                            <div>
                                                <span
                                                    class="badge <?= ($booking['payment_status'] ?? '') === 'Đã thanh toán' ? 'bg-success' : 'bg-secondary text-white' ?>">
                                                    <?= htmlspecialchars($booking['payment_status'] ?? 'Chưa thanh toán') ?></span>
                                            </div>
                                            <?php if (($booking['payment_status'] ?? '') !== 'Đã thanh toán'): ?>
                                                <div class="mt-1">
                                                    <a href="<?= BASE_URL ?>payment.php?booking_id=<?= htmlspecialchars($booking['id']) ?>"
                                                        class="btn btn-sm btn-warning">Thanh toán</a>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span
                                                class="badge <?= $booking['status'] === 'Đã xác nhận' ? 'bg-success' : ($booking['status'] === 'Đã hủy' ? 'bg-danger' : 'bg-warning text-dark') ?>">
                                                <?= htmlspecialchars($booking['status']) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
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
                                <form method="post" action="<?= BASE_URL ?>user.php" class="mt-3">
                                    <input type="hidden" name="action" value="place_booking">
                                    <input type="hidden" name="tour_id" value="<?= htmlspecialchars($item['id']) ?>">
                                    <div class="row g-2">
                                        <div class="col-7">
                                            <label class="form-label small">Ngày khởi hành</label>
                                            <input type="date" name="booking_date" class="form-control form-control-sm"
                                                required>
                                        </div>
                                        <div class="col-5">
                                            <label class="form-label small">Số khách</label>
                                            <input type="number" name="quantity" class="form-control form-control-sm" min="1"
                                                value="1" required>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm mt-3 w-100">Đặt tour</button>
                                </form>
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