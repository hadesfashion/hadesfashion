<?php
session_start();
?>
<?php
// Kết nối đến cơ sở dữ liệu
$connect = new mysqli('localhost', 'root', '', 'vidu');

// Kiểm tra kết nối
if ($connect->connect_error) {
    die("Kết nối thất bại: " . $connect->connect_error);
}

// Truy vấn để lấy 3 tin tức liên quan
$sql_related = "SELECT id, tieude, hinhanh FROM tintuc ORDER BY ngaydang DESC LIMIT 4";
$result_related = $connect->query($sql_related);
?>

<?php include '../header.php'; ?>
<div class="container">
    <!-- Sidebar -->
    <aside class="containerleft left-content col-lg-3">
        <div class="aside-item sidebar-category blog-category">
            <h2 class="aside-title title-head">Danh mục tin tức</h2>
            <div class="aside-content">
                <nav class="nav-category navbar-toggleable-md">
                    <ul class="nav navbar-pills flex-column">
                        <li class="nav-item">
                            <a class="nav-link black-text" href="#">Tin tức thời trang</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link black-text" href="#">Mẹo vặt hay</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link black-text" href="#">Kinh nghiệm thời trang</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link black-text" href="#">Tư vấn hỏi đáp</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="aside-item sidebar-related-articles">
    <h2 class="aside-title title-head">Tin tức liên quan</h2>
    <div class="aside-content">
        <ul class="nav navbar-pills flex-column">
            <style> .related-article-text {
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
            <?php
            if ($result_related->num_rows > 0) {
                // Lặp qua kết quả và hiển thị
                while ($row = $result_related->fetch_assoc()) {
                    echo '<li class="nav-item d-flex align-items-start">';
                    // Tạo liên kết với id của tin tức để chuyển hướng đến trang chi tiết
                    echo '    <a class="nav-link black-text" href="/webbanhang/pages/main/chitiettintuc.php?id=' . htmlspecialchars($row['id']) . '" style="display: flex; align-items: center;">';
                    // Hiển thị ảnh nhỏ
                    echo '        <img src="/webbanhang/admin/modules/quanlytintuc/img/' . htmlspecialchars($row['hinhanh']) . '" alt="' . htmlspecialchars($row['tieude']) . '" style="width: 100px; height: 70px; object-fit: cover; margin-right: 10px;">';
                    // Hiển thị tiêu đề tin tức
                    echo '        <span class="related-article-text">' . htmlspecialchars($row['tieude']) . '</span>';
                    
                    echo '    </a>'; // Kết thúc thẻ <a>
                    echo '</li>'; // Kết thúc thẻ <li>
                }
            } else {
                echo '<li class="nav-item"><span class="black-text">Không có tin tức liên quan.</span></li>';
            }
            ?>
        </ul>
    </div>
</div>
    </aside>

    <style>
        .aside-title {
            font-size: 18px; /* Thay đổi kích thước chữ */
            white-space: nowrap; /* Giữ chữ trên một dòng */
            background-color: #f4f2f2; /* Màu nền xám nhạt */
            padding: 10px; /* Khoảng cách bên trong khung */
            border: 1px solid #ccc; /* Đường viền khung */
            border-radius: 0px; /* Bo góc khung */
            text-align: left; /* Căn trái chữ */
            margin: 0; /* Bỏ margin mặc định */
        }

        .black-text {
            color: #000; /* Màu chữ đen */
        }
        
        .black-text:hover {
            color: #8adf12; /* Màu chữ khi di chuột vào */
        }
    </style>

    <!-- Main content -->
    <section class="main-content">  
        <?php
        // Câu lệnh SQL để lấy tất cả tin tức
        $sql = "SELECT * FROM tintuc";
        $result = $connect->query($sql);

        if ($result->num_rows > 0) {
            // Hiển thị từng tin tức
            while($row = $result->fetch_assoc()) {
                echo '<div class="news-item">';
                echo '    <div class="image">';
                echo '        <img src="/webbanhang/admin/modules/quanlytintuc/img/' . $row['hinhanh'] . '" alt="Ảnh tin tức">';
                echo '    </div>';
                echo '    <div class="news-info">';
                echo '        <h4>' . $row['tieude'] . '</h4>';
                echo '        <p>' . $row['mota'] . '</p>';
                echo '        <a href="/webbanhang/pages/main/chitiettintuc.php?id=' . $row['id'] . '"> Xem thêm</a>';
                echo '    </div>';
                echo '</div>';
            }
        } else {
            echo "<p>Không có tin tức nào được tìm thấy.</p>";
        }

        // Đóng kết nối
        $connect->close();
        ?>
    </section>
</div>
<?php include '../footer.php'; ?>
</body>
</html>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
}

.container {
    display: flex;
    justify-content: space-between;
    margin: 20px auto;
    max-width: 1200px;
}

.sidebar {
    width: 25%;
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.sidebar h3 {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 15px;
    color: #333;
}

.sidebar ul {
    list-style-type: none;
    padding-left: 0;
}

.sidebar ul li {
    margin-bottom: 10px;
}

.sidebar ul li a {
    text-decoration: none;
    color: #333;
    font-size: 16px;
}

.featured-news {
    margin-top: 30px;
}

.featured-news .featured-item {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.featured-news .featured-item img {
    width: 50px;
    height: 50px;
    margin-right: 10px;
}

.featured-news .featured-item a {
    text-decoration: none;
    color: #ff6600;
}

.main-content {
    width: 72%;
    display: flex;  /* Sử dụng flex để hiển thị 2 mục tin tức trên cùng 1 hàng */
    flex-wrap: wrap; /* Cho phép xuống dòng nếu cần */
    gap: 30px; /* Khoảng cách giữa các mục tin tức */
}

.news-item {
    background-color: #ffffff;
    border-radius: 0px;  /* bo góc */
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    width: calc(50% - 30px); /* Mỗi tin tức chiếm 50% chiều rộng khung chứa, trừ khoảng cách */
    margin-bottom: 30px;
}

.news-item .image img {
    width: 100%;
    height: 150px; /* Đặt chiều cao cố định cho hình ảnh */
    object-fit: cover; /* Cắt ảnh để vừa khung */
}

.news-info {
    padding: 15px;
}

.news-info h4 {
    font-size: 20px;
    margin: 0;
    font-weight: bold;
}

.news-info p {
    font-size: 14px;
    color: #666;
    margin-top: 5px;
}

.news-info a {
    display: inline-block;
    margin-top: 10px;
    color: #ff6600;
    text-decoration: none;
    font-weight: bold;
}

.news-info a:hover {
    color: #cc5200;
    text-decoration: underline;
}
</style>
