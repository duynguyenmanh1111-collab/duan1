<?php
session_start();
require_once __DIR__ . '/configs/env.php';
require_once __DIR__ . '/configs/helper.php';

$errors = [];
$message = null;

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['user']);
    $message = 'Bạn đã đăng xuất thành công.';
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
        $users = $_SESSION['users'] ?? [];
        $user = null;

        foreach ($users as $item) {
            if (strtolower($item['email']) === strtolower($email)) {
                $user = $item;
                break;
            }
        }

        if (!$user || !password_verify($password, $user['password'])) {
            $errors[] = 'Email hoặc mật khẩu không chính xác.';
        } else {
            $_SESSION['user'] = [
                'username' => $user['username'],
                'email' => $user['email'],
            ];

            header('Location: ' . BASE_URL);
            exit;
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
                            <a href="login.php?action=logout" class="btn btn-outline-secondary">Đăng xuất</a>
                        <?php else: ?>
                            <form method="post" action="login.php">
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
                                <a href="register.php">Chưa có tài khoản? Đăng ký ngay</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>