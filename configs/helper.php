<?php

if (!function_exists('debug')) {
    function debug($data)
    {
        echo '<pre>';
        print_r($data);
        die;
    }
}

if (!function_exists('upload_file')) {
    function upload_file($folder, $file)
    {
        $targetFile = $folder . '/' . time() . '-' . $file["name"];

        if (move_uploaded_file($file["tmp_name"], PATH_ASSETS_UPLOADS . $targetFile)) {
            return $targetFile;
        }

        throw new Exception('Upload file không thành công!');
    }
}

if (!function_exists('is_logged_in')) {
    function is_logged_in(): bool
    {
        return isset($_SESSION['user']);
    }
}

if (!function_exists('is_admin')) {
    function is_admin(): bool
    {
        return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
    }
}

if (!function_exists('require_login')) {
    function require_login(): void
    {
        if (!is_logged_in()) {
            header('Location: ' . BASE_URL . 'login.php');
            exit;
        }
    }
}

if (!function_exists('require_admin')) {
    function require_admin(): void
    {
        require_login();

        if (!is_admin()) {
            header('Location: ' . BASE_URL . 'user.php');
            exit;
        }
    }
}

if (!function_exists('require_user')) {
    function require_user(): void
    {
        require_login();

        if (is_admin()) {
            header('Location: ' . BASE_URL . 'admin.php');
            exit;
        }
    }
}