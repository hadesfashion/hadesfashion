<!-- banner -->
<style>
    /* Loại bỏ margin và padding mặc định của body và html */
    html, body {
        margin: 0;
        padding: 0;
        height: 100%;
        overflow-x: hidden; /* Đảm bảo không có cuộn ngang */
    }

    /* Đảm bảo carousel chiếm toàn bộ chiều cao và không có khoảng trống */
    .carousel-item {
        height: 68vh; /* Chiều cao 50% của viewport */
        overflow: hidden; /* Ẩn phần không cần thiết */
    }

    .carousel-item img {
        height: 100%; /* Đảm bảo ảnh chiếm toàn bộ chiều cao */
        width: 100%;  /* Đảm bảo ảnh chiếm toàn bộ chiều rộng */
        object-fit: cover; /* Giữ tỷ lệ ảnh và cắt bỏ phần thừa */
        transition: transform 0.5s ease; /* Hiệu ứng chuyển động mượt mà */
    }

    /* Danh sách sản phẩm */
    .cat-item {
        border: 1px solid #ddd; /* Khung cho từng mục */
        border-radius: 5px; /* Bo góc cho khung */
        padding: 10px; /* Thêm khoảng cách trong khung */
        transition: transform 0.3s; /* Hiệu ứng zoom khi hover */
    }

    .cat-item:hover {
        transform: scale(1.05); /* Phóng to khi hover */
    }
</style>
<?php
    // Kết nối cơ sở dữ liệu
    $servername = "localhost";
    $username = "root"; 
    $password = ""; 
    $dbname = "vidu"; 

    // Tạo kết nối
    $connect = new mysqli($servername, $username, $password, $dbname);

    // Kiểm tra kết nối
    if ($connect->connect_error) {
        die("Kết nối thất bại: " . $connect->connect_error);
    }

// Lấy tất cả ảnh trang bìa từ cơ sở dữ liệu
$query = "SELECT * FROM anhtrangbia WHERE tinhtrang = 1"; // Lọc ra ảnh có trạng thái 'Kích hoạt'
$result = mysqli_query($connect, $query);

// Kiểm tra nếu có ảnh
if (mysqli_num_rows($result) > 0) {
    $carouselItems = '';
    $isFirstItem = true; // Biến để đánh dấu ảnh đầu tiên
    while ($row = mysqli_fetch_assoc($result)) {
        // Đánh dấu ảnh đầu tiên là active
        $activeClass = $isFirstItem ? 'active' : '';
        $carouselItems .= '<div class="carousel-item ' . $activeClass . '">
            <img src="/webbanhang/admin/modules/quanlyanhbia/img/' . $row['hinhanh'] . '" alt="Banner ' . $row['id_anhtrangbia'] . '">
        </div>';
        $isFirstItem = false; // Chỉ đánh dấu ảnh đầu tiên là active
    }
} else {
    $carouselItems = '<div class="carousel-item active">
        <img src="/webbanhang/img/default.jpg" alt="Default Banner">
    </div>';
}
?>

<div class="container-fluid pt-1">
    <div id="header-carousel" class="carousel slide" data-ride="carousel" data-interval="2000" data-pause="false">
        <div class="carousel-inner">
            <?php
            // Hiển thị ảnh carousel từ cơ sở dữ liệu
            echo $carouselItems;
            ?>
        </div>
        <a class="carousel-control-prev" href="#header-carousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </a>
        <a class="carousel-control-next" href="#header-carousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </a>
    </div>
</div>
<!-- Đóng banner-->
