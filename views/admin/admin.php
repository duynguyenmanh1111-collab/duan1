<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../configs/env.php';
require_once __DIR__ . '/../../configs/helper.php';
require_once __DIR__ . '/../../configs/pdo.php';

require_admin();

$conn = get_connection();


if (!function_exists('add_tour')) {
    function add_tour($conn, $name, $description, $price, $category, $image)
    {
        $stmt = $conn->prepare("INSERT INTO tour_items(name, description, price, category, image) VALUES(?,?,?,?,?)");
        return $stmt->execute([$name, $description, $price, $category, $image]);
    }
}

if (!function_exists('update_tour')) {
    function update_tour($conn, $id, $name, $description, $price, $category, $image)
    {
        if ($image) {
            $sql = "UPDATE tour_items SET name=?, description=?, price=?, category=?, image=? WHERE id=?";
            $params = [$name, $description, $price, $category, $image, $id];
        } else {
            $sql = "UPDATE tour_items SET name=?, description=?, price=?, category=? WHERE id=?";
            $params = [$name, $description, $price, $category, $id];
        }
        return $conn->prepare($sql)->execute($params);
    }
}

if (!function_exists('delete_tour')) {
    function delete_tour($conn, $id)
    {
        return $conn->prepare("DELETE FROM tour_items WHERE id=?")->execute([$id]);
    }
}

// Bổ sung hàm lấy danh sách tour nếu chưa có
if (!function_exists('get_tour_items')) {
    function get_tour_items($conn)
    {
        $stmt = $conn->prepare("SELECT * FROM tour_items ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

/* ================= HANDLE POST ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $uploadDir = __DIR__ . '/../../uploads/';
    if (!is_dir($uploadDir))
        mkdir($uploadDir, 0777, true);

    // THÊM TOUR
    if (isset($_POST['action']) && $_POST['action'] === 'add_tour') {
        $img = '';
        if (!empty($_FILES['image']['name'])) {
            $img = time() . '_' . $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $img);
        }
        // Lấy giá trị từ form (name="title") truyền vào cột "name" trong DB
        add_tour($conn, $_POST['title'], $_POST['description'], $_POST['price'], $_POST['category'], $img);

        header("Location: admin.php?tab=tour");
        exit;
    }

    // SỬA TOUR
    if (isset($_POST['action']) && $_POST['action'] === 'update_tour') {
        $img = '';
        if (!empty($_FILES['image']['name'])) {
            $img = time() . '_' . $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $img);
        }
        update_tour($conn, $_POST['id'], $_POST['title'], $_POST['description'], $_POST['price'], $_POST['category'], $img);

        header("Location: admin.php?tab=tour");
        exit;
    }

    // XÓA TOUR
    if (isset($_POST['action']) && $_POST['action'] === 'delete_tour') {
        delete_tour($conn, $_POST['id']);

        header("Location: admin.php?tab=tour");
        exit;
    }
}

$items = get_tour_items($conn);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Admin - Quản lý Tour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar bg-white shadow-sm px-3">
        <button class="btn btn-outline-primary" onclick="toggleSidebar()">☰</button>
        <span class="mx-auto fw-bold text-primary">Trang Quản Lý</span>
        <a href="<?= BASE_URL ?>logout.php" class="btn btn-danger btn-sm">Đăng xuất</a>
    </nav>

    <div id="sidebar"
        style="position: fixed; top:0; left:-250px; width:250px; height:100%; background:#fff; transition:0.3s; padding-top:60px; z-index:999; border-right: 1px solid #ddd;">
        <a href="#" onclick="showTab('home')" class="d-block p-3 border-bottom text-decoration-none text-dark">🏠 Trang
            chủ</a>
        <a href="#" onclick="showTab('add')" class="d-block p-3 border-bottom text-decoration-none text-dark">➕ Thêm
            tour</a>
        <a href="#" onclick="showTab('tour')" class="d-block p-3 border-bottom text-decoration-none text-dark">📦 Danh
            sách tour</a>
        <a href="#" onclick="showTab('booking')" class="d-block p-3 border-bottom text-decoration-none text-dark">📑
            Quản lý booking</a>
    </div>

    <div class="container mt-4">

        <div id="home">
            <div class="text-center mt-5">
                <h2 class="fw-bold text-primary">Chào mừng đến với hệ thống quản trị</h2>
                <p>Hệ thống quản lý của VIE Travel</p>
            </div>
        </div>

        <div id="add" style="display:none;">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">Thêm tour mới</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add_tour">

                        <label class="fw-bold">Tiêu đề tour</label>
                        <input class="form-control mb-2" name="title" required placeholder="Nhập tên tour...">

                        <label class="fw-bold">Mô tả</label>
                        <textarea class="form-control mb-2" name="description" rows="3"></textarea>

                        <label class="fw-bold">Giá (VNĐ)</label>
                        <input type="number" class="form-control mb-2" name="price">

                        <label class="fw-bold">Danh mục</label>
                        <select class="form-control mb-2" name="category">
                            <option value="Nội địa">Nội địa</option>
                            <option value="Quốc tế">Quốc tế</option>
                        </select>

                        <label class="fw-bold">Ảnh đại diện</label>
                        <input type="file" class="form-control mb-3" name="image">

                        <button class="btn btn-success w-100 py-2">Thêm Tour Mới</button>
                    </form>
                </div>
            </div>
        </div>

        <div id="tour" style="display:none;">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <span>Danh sách tour đang có</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tên Tour</th>
                                <th>Giá</th>
                                <th>Ảnh</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($items)): ?>
                                <tr>
                                    <td colspan="4" class="text-center p-4 text-muted">Chưa có dữ liệu tour nào.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td class="align-middle fw-semibold">
                                            <?= htmlspecialchars($item['name'] ?? $item['title'] ?? 'Chưa có tên') ?>
                                        </td>
                                        <td class="align-middle text-danger fw-bold">
                                            <?= number_format((float) ($item['price'] ?? 0)) ?>đ
                                        </td>
                                        <td class="align-middle">
                                            <?php if (!empty($item['image'])): ?>
                                                <img src="<?= BASE_URL ?>uploads/<?= $item['image'] ?>" width="60"
                                                    class="rounded shadow-sm">
                                            <?php else: ?>
                                                <span class="text-muted small">Không ảnh</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="align-middle text-center">
                                            <form method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa tour này?');"
                                                style="display:inline;">
                                                <input type="hidden" name="action" value="delete_tour">
                                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                <button class="btn btn-outline-danger btn-sm">Xóa</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="booking" class="tab-content">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Quản lý đặt Tour</span>
                    <span class="badge bg-primary">
                        <?= count($bookings) ?> đơn hàng
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-center">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Tour đặt</th>
                                <th>Ngày đặt</th>
                                <th>Thanh toán</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $bk): ?>
                                <tr>
                                    <td>#
                                        <?= $bk['id'] ?>
                                    </td>
                                    <td class="text-start fw-bold">
                                        <?= htmlspecialchars($bk['tour_title']) ?>
                                    </td>
                                    <td>
                                        <?= date('d/m/Y', strtotime($bk['booking_date'])) ?>
                                    </td>
                                    <td>
                                        <?php if ($bk['payment_status'] === 'Đã thanh toán'): ?>
                                            <span class="badge bg-success">Đã thanh toán</span>
                                        <?php else: ?>
                                            <a href="payment.php?booking_id=<?= $bk['id'] ?>"
                                                class="badge bg-warning text-dark text-decoration-none">
                                                Chưa thanh toán (Quét QR)
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span
                                            class="badge <?= $bk['status'] === 'Đã xác nhận' ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= $bk['status'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" onsubmit="return confirm('Xóa booking này?')">
                                            <input type="hidden" name="action" value="delete_booking">
                                            <input type="hidden" name="booking_id" value="<?= $bk['id'] ?>">
                                            <button class="btn btn-sm btn-outline-danger px-3">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script>
        function toggleSidebar() {
            let s = document.getElementById("sidebar");
            s.style.left = (s.style.left === "0px") ? "-250px" : "0px";
        }

        function showTab(tab) {
            document.getElementById("home").style.display = "none";
            document.getElementById("add").style.display = "none";
            document.getElementById("tour").style.display = "none";
            document.getElementById("booking").style.display = "none";

            const target = document.getElementById(tab);
            if (target) {
                target.style.display = "block";
            }
            document.getElementById("sidebar").style.left = "-250px";
        }

        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab) {
            showTab(tab);
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>