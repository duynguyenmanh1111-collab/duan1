<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../configs/env.php';
require_once __DIR__ . '/../../configs/helper.php';
require_once __DIR__ . '/../../configs/pdo.php';

require_admin();

$conn = get_connection();

/* ================= TOUR FUNCTIONS ================= */
if (!function_exists('add_tour')) {
    function add_tour($conn, $title, $description, $price, $category, $image)
    {
        $stmt = $conn->prepare("INSERT INTO tour_items(title,description,price,category,image) VALUES(?,?,?,?,?)");
        return $stmt->execute([$title, $description, $price, $category, $image]);
    }
}

if (!function_exists('update_tour')) {
    function update_tour($conn, $id, $title, $description, $price, $category, $image)
    {
        if ($image) {
            $sql = "UPDATE tour_items SET title=?,description=?,price=?,category=?,image=? WHERE id=?";
            $params = [$title, $description, $price, $category, $image, $id];
        } else {
            $sql = "UPDATE tour_items SET title=?,description=?,price=?,category=? WHERE id=?";
            $params = [$title, $description, $price, $category, $id];
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

/* ================= HANDLE POST ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $uploadDir = __DIR__ . '/../../uploads/';
    if (!is_dir($uploadDir))
        mkdir($uploadDir, 0777, true);

    // THÊM TOUR
    if ($_POST['action'] === 'add_tour') {
        $img = '';
        if (!empty($_FILES['image']['name'])) {
            $img = time() . '_' . $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $img);
        }
        add_tour($conn, $_POST['title'], $_POST['description'], $_POST['price'], $_POST['category'], $img);

        // ✅ FIX: giữ tab
        header("Location: admin.php?tab=tour");
        exit;
    }

    // SỬA TOUR
    if ($_POST['action'] === 'update_tour') {
        $img = '';
        if (!empty($_FILES['image']['name'])) {
            $img = time() . '_' . $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $img);
        }
        update_tour($conn, $_POST['id'], $_POST['title'], $_POST['description'], $_POST['price'], $_POST['category'], $img);

        // ✅ FIX
        header("Location: admin.php?tab=tour");
        exit;
    }

    // XÓA TOUR
    if ($_POST['action'] === 'delete_tour') {
        delete_tour($conn, $_POST['id']);

        // ✅ FIX
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
    <title>Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar bg-white shadow-sm px-3">
        <button class="btn btn-outline-primary" onclick="toggleSidebar()">☰</button>
        <span class="mx-auto fw-bold text-primary">ADMIN</span>
        <a href="<?= BASE_URL ?>logout.php" class="btn btn-danger btn-sm">Đăng xuất</a>
    </nav>

    <div id="sidebar" style="
position: fixed;
top:0; left:-250px;
width:250px; height:100%;
background:#fff;
transition:0.3s;
padding-top:60px;
z-index:999;
">
        <a href="#" onclick="showTab('home')" class="d-block p-3 border-bottom">🏠 Trang chủ</a>
        <a href="#" onclick="showTab('add')" class="d-block p-3 border-bottom">➕ Thêm tour</a>
        <a href="#" onclick="showTab('tour')" class="d-block p-3 border-bottom">📦 Danh sách tour</a>
        <a href="#" onclick="showTab('booking')" class="d-block p-3 border-bottom">📑 Quản lý booking</a>
    </div>

    <div class="container mt-4">

        <div id="home">
            <div class="text-center mt-5">
                <h2 class="fw-bold text-primary">Chào mừng đến với trang admin</h2>
            </div>
        </div>

        <div id="add" style="display:none;">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">Thêm tour</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add_tour">

                        <label>Tiêu đề</label>
                        <input class="form-control mb-2" name="title" required>

                        <label>Mô tả</label>
                        <textarea class="form-control mb-2" name="description"></textarea>

                        <label>Giá</label>
                        <input class="form-control mb-2" name="price">

                        <label>Danh mục</label>
                        <select class="form-control mb-2" name="category">
                            <option>Nội địa</option>
                            <option>Quốc tế</option>
                        </select>

                        <label>Ảnh</label>
                        <input type="file" class="form-control mb-2" name="image">

                        <button class="btn btn-success w-100">Thêm</button>
                    </form>
                </div>
            </div>
        </div>

        <div id="tour" style="display:none;">
            <div class="card">
                <div class="card-header bg-secondary text-white">Danh sách tour</div>
                <div class="card-body">

                    <table class="table">
                        <tr>
                            <th>Tên</th>
                            <th>Giá</th>
                            <th>Ảnh</th>
                            <th>Action</th>
                        </tr>

                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['title']) ?></td>
                                <td><?= number_format((float) ($item['price'] ?? 0)) ?>đ</td>
                                <td>
                                    <?php if (!empty($item['image'])): ?>
                                        <img src="<?= BASE_URL ?>uploads/<?= $item['image'] ?>" width="80">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="delete_tour">
                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                        <button class="btn btn-danger btn-sm">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </table>

                </div>
            </div>
        </div>

        <div id="booking" style="display:none;">
            <div class="card">
                <div class="card-header bg-danger text-white">Quản lý booking</div>
                <div class="card-body text-center text-muted">
                    (Hiện Chưa Có Booking Nào )
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

            document.getElementById(tab).style.display = "block";
            document.getElementById("sidebar").style.left = "-250px";
        }

        // ✅ FIX: giữ tab sau redirect
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab) {
            showTab(tab);
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>