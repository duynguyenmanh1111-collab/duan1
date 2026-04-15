<?php
class TourModel extends BaseModel
{
    private $conn;

    public function __construct()
    {
        // Gọi hàm get_connection từ file pdo.php của bạn
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
        $stmt = $this->conn->prepare("INSERT INTO tour_items (title, description) VALUES (?, ?)");
        return $stmt->execute([$title, $description]);
    }

    // Cập nhật tour
    public function updateTour($id, $title, $description)
    {
        $stmt = $this->conn->prepare("UPDATE tour_items SET title = ?, description = ? WHERE id = ?");
        return $stmt->execute([$title, $description, $id]);
    }

    // Xóa tour
    public function deleteTour($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM tour_items WHERE id = ?");
        return $stmt->execute([$id]);
    }
    //tim kiem
    public function searchTours($keyword)
    {
        $sql = "SELECT * FROM tour_items WHERE title LIKE ? OR description LIKE ? ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        $search = "%$keyword%";
        $stmt->execute([$search, $search]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}