<?php
session_start();
require_once __DIR__ . '/configs/env.php';
require_once __DIR__ . '/configs/helper.php';

$errors = [];
$message = null;

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['user']);
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] === 'admin') {
        header('Location: ' . BASE_URL . 'admin.php');
    } else {
        header('Location: ' . BASE_URL . 'dashboard.php');
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '') {
        $errors[] = 'Email không được để trống.';
    }
    if ($password === '') {
        $errors[] = 'Mật khẩu không được để trống.';
    }

    if (empty($errors)) {
        try {
            // Khởi tạo kết nối Database
            $conn = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                ];

                if ($user['role'] === 'admin') {
                    header('Location: ' . BASE_URL . 'admin.php');
                } else {
                    header('Location: ' . BASE_URL . 'dashboard.php');
                }
                exit;
            } else {
                $errors[] = 'Email hoặc mật khẩu không chính xác.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Lỗi kết nối database: ' . $e->getMessage();
        }
    }
}

$pageTitle = 'Đăng nhập';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <base href="<?= htmlspecialchars(BASE_URL) ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title mb-4"><?= htmlspecialchars($pageTitle) ?></h3>

                        <?php if ($message): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                        <?php endif; ?>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['user'])): ?>
                            <div class="alert alert-info">
                                Bạn đã đăng nhập với email
                                <strong><?= htmlspecialchars($_SESSION['user']['email']) ?></strong>.
                            </div>
                            <a href="<?= BASE_URL ?>logout.php" class="btn btn-outline-secondary">Đăng xuất</a>
                        <?php else: ?>
                            <form method="post" action="<?= BASE_URL ?>login.php">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mật khẩu</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
                            </form>
                            <div class="mt-3 text-center">
                                <a href="<?= BASE_URL ?>register.php">Chưa có tài khoản? Đăng ký ngay</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>