<?php
class AdminTourController
{
    private $tourModel;

    public function __construct()
    {
        $this->tourModel = new TourModel();
    }

    public function index()
    {
        $keyword = $_GET['keyword'] ?? '';

        if (!empty($keyword)) {
            $items = $this->tourModel->searchTours($keyword);
            $pageTitle = "Kết quả tìm kiếm cho: " . htmlspecialchars($keyword);
        } else {
            $items = $this->tourModel->getAllTours();
            $pageTitle = "Quản lý danh sách Tour";
        }

        $user = $_SESSION['user'] ?? ['username' => 'Admin', 'role' => 'Quản trị viên'];

        include 'views/admin/admin.php';
    }

    public function show($id)
    {
        $tour = $this->tourModel->getTourById($id);

        if (!$tour) {
            header("Location: index.php?act=list-tour&error=Tour không tồn tại");
            exit();
        }

        $pageTitle = "Chi tiết: " . $tour['title'];
        $user = $_SESSION['user'] ?? ['username' => 'Admin', 'role' => 'Quản trị viên'];

        include 'views/admin/detail.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';

            if (!empty($title) && !empty($description)) {
                $this->tourModel->insertTour($title, $description);
                header("Location: index.php?act=list-tour&message=Thêm tour mới thành công");
            } else {
                header("Location: index.php?act=list-tour&error=Vui lòng nhập đầy đủ thông tin");
            }
            exit();
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';

            if ($id && !empty($title) && !empty($description)) {
                $this->tourModel->updateTour($id, $title, $description);
                header("Location: index.php?act=list-tour&message=Cập nhật thành công");
            } else {
                header("Location: index.php?act=list-tour&error=Dữ liệu không hợp lệ");
            }
            exit();
        }
    }

    public function destroy($id)
    {
        if ($id) {
            $this->tourModel->deleteTour($id);
            header("Location: index.php?act=list-tour&message=Đã xóa tour thành công");
        } else {
            header("Location: index.php?act=list-tour&error=ID không hợp lệ");
        }
        exit();
    }
}