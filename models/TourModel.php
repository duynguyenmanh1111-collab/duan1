<?php

namespace Models; // THÊM DÒNG NÀY ĐỂ Controller có thể "use Models\TourModel"

// Không cần require_once 'BaseModel.php' ở đây vì index.php đã require rồi
// Nhưng nếu bạn muốn chắc chắn, hãy dùng đường dẫn đúng hoặc để index lo
use PDO; // Để sử dụng được class PDO bên trong file này

class TourModel
{
    private $conn;

    public function __construct()
    {
        // Hàm get_connection() nằm trong file pdo.php đã được require ở index.php
        $this->conn = get_connection();
    }

    // Lấy tất cả danh sách tour
    public function getAllTours()
    {
        $stmt = $this->conn->query("SELECT * FROM tour_items ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy chi tiết 1 tour theo ID
    public function getTourById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM tour_items WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm tour mới
    public function insertTour($title, $description)
    {
        $stmt = $this->conn->prepare("INSERT INTO tour_items (name, description) VALUES (?, ?)");
        return $stmt->execute([$title, $description]);
    }

    // Cập nhật tour
    public function updateTour($id, $title, $description)
    {
        $stmt = $this->conn->prepare("UPDATE tour_items SET name = ?, description = ? WHERE id = ?");
        return $stmt->execute([$title, $description, $id]);
    }

    // Xóa tour
    public function deleteTour($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM tour_items WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Tìm kiếm
    public function searchTours($keyword)
    {
        $sql = "SELECT * FROM tour_items WHERE name LIKE ? OR description LIKE ? ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        $search = "%$keyword%";
        $stmt->execute([$search, $search]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}