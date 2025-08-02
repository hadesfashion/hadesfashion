<?php 
// Bắt đầu bộ đệm đầu ra
ob_start();
?>
<?php
session_start();
include '../header.php'; 
include './config.php'; 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenkhachhang = $_POST['tenkhachhang'];
    $diachi = $_POST['diachi'];
    $dienthoai = $_POST['dienthoai'];
    $email = $_POST['email'];
    $matkhau = $_POST['matkhau'];

    // Mã hóa mật khẩu
    $matkhau_moi = password_hash($matkhau, PASSWORD_DEFAULT);

    // Kiểm tra xem email đã tồn tại chưa
    $sql_check_email = "SELECT * FROM khachhang WHERE email = '$email'";
    $result_check_email = mysqli_query($connect, $sql_check_email);

    if (mysqli_num_rows($result_check_email) > 0) {
        $error_message = "Email đã tồn tại!";
    } else {
        // Thêm khách hàng vào cơ sở dữ liệu
        $sql = "INSERT INTO khachhang (tenkhachhang, diachi, dienthoai, email, matkhau, id_role) VALUES ('$tenkhachhang', '$diachi', '$dienthoai', '$email', '$matkhau_moi', 1)";

        if (mysqli_query($connect, $sql)) {
            // Đăng ký thành công
            $_SESSION['id_khachhang'] = mysqli_insert_id($connect); // Lưu ID khách hàng vào session
            $_SESSION['tenkhachhang'] = $tenkhachhang; // Lưu tên khách hàng vào session
            $_SESSION['diachi'] = $diachi; // Lưu tên khách hàng vào session
            $_SESSION['dienthoai'] = $dienthoai; // Lưu tên khách hàng vào session

            header("Location:http://localhost/webbanhang"); // Điều hướng đến trang giỏ hàng hoặc bất kỳ trang nào bạn muốn
            exit();
        } else {
            $error_message = "Có lỗi xảy ra. Vui lòng thử lại!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- Thêm Font Awesome -->
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .page-header {
            background: url('/webbanhang/img/anhdangky.jpg') no-repeat center center fixed; /* Đặt ảnh nền */
            background-size: cover; /* Ảnh sẽ phủ đầy */
            height: 100vh; /* Chiếm toàn bộ chiều cao màn hình */
            display: flex;
            align-items: center; /* Căn giữa theo chiều dọc */
            justify-content: center; /* Căn giữa theo chiều ngang */
        }

        .form-section {
            width: 100%; /* Giữ chiều rộng 100% */
            max-width: 400px; /* Giới hạn chiều rộng tối đa của form */
            padding: 20px; /* Giữ lại padding nếu cần */
            display: flex;
            flex-direction: column; /* Sắp xếp các phần tử theo cột */
            align-items: center; /* Căn giữa các phần tử bên trong */
            background-color: rgba(255, 255, 255, 0.8); /* Nền trắng mờ */
            border-radius: 10px; /* Bo góc cho form */
        }

        .form-section h2 {
            text-align: center;
            color: #333;
        }

        .form-group {
            position: relative; /* Để đặt icon vào vị trí chính xác */
            width: 100%; /* Chiều rộng của nhóm form */
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"] {
            border: 1px solid #ddd; /* Viền nhẹ cho input */
            background: none; /* Xóa nền cho input */
            margin-bottom: 10px; /* Khoảng cách giữa các trường nhập */
            width: 100%; /* Chiều rộng của input */
            padding: 8px 30px 8px 40px; /* Thêm padding bên trái cho icon */
            color: #333; /* Màu chữ */
            box-shadow: none; /* Xóa bóng cho input */
        }

        .form-group i {
            position: absolute; /* Đặt icon ở vị trí chính xác */
            left: 10px; /* Căn trái cho icon */
            top: 50%; /* Căn giữa theo chiều dọc */
            transform: translateY(-50%); /* Căn giữa hoàn hảo */
            color: black; /* Màu của icon */
        }

        .form-section button {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            background-color: #ff4081;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-section button:hover {
            background-color: #e0356b;
        }

        .form-section p {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }

        .form-section p a {
            color: #ff4081;
            text-decoration: none;
        }
    </style>
    
</head>
<body>

<main class="main-content mt-0">
    <section>
        <div class="page-header">
            <div class="form-section">
                <h2>Đăng Ký</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <i class="fas fa-user"></i>
                        <input type="text" name="tenkhachhang" placeholder="Tên khách hàng" required>
                    </div>
                    <div class="form-group">
                        <i class="fas fa-home"></i>
                        <input type="text" name="diachi" placeholder="Địa chỉ" required>
                    </div>
                    <div class="form-group">
                        <i class="fas fa-phone"></i>
                        <input type="text" name="dienthoai" placeholder="Số điện thoại" required>
                    </div>
                    <div class="form-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Email" required class="form-control mb-3" autocomplete="new-email">
                    </div>
                    <div class="form-group">
                        <i class="fas fa-key"></i>
                        <input type="password" name="matkhau" placeholder="Mật khẩu" required class="form-control mb-3" autocomplete="new-password">
                    </div>
                    <label>
                        <input type="checkbox"> Tôi đồng ý <a href="#">Điều khoản và điều kiện</a>
                    </label>
                    <button type="submit">ĐĂNG KÝ</button>
                </form>
                <p>Bạn đã có tài khoản? <a href="http://localhost/webbanhang/pages/main/dangnhap.php">Đăng Nhập</a></p>
            </div>
        </div>
    </section>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>

</body>
</html>
<?php
// Kết thúc bộ đệm đầu ra và xóa nó
ob_end_flush();
?>
