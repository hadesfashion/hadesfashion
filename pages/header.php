<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>CỬA HÀNG HADES</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free HTML Templates" name="keywords">
    <meta content="Free HTML Templates" name="description">
    <link href="/webbanhang/img/favicon.ico" rel="icon">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">  
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="/webbanhang/lib/animate/animate.min.css" rel="stylesheet">
    <link href="/webbanhang/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="/webbanhang/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="/webbanhang/css/giaodien.css">
    <style>
        .header {
            background-color:  pink ;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 20px;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }
        .account-links a, .cart-shopping a {
            color: white ;
            text-decoration: none;
            margin: 0 10px;
        }
        .header .logo-header {
            font-size: 24px;
            font-weight: bold;
        }
        .menu-header a {
            color: white;
            font-size: 16px;
            text-decoration: none;
            padding: 5px 10px;
        }

        .menu-header a {
    display: inline-block;
    position: relative; /* Để chứa phần tử hiệu ứng bên trong */
    text-decoration: none; /* Bỏ gạch chân mặc định */
    color: #000; /* Màu chữ đen */
    overflow: hidden; /* Để hiệu ứng không tràn ra ngoài */
    transition: color 0.3s ease; /* Hiệu ứng mượt khi thay đổi màu */
}

.menu-header a:hover {
    color: #000; /* Giữ màu đen khi hover */
}

/* Hiệu ứng ripple */
.menu-header a::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(0, 0, 0, 0.1); /* Màu sắc của sóng (đen nhạt) */
    border-radius: 50%; /* Bo tròn tạo sóng */
    transform: translate(-50%, -50%);
    transition: width 0.6s ease, height 0.6s ease, opacity 0.6s ease;
    opacity: 0;
}

.menu-header a:hover::after {
    width: 200%; /* Kích thước sóng lan tỏa */
    height: 200%; /* Kích thước sóng lan tỏa */
    opacity: 1; /* Hiển thị sóng */
}
    </style>
</head>

<body style="padding-top: 80px;">
    <!-- Header -->
    <div class="header">
        <a href="http://localhost/webbanhang/" class="logo-header" style="color: black;">HADES STUDIO</a>

        <!-- Menu hiển thị trên header -->
        <div class="menu-header" style="display: inline-flex; gap: 15px; margin-left: 20px;">
            <?php
            // Kết nối đến cơ sở dữ liệu
            $connect = mysqli_connect('localhost', 'root', '', 'vidu');

            // Truy vấn lấy tất cả các mục menu có trạng thái hiển thị
            $sql = "SELECT * FROM menu WHERE trangthai = 1";
            $query = mysqli_query($connect, $sql);

            // Duyệt qua các mục menu và hiển thị
            while ($row = mysqli_fetch_assoc($query)) {
                $iconClass = '';

                // Gán icon dựa vào tên menu
                $tenmenu = trim($row['tenmenu']);
                switch ($row['tenmenu']) {
                }
            echo '<a href="' . $row['lienket'] . '" style="color: black; text-decoration: none;">';
                echo '<i class="' . $iconClass . '" style="margin-right: 5px;"></i>';
                echo $row['tenmenu'];
                echo '</a>';
            }
            ?>
        </div>

        <!-- Tài khoản người dùng -->
        <div class="account-links">
            <?php if (isset($_SESSION['tenkhachhang'])): ?>
                <a href="http://localhost/webbanhang/pages/main/thongtintaikhoan.php" style="color: black;">
                    <i class="fas fa-user" style="color:black; margin-right: 5px;"></i>
                    Xin chào, <?php echo htmlspecialchars($_SESSION['tenkhachhang']); ?>
                </a>
                /
                <a href="http://localhost/webbanhang/pages/main/logout.php" style="color: black;">Đăng xuất</a>
            <?php else: ?>
                <a href="http://localhost/webbanhang/pages/main/dangnhap.php" style="color: black;">
                    <i class="fas fa-user" style="color:black; margin-right: 5px;"></i>
                    Đăng nhập
                </a>
                /
                <a href="http://localhost/webbanhang/pages/main/dangky.php" style="color: black;">Đăng ký</a>
            <?php endif; ?>
        </div>

   
    </div>

</body>
</html>
