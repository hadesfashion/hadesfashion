<?php include '../header.php'; ?>
<?php
// Kết nối cơ sở dữ liệu
$connect = new mysqli('localhost', 'root', '', 'vidu'); // Cập nhật tên database, username và password nếu cần

// Kiểm tra kết nối
if ($connect->connect_error) {
    die("Kết nối thất bại: " . $connect->connect_error);
}

// Lấy ID bài báo từ tham số URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Câu lệnh SQL để lấy thông tin bài báo
$sql = "SELECT * FROM tintuc WHERE id = $id"; 
$result = $connect->query($sql);

// Truy vấn để lấy 3 tin tức liên quan
$sql_related = "SELECT id, tieude, hinhanh FROM tintuc ORDER BY ngaydang DESC LIMIT 4";
$result_related = $connect->query($sql_related);

// Kiểm tra nếu bài báo tồn tại
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    die("Bài báo không tồn tại.");
}

// Đóng kết nối
$connect->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($row['tieude']); ?></title> <!-- Thay đổi tiêu đề -->
    <link rel="stylesheet" href="/webbanhang/admin/assets/vendors/datatables.net-bs5/dataTables.bootstrap5.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9; /* Màu nền trang */
        }
        .container {
            display: flex; /* Thiết lập flex cho bố cục */
            margin-top: 20px;
        }
        .left-content {
            width: 25%; /* Độ rộng của sidebar */
            padding-right: 15px; /* Khoảng cách bên phải cho sidebar */
        }
        .right-content {
            width: 75%; /* Độ rộng của nội dung chi tiết */
        }
        .aside-title {
            font-size: 18px; /* Kích thước chữ tiêu đề sidebar */
            background-color: #f4f2f2; /* Màu nền tiêu đề */
            padding: 10px; /* Khoảng cách bên trong */
            border: 1px solid #ccc; /* Đường viền */
            margin-bottom: 10px; /* Khoảng cách dưới */
        }
        .nav-item {
            margin-bottom: 5px; /* Khoảng cách giữa các mục */
        }
        .nav-link {
            color: #000; /* Màu chữ đen */
        }
        .nav-link:hover {
            color: #8adf12; /* Màu chữ khi di chuột vào */
        }
        .img-container {
            margin: 20px 0; /* Khoảng cách trên dưới cho ảnh */
        }
        .related-article-text {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
            width: calc(100% - 110px); /* Giảm trừ độ rộng ảnh và khoảng cách */
            max-height: 3.6em; /* Giới hạn chiều cao của 3 dòng chữ */
            line-height: 1.2em; /* Đặt khoảng cách dòng */
            text-overflow: ellipsis;
            white-space: normal;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Sidebar -->
    <aside class="left-content">
        <div class="aside-item sidebar-category blog-category">
            <h2 class="aside-title title-head">Danh mục tin tức</h2>
            <div class="aside-content">
                <nav class="nav-category navbar-toggleable-md">
                    <ul class="nav navbar-pills flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="#">Tin tức thời trang</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Mẹo vặt hay</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Kinh nghiệm thời trang</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Tư vấn hỏi đáp</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="aside-item sidebar-related-articles">
            <h2 class="aside-title title-head">Tin tức liên quan</h2>
            <div class="aside-content">
                <ul class="nav navbar-pills flex-column">
                    <?php
                    if ($result_related->num_rows > 0) {
                        while ($row_related = $result_related->fetch_assoc()) {
                            echo '<li class="nav-item d-flex align-items-start">';
                            echo '    <a class="nav-link" href="/webbanhang/pages/main/chitiettintuc.php?id=' . htmlspecialchars($row_related['id']) . '" style="display: flex; align-items: center;">';
                            echo '        <img src="/webbanhang/admin/modules/quanlytintuc/img/' . htmlspecialchars($row_related['hinhanh']) . '" alt="' . htmlspecialchars($row_related['tieude']) . '" style="width: 100px; height: 70px; object-fit: cover; margin-right: 10px;">';
                            echo '        <span class="related-article-text">' . htmlspecialchars($row_related['tieude']) . '</span>';
                            echo '    </a>'; 
                            echo '</li>'; 
                        }
                    } else {
                        echo '<li class="nav-item"><span>Không có tin tức liên quan.</span></li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </aside>

    <!-- Chi tiết tin tức -->
    <div class="right-content">
        <div class="mt-5">
          <h1 id="tieude" style="font-size: 1.5rem; margin-top: -3.5rem; margin-bottom: 1.5rem;">
              <?php echo htmlspecialchars($row['tieude']); ?>
          </h1>

            <div class="img-container">
                <img style="width: 100%; height: auto; object-fit: cover;" src="/webbanhang/admin/modules/quanlytintuc/img/<?php echo htmlspecialchars($row['hinhanh']); ?>" alt="Ảnh bài báo">
            </div>

            <div id="noidung" style="font-size: 1rem; margin-bottom: 1rem;">
                <?php echo nl2br(htmlspecialchars($row['noidung'])); ?>
            </div>

            <p class="mt-3"><strong>Thời gian: </strong><?php echo date('d/m/Y H:i', strtotime($row['ngaydang'])); ?></p> <!-- Thay đổi ngày đăng -->
            
            <a href="http://localhost/webbanhang/" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>
</div>

<script src="/webbanhang/admin/assets/vendors/datatables.net-bs5/dataTables.bootstrap5.js"></script>
<?php include '../footer.php'; ?>
</body>
</html>
