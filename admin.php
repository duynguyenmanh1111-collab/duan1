<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/configs/env.php';
require_once __DIR__ . '/configs/helper.php';
require_once __DIR__ . '/configs/pdo.php';

require_admin();

$conn = get_connection();
$errors = [];
$message = $_GET['msg'] ?? null;

// 1. XỬ LÝ DỮ LIỆU (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_tour' || $action === 'edit_tour') {
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = $_POST['category'] ?? 'Nội địa';
        $price_adult = intval($_POST['price_adult'] ?? 0);
        $price_child = intval($_POST['price_child'] ?? 0);
        $price_baby = intval($_POST['price_baby'] ?? 0);
        $old_image = $_POST['old_image'] ?? '';

        $image = $old_image;
        if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] === 0) {
            $image = time() . '_' . $_FILES['image_upload']['name'];
            move_uploaded_file($_FILES['image_upload']['tmp_name'], __DIR__ . '/uploads/' . $image);
        }

        if ($id > 0) {
            $sql = "UPDATE tour_items SET name=?, description=?, image=?, category=?, price_adult=?, price_child=?, price_baby=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([$name, $description, $image, $category, $price_adult, $price_child, $price_baby, $id]);
        } else {
            $sql = "INSERT INTO tour_items (name, description, image, category, price_adult, price_child, price_baby) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([$name, $description, $image, $category, $price_adult, $price_child, $price_baby]);
        }
        header("Location: admin.php?tab=tour&msg=success");
        exit();
    }

    if ($action === 'delete_tour') {
        $stmt = $conn->prepare("DELETE FROM tour_items WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        header("Location: admin.php?tab=tour&msg=deleted");
        exit();
    }

    if ($action === 'update_booking_status') {
        $booking_id = intval($_POST['booking_id']);
        $new_status = $_POST['status'];
        $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $booking_id]);
        header("Location: admin.php?tab=booking&msg=updated");
        exit();
    }
}

// 2. LẤY DỮ LIỆU
$tours = $conn->query("SELECT * FROM tour_items ORDER BY id DESC")->fetchAll();
$bookings = $conn->query("SELECT * FROM bookings ORDER BY id DESC")->fetchAll();

$editItem = null;
if (isset($_GET['edit_id'])) {
    $stmt = $conn->prepare("SELECT * FROM tour_items WHERE id = ?");
    $stmt->execute([$_GET['edit_id']]);
    $editItem = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản trị hệ thống</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7f6;
        }

        .sidebar {
            min-height: 100vh;
            background: #2c3e50;
            color: white;
            padding: 20px;
            position: fixed;
            width: 16.6%;
        }

        .sidebar a {
            color: #bdc3c7;
            text-decoration: none;
            display: block;
            padding: 12px;
            border-radius: 5px;
            cursor: pointer;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #34495e;
            color: white;
        }

        .main-content {
            margin-left: 16.6%;
            padding: 30px;
        }

        .tab-content {
            display: none;
        }

        .active-tab {
            display: block;
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 p-0">
                <div class="sidebar shadow">
                    <h4 class="text-center mb-4">VIE Travel</h4>
                    <hr>
                    <a onclick="showTab('tour')" id="btn-tour">📦 Quản lý Tour</a>
                    <a onclick="showTab('add')" id="btn-add">➕ <?= $editItem ? 'Sửa Tour' : 'Thêm Tour' ?></a>
                    <a onclick="showTab('booking')" id="btn-booking">📝 Quản lý Booking</a>
                    <hr>
                    <a href="index.php">🏠 Trang chủ</a>
                </div>
            </div>

            <div class="col-md-10 main-content">
                <?php if ($message): ?>
                    <div class="alert alert-success">Thao tác thành công!</div>
                <?php endif; ?>

                <div id="tour"
                    class="tab-content <?= (!isset($_GET['edit_id']) && ($_GET['tab'] ?? 'tour') == 'tour') ? 'active-tab' : '' ?>">
                    <h3 class="mb-4">Danh sách Tour</h3>
                    <table class="table table-hover bg-white shadow-sm rounded">
                        <thead class="table-dark">
                            <tr>
                                <th>Ảnh</th>
                                <th>Tên Tour</th>
                                <th>Giá Người Lớn</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tours as $t): ?>
                                <tr>
                                    <td><img src="uploads/<?= $t['image'] ?>" width="50" class="rounded"></td>
                                    <td><?= htmlspecialchars($t['name']) ?></td>
                                    <td><?= number_format($t['price_adult']) ?>đ</td>
                                    <td>
                                        <a href="admin.php?edit_id=<?= $t['id'] ?>"
                                            class="btn btn-sm btn-info text-white">Sửa</a>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Xóa tour này?')">
                                            <input type="hidden" name="action" value="delete_tour">
                                            <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                            <button class="btn btn-sm btn-danger">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div id="booking" class="tab-content <?= (($_GET['tab'] ?? '') == 'booking') ? 'active-tab' : '' ?>">
                    <h3 class="mb-4">Danh sách Đơn hàng</h3>
                    <table class="table bg-white shadow-sm">
                        <thead class="table-primary">
                            <tr>
                                <th>Khách hàng</th>
                                <th>Tour</th>
                                <th>Số lượng</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $b): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($b['user_name'] ?? 'Khách') ?></strong><br>
                                        <small><?= $b['user_phone'] ?? '' ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($b['tour_title'] ?? 'N/A') ?></td>
                                    <td>
                                        <?= $b['adult_count'] ?? 0 ?>L -
                                        <?= $b['child_count'] ?? 0 ?>N -
                                        <?= $b['baby_count'] ?? 0 ?>B
                                    </td>
                                    <td class="text-danger fw-bold"><?= number_format($b['amount'] ?? 0) ?>đ</td>
                                    <td>
                                        <form method="POST">
                                            <input type="hidden" name="action" value="update_booking_status">
                                            <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                            <select name="status" class="form-select form-select-sm"
                                                onchange="this.form.submit()">
                                                <option value="Chờ xác nhận" <?= ($b['status'] ?? '') == 'Chờ xác nhận' ? 'selected' : '' ?>>Chờ xác nhận</option>
                                                <option value="Đã xác nhận" <?= ($b['status'] ?? '') == 'Đã xác nhận' ? 'selected' : '' ?>>Đã xác nhận</option>
                                                <option value="Đã hủy" <?= ($b['status'] ?? '') == 'Đã hủy' ? 'selected' : '' ?>>Đã hủy</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div id="add"
                    class="tab-content <?= (isset($_GET['edit_id']) || ($_GET['tab'] ?? '') == 'add') ? 'active-tab' : '' ?>">
                    <h3><?= $editItem ? 'Cập nhật Tour' : 'Thêm mới Tour' ?></h3>
                    <form method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm border-0">
                        <input type="hidden" name="action" value="<?= $editItem ? 'edit_tour' : 'add_tour' ?>">
                        <input type="hidden" name="id" value="<?= $editItem['id'] ?? 0 ?>">
                        <input type="hidden" name="old_image" value="<?= $editItem['image'] ?? '' ?>">
                        <div class="mb-3"><label class="fw-bold">Tên tour</label><input type="text" name="title"
                                class="form-control" value="<?= $editItem['name'] ?? '' ?>" required></div>
                        <div class="row mb-3">
                            <div class="col"><label>Giá người lớn</label><input type="number" name="price_adult"
                                    class="form-control" value="<?= $editItem['price_adult'] ?? 0 ?>"></div>
                            <div class="col"><label>Giá trẻ em</label><input type="number" name="price_child"
                                    class="form-control" value="<?= $editItem['price_child'] ?? 0 ?>"></div>
                            <div class="col"><label>Giá em bé</label><input type="number" name="price_baby"
                                    class="form-control" value="<?= $editItem['price_baby'] ?? 0 ?>"></div>
                        </div>
                        <div class="mb-3"><label class="fw-bold">Ảnh đại diện</label><input type="file"
                                name="image_upload" class="form-control"></div>
                        <div class="mb-3"><label class="fw-bold">Mô tả</label><textarea name="description"
                                class="form-control" rows="3"><?= $editItem['description'] ?? '' ?></textarea></div>
                        <button type="submit" class="btn btn-primary px-5">Lưu thông tin</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active-tab'));
            document.getElementById(tabId).classList.add('active-tab');
            const url = new URL(window.location);
            url.searchParams.set('tab', tabId);
            window.history.pushState({}, '', url);
        }
    </script>
</body>

</html>