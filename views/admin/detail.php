<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= htmlspecialchars($pageTitle) ?>
    </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/icons/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>?act=list-tour"><i class="bi bi-arrow-left"></i> Quay lại danh
                sách</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow border-0">
                    <div class="card-header bg-primary text-white py-3">
                        <h3 class="card-title mb-0">Chi tiết Tour #
                            <?= $tour['id'] ?>
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="text-muted small text-uppercase fw-bold">Tiêu đề Tour</label>
                            <h2 class="text-dark">
                                <?= htmlspecialchars($tour['title']) ?>
                            </h2>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <label class="text-muted small text-uppercase fw-bold">Mô tả lịch trình</label>
                            <div class="mt-2 p-3 bg-white border rounded"
                                style="white-space: pre-wrap; line-height: 1.6;">
                                <?= htmlspecialchars($tour['description']) ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold">Ngày tạo</label>
                                <p><i class="bi bi-calendar-check"></i>
                                    <?= date('d/m/Y H:i', strtotime($tour['created_at'])) ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold">Trạng thái</label>
                                <p><span class="badge bg-success">Đang hoạt động</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white py-3 d-flex justify-content-end gap-2">
                        <a href="<?= BASE_URL ?>?act=list-tour" class="btn btn-secondary">
                            <i class="bi bi-list-ul"></i> Danh sách
                        </a>
                        <button class="btn btn-warning" onclick="history.back()">
                            <i class="bi bi-pencil-square"></i> Chỉnh sửa
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>