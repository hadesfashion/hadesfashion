<?php
session_start(); // Khởi động session
include '../header.php';

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

// Xử lý dữ liệu form khi người dùng xác nhận thanh toán
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten_khach_hang = $_POST['hoten'];
    $so_dien_thoai = $_POST['dienthoai'];
    $dia_chi_nhan_hang = $_POST['diachi'];
    $hinh_thuc_thanhtoan = $_POST['hinhthucthanhtoan']; // Tên phải khớp với tên trong form

    // Kiểm tra nếu người dùng đã đăng nhập và id_khachhang tồn tại trong session
    if (!isset($_SESSION['id_khachhang'])) {
        echo "Vui lòng đăng nhập trước khi đặt hàng.";
        exit;
    }

    $id_khachhang = $_SESSION['id_khachhang']; // Lấy id_khachhang từ session

    // Kiểm tra giỏ hàng
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
// Mã đơn hàng ngẫu nhiên, độ dài 4 ký tự (tất cả là số)
$madonhang = substr(rand(1000, 9999), 0, 4);
         
        // Thêm đơn hàng vào bảng `donhang`
        $query = "INSERT INTO donhang (madonhang, id_khachhang, tinhtrang, hinhthuc_thanhtoan)
                  VALUES ('$madonhang', $id_khachhang, '0', '$hinh_thuc_thanhtoan')";

        if ($connect->query($query) === TRUE) {
            $donhang_id = $connect->insert_id; // Lấy id của đơn hàng mới

            // Duyệt qua từng sản phẩm trong giỏ hàng
            foreach ($_SESSION['cart'] as $key => $quantity) {
                $parts = explode('|', $key);
                if (count($parts) === 3) {
                    list($id_sanpham, $color, $size) = $parts;

                    // Truy vấn lấy giá sản phẩm
                    $result = $connect->query("SELECT * FROM sanpham WHERE id_sanpham = $id_sanpham");
                    if ($row = $result->fetch_assoc()) {
                        $gia = $row['giasanpham'];
                        $tongtien = $gia * $quantity;

                        // Thêm sản phẩm vào bảng `donhang_details`
                        $query_details = "INSERT INTO donhang_details (id_donhang, id_sanpham, soluong, gia_khi_mua, mau, kichthuoc)
                                          VALUES ($donhang_id, $id_sanpham, $quantity, $gia, '$color', '$size')";
                        $connect->query($query_details);
                    }
                }
            }

            // Thông báo thành công
            echo "
            <div style='text-align: center; padding: 20px;'>
                <h2 style='color: green;'>CẢM ƠN QUÝ KHÁCH!</h2>
                <p>Đơn hàng của bạn đã được đặt thành công!</p>
                <p>Hãy kiểm tra Email để nhận thông báo.</p>
                <br>
                <a href='donhang.php' style='display: inline-block; padding: 10px 20px; background-color: blue; color: white; text-decoration: none;'>Xem đơn hàng của bạn</a>
                <br><br>
                <a href='http://localhost/webbanhang/' style='display: inline-block; padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none;'>Ấn vào đây để tiếp tục mua hàng</a>
            </div>";
            
            // Xóa giỏ hàng sau khi đặt đơn
            unset($_SESSION['cart']);
        } else {
            echo "Lỗi: " . $query . "<br>" . $connect->error;
        }
    } else {
        echo "Giỏ hàng của bạn đang trống. Vui lòng chọn sản phẩm trước khi thanh toán.";
    }
}

$connect->close();
include '../footer.php';
?>
