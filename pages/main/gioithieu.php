<?php
// Kết nối cơ sở dữ liệu
$connect = mysqli_connect('localhost', 'root', '', 'vidu');

// Kiểm tra kết nối
if (!$connect) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Truy vấn lấy thông tin trang giới thiệu
$sql = "SELECT * FROM gioithieu WHERE trangthai = 1 LIMIT 1"; // Chỉ lấy một bản ghi có trạng thái hiển thị
$query = mysqli_query($connect, $sql);
$row = mysqli_fetch_assoc($query);

// Kiểm tra nếu có dữ liệu
if ($row) {
    $ten = $row['ten'];
    $mota = $row['mota'];
} else {
    $ten = "Chưa có thông tin";
    $mota = "Dữ liệu giới thiệu không có sẵn.";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giới thiệu</title>
    <link rel="stylesheet" href="style.css"> <!-- Liên kết tới file CSS -->
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <h1 class="title"><?php echo $ten; ?></h1> <!-- Hiển thị tiêu đề -->
            <p class="sub-title">Khám phá hành trình của chúng tôi và những giá trị mà chúng tôi mang lại.</p>
        </div>
    </header>

    <!-- Content -->
    <section class="content">
        <div class="container">
            <div class="content-left">
                <h2 class="heading">Chúng tôi là ai?</h2>
                <p class="description"><?php echo nl2br($mota); ?></p> <!-- Hiển thị mô tả -->
            </div>
            <div class="content-right">
                <img src="images/about_us.jpg" alt="Giới thiệu" class="image">
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2024 Công ty XYZ. Tất cả quyền lợi được bảo lưu.</p>
        </div>
    </footer>
</body>
</html>
