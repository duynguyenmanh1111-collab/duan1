<?php
require_once 'env.php';

function get_connection()
{
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        return null;
    }
}

function ensure_tour_items_table(PDO $conn): void
{
    $sql = "CREATE TABLE IF NOT EXISTS tour_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,

        -- 👇 THÊM MỚI
        image VARCHAR(255) DEFAULT '',
        category VARCHAR(100) DEFAULT '',
        price INT DEFAULT 0,

        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $conn->exec($sql);

    // Check if 'title' column exists and rename to 'name'
    $stmt = $conn->query("SHOW COLUMNS FROM tour_items LIKE 'title'");
    if ($stmt->fetch()) {
        $conn->exec("ALTER TABLE tour_items CHANGE title name VARCHAR(255) NOT NULL");
    }

    // Add missing columns if they don't exist
    $columns = ['image' => "VARCHAR(255) DEFAULT ''", 'category' => "VARCHAR(100) DEFAULT ''", 'price' => "INT DEFAULT 0"];
    foreach ($columns as $col => $def) {
        $stmt = $conn->prepare("SHOW COLUMNS FROM tour_items LIKE ?");
        $stmt->execute([$col]);
        if (!$stmt->fetch()) {
            $conn->exec("ALTER TABLE tour_items ADD COLUMN $col $def");
        }
    }
}

function add_tour_item($conn, $title, $description, $image, $category, $price)
{
    $stmt = $conn->prepare("
        INSERT INTO tour_items (name, description, image, category, price)
        VALUES (?, ?, ?, ?, ?)
    ");
    return $stmt->execute([$title, $description, $image, $category, $price]);
}

function get_tour_items(PDO $conn): array
{
    $stmt = $conn->query("
        SELECT id, name, description, image, category, price, created_at 
        FROM tour_items 
        ORDER BY created_at DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Tạo bảng bookings
function ensure_bookings_table(PDO $conn): void
{
    $sql = "CREATE TABLE IF NOT EXISTS bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        user_name VARCHAR(255),
        tour_id INT NOT NULL,
        tour_title VARCHAR(255),
        booking_date DATE,
        quantity INT DEFAULT 1,
        payment_status VARCHAR(50) DEFAULT 'Chưa thanh toán',
        status VARCHAR(50) DEFAULT 'Chờ xác nhận',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $conn->exec($sql);

    // Add missing columns if they don't exist
    $columns = [
        'user_id' => "INT NOT NULL",
        'user_name' => "VARCHAR(255)",
        'tour_id' => "INT NOT NULL",
        'tour_title' => "VARCHAR(255)",
        'booking_date' => "DATE",
        'quantity' => "INT DEFAULT 1",
        'payment_status' => "VARCHAR(50) DEFAULT 'Chưa thanh toán'",
        'status' => "VARCHAR(50) DEFAULT 'Chờ xác nhận'",
        'created_at' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
    ];
    foreach ($columns as $col => $def) {
        $stmt = $conn->prepare("SHOW COLUMNS FROM bookings LIKE ?");
        $stmt->execute([$col]);
        if (!$stmt->fetch()) {
            $conn->exec("ALTER TABLE bookings ADD COLUMN $col $def");
        }
    }
}
function get_all_bookings(PDO $conn): array
{
    $stmt = $conn->query("SELECT * FROM bookings ORDER BY id DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function update_booking_status(PDO $conn, int $id, string $status): bool
{
    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    return $stmt->execute([$status, $id]);
}

function get_booking_by_id(PDO $conn, int $id)
{
    $stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function delete_booking(PDO $conn, int $id): bool
{
    // Xóa log trước
    $stmt1 = $conn->prepare("DELETE FROM booking_status_logs WHERE booking_id = ?");
    $stmt1->execute([$id]);

    // Xóa booking
    $stmt2 = $conn->prepare("DELETE FROM bookings WHERE id = ?");
    return $stmt2->execute([$id]);
}

// Lấy 1 tour theo ID
function get_tour_by_id(PDO $conn, int $id)
{
    $stmt = $conn->prepare("SELECT * FROM tour_items WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Thêm booking
function add_booking(PDO $conn, $userId, $userName, $tourId, $tourTitle, $bookingDate, $quantity): bool
{
    $sql = "INSERT INTO bookings (user_id, user_name, tour_id, tour_title, booking_date, quantity)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    return $stmt->execute([$userId, $userName, $tourId, $tourTitle, $bookingDate, $quantity]);
}

// Lấy booking của user
function get_user_bookings(PDO $conn, int $userId): array
{
    $stmt = $conn->prepare("SELECT * FROM bookings WHERE user_id = ? ORDER BY id DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}g