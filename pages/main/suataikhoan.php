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

// Lấy ID khách hàng từ session
$id_khachhang = $_SESSION['id_khachhang'];

// Cập nhật thông tin tài khoản nếu biểu mẫu đã được gửi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenkhachhang = trim($_POST['tenkhachhang']);
    $diachi = trim($_POST['diachi']);
    $dienthoai = trim($_POST['dienthoai']);
    $email = trim($_POST['email']);

    // Kiểm tra xem các trường không để trống
    if (empty($tenkhachhang) || empty($diachi) || empty($dienthoai) || empty($email)) {
        echo "Vui lòng điền đầy đủ thông tin.";
    } else {
        // Cập nhật thông tin khách hàng trong cơ sở dữ liệu
        $sql = "UPDATE khachhang SET 
                    tenkhachhang = '$tenkhachhang', 
                    diachi = '$diachi', 
                    dienthoai = '$dienthoai', 
                    email = '$email' 
                WHERE id_khachhang = '$id_khachhang'";
        
        if ($connect->query($sql) === TRUE) {
            // Cập nhật session với tên mới
            $_SESSION['tenkhachhang'] = $tenkhachhang;
            header("Location: http://localhost/webbanhang/pages/main/thongtintaikhoan.php"); 
            exit();
        } else {
            echo "Lỗi cập nhật: " . $connect->error; // In ra lỗi nếu có
        }
    }
}

// Lấy thông tin khách hàng từ cơ sở dữ liệu
$sql = "SELECT * FROM khachhang WHERE id_khachhang = '$id_khachhang'";
$result = mysqli_query($connect, $sql);
$khachhang = mysqli_fetch_assoc($result);

if (!$khachhang) {
    echo "Không tìm thấy thông tin tài khoản.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Thông Tin Tài Khoản</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Chiều cao toàn màn hình */
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px; /* Chiều rộng của form */
            box-sizing: border-box;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input {
            width: 100%; /* Để tất cả các khung có chiều dài bằng nhau */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            height: 40px; /* Chiều cao cố định cho input */
        }
        .button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            display: block;
            width: 100%; /* Để nút chiếm chiều rộng của form */
            text-decoration: none;
            font-weight: bold;
            margin-top: 10px; /* Khoảng cách giữa button và form */
            box-sizing: border-box;
        }
        .button:hover {
            background-color: #45a049;
        }
        .back-link {
            text-align: center;
            margin-top: 15px;
            display: block;
            text-decoration: none;
            color: #007BFF;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Sửa Thông Tin Tài Khoản</h2>

        <!-- Biểu mẫu cập nhật thông tin tài khoản -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="tenkhachhang">Tên Khách Hàng:</label>
                <input type="text" name="tenkhachhang" id="tenkhachhang" value="<?php echo htmlspecialchars($khachhang['tenkhachhang']); ?>">
            </div>
            <div class="form-group">
                <label for="diachi">Địa Chỉ Nhận Hàng:</label>
                <input type="text" name="diachi" id="diachi" value="<?php echo htmlspecialchars($khachhang['diachi']); ?>">
            </div>
            <div class="form-group">
                <label for="dienthoai">Điện Thoại:</label>
                <input type="text" name="dienthoai" id="dienthoai" value="<?php echo htmlspecialchars($khachhang['dienthoai']); ?>">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($khachhang['email']); ?>">
            </div>

            <button type="submit" class="button">Cập nhật</button>
        </form>

        <a href="/webbanhang/pages/main/thongtintaikhoan.php" class="back-link">Quay lại</a>
    </div>

</body>
</html>
