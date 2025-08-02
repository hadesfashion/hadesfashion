<?php
session_start();
// Kết nối đến cơ sở dữ liệu
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "vidu";

// Tạo kết nối
$connect = new mysqli($servername, $username, $password, $database);

// Kiểm tra kết nối
if ($connect->connect_error) {
    die("Kết nối thất bại: " . $connect->connect_error);
}

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['id_khachhang'])) {
    header("Location: http://localhost/webbanhang/pages/main/dangnhap.php");
    exit();
}
include '../header.php'; 

// Lấy ID khách hàng từ session
$id_khachhang = $_SESSION['id_khachhang'];

// Lấy thông tin khách hàng từ cơ sở dữ liệu
$sql = "SELECT * FROM khachhang WHERE id_khachhang = '$id_khachhang'";
$result = mysqli_query($connect, $sql);
$khachhang = mysqli_fetch_assoc($result);

if (!$khachhang) {
    echo "Không tìm thấy thông tin tài khoản.";
    exit();
}

// Lưu thông tin tài khoản vào session để sử dụng trong trang thanh toán
$_SESSION['tenkhachhang'] = $khachhang['tenkhachhang'];
$_SESSION['dienthoai'] = $khachhang['dienthoai'];
$_SESSION['diachi'] = $khachhang['diachi'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Tin Tài Khoản</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f7f7;
            display: flex;
            justify-content: center; /* Căn giữa theo chiều ngang */
            align-items: center; /* Căn giữa theo chiều dọc */
            min-height: 100vh; /* Chiều cao tối thiểu là chiều cao của màn hình */
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 90%; /* Thay đổi chiều rộng của form để vừa với màn hình */
            max-width: 400px; /* Chiều rộng tối đa của form */
            box-sizing: border-box;
            text-align: center; /* Căn giữa nội dung bên trong form */
        }
        h2 {
            color: #333;
            font-size: 24px; /* Tăng cỡ chữ cho tiêu đề */
            margin-bottom: 20px; /* Khoảng cách dưới tiêu đề */
        }
        .form-group {
            display: flex; /* Sử dụng flexbox để căn giữa */
            justify-content: space-between; /* Giãn cách đều giữa nhãn và giá trị */
            margin-bottom: 15px;
            align-items: center; /* Căn giữa theo chiều dọc */
        }
        label {
            font-weight: bold;
            margin-right: 10px; /* Khoảng cách giữa nhãn và giá trị */
            text-align: left; /* Căn trái cho label */
            flex-basis: 40%; /* Đặt chiều rộng cố định cho nhãn */
        }
        .value {
            flex-basis: 60%; /* Đặt chiều rộng cố định cho giá trị */
            text-align: left; /* Căn trái cho giá trị */
        }
        .button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            display: inline-block; /* Chỉnh sửa thành inline-block để dễ dàng căn giữa */
            text-decoration: none;
            font-weight: bold;
            margin-top: 10px; /* Khoảng cách giữa button và form */
            box-sizing: border-box;
        }
        .button-container {
            text-align: center; /* Căn giữa cho nút sửa thông tin */
        }
        .button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Tài Khoản Của Bạn</h2>

        <div class="form-group">
            <label for="tenkhachhang">Tên Khách Hàng:</label>
            <div class="value"><?php echo htmlspecialchars($khachhang['tenkhachhang']); ?></div>
        </div>
        <div class="form-group">
            <label for="diachi">Địa Chỉ Nhận Hàng:</label>
            <div class="value"><?php echo htmlspecialchars($khachhang['diachi']); ?></div>
        </div>
        <div class="form-group">
            <label for="dienthoai">Điện Thoại:</label>
            <div class="value"><?php echo htmlspecialchars($khachhang['dienthoai']); ?></div>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <div class="value"><?php echo htmlspecialchars($khachhang['email']); ?></div>
        </div>
        <div class="button-container">
            <a href="/webbanhang/pages/main/suataikhoan.php" class="button">Sửa Thông Tin Tài Khoản</a>
        </div>
    </div>
</body>
</html>
