<?php 
// Bắt đầu bộ đệm đầu ra
ob_start();
?>

<?php
session_start();
include '../header.php'; 
include './config.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $matkhau = $_POST['matkhau'];

    // Lấy thông tin khách hàng từ cơ sở dữ liệu
    $sql = "SELECT * FROM khachhang WHERE email = '$email'";
    $result = mysqli_query($connect, $sql);
    $user = mysqli_fetch_assoc($result);

    // Kiểm tra xem người dùng có tồn tại và mật khẩu có đúng không
    if ($user && password_verify($matkhau, $user['matkhau'])) {
        // Lưu ID và thông tin khách hàng vào session
        $_SESSION['id_khachhang'] = $user['id_khachhang'];
        $_SESSION['tenkhachhang'] = $user['tenkhachhang']; // Lưu tên khách hàng vào session
        $_SESSION['diachi'] = $user['diachi']; // Lưu địa chỉ vào session
        $_SESSION['dienthoai'] = $user['dienthoai']; // Lưu điện thoại vào session

        // Điều hướng trực tiếp đến trang giỏ hàng
        header("Location: http://localhost/webbanhang");
        exit(); // Dừng script lại để tránh việc mã tiếp tục chạy
    } else {
        $error_message = "Sai email hoặc mật khẩu!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
            background: url('/webbanhang/img/anhdangky.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background-color: rgba(255, 255, 255, 0.9); /* Màu nền khung login trong suốt nhẹ */
            border-radius: 8px; /* Bo góc khung */
            padding: 20px; /* Khoảng cách nội dung trong khung */
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.3); /* Hiệu ứng đổ bóng */
            width: 400px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            position: relative;
            width: 100%;
            margin-bottom: 15px;
        }

        .form-group input {
            border: none;
            border-radius: 5px;
            padding: 10px 40px;
            width: 100%;
            color: #333;
            background: #f0f0f0;
            outline: none;
        }

        .form-group i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #333;
            font-size: 20px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #ff4081;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 15px;
        }

        button:hover {
            background-color: #e0356b;
        }

        .error-message {
            color: red;
            margin-bottom: 15px;
        }

        .divider {
            margin: 20px 0;
        }

        .footer-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .footer-buttons a {
            text-decoration: none;
            color: #ff4081;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="login-container">
    <h2>Đăng Nhập</h2>

    <?php
    if (isset($error_message)) {
        echo "<p class='error-message'>$error_message</p>";
    }
    ?>

    <form method="POST" action="">
        <div class="form-group">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" required placeholder="Email" autocomplete="new-email">
        </div>
        <div class="form-group">
            <i class="fas fa-key"></i>
            <input type="password" name="matkhau" required placeholder="Mật khẩu" autocomplete="new-password">
        </div>
        <button type="submit">Đăng Nhập</button>
    </form>

    <div class="divider">
        <span>Bạn chưa có tài khoản? </span>
    </div>

    <div class="footer-buttons">
        <a href="http://localhost/webbanhang/">Quay lại trang chủ</a>
        <a href="http://localhost/webbanhang/pages/main/dangky.php">Đăng ký</a>
    </div>
</div>
</body>
</html>
<?php
// Kết thúc bộ đệm đầu ra và xóa nó
ob_end_flush();
?>
