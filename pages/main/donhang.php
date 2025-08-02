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

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['id_khachhang'])) {
    echo "Vui lòng đăng nhập để xem đơn hàng của bạn.";
    exit;
}

// Lấy id_khachhang từ session
$id_khachhang = $_SESSION['id_khachhang'];

// Truy vấn lấy thông tin đơn hàng
$query = "SELECT * FROM donhang WHERE id_khachhang = $id_khachhang ORDER BY thoigian DESC"; // Sắp xếp theo thời gian đặt hàng mới nhất
$result = $connect->query($query);

if ($result->num_rows > 0) {
    echo "<h2 style='color: black; font-weight: bold;'>Đơn hàng </h2>";
    echo "<table class='lietkesp'>"; // Thay đổi thành lớp CSS
    echo "<thead>";
    echo "<tr>";
    echo "<th>STT</th>";
    echo "<th>Mã đơn hàng</th>";
    echo "<th>Ảnh</th>";
    echo "<th>Màu</th>";
    echo "<th>Kích thước</th>";
    echo "<th>Số lượng</th>";
    echo "<th>Giá tiền</th>";
    echo "<th>Thanh toán</th>";
    echo "<th>Thời gian đặt</th>";
    echo "<th>Tình trạng</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    $stt = 1; // Biến số thứ tự
    while ($row = $result->fetch_assoc()) {
        // Lấy thông tin đơn hàng
        $donhang_id = $row['id_donhang'];

        // Truy vấn để lấy chi tiết đơn hàng và ảnh sản phẩm
        $query_details = "SELECT dd.*, sp.anhsanpham FROM donhang_details dd JOIN sanpham sp ON dd.id_sanpham = sp.id_sanpham WHERE dd.id_donhang = $donhang_id";
        $result_details = $connect->query($query_details);

        if ($result_details->num_rows > 0) {
            while ($detail = $result_details->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $stt++ . "</td>"; // Hiển thị số thứ tự
                echo "<td>" . $row['madonhang'] . "</td>";

                // Hiển thị ảnh sản phẩm
                $image_path = '/webbanhang/admin/modules/quanlysanpham/img/' . $detail['anhsanpham']; // Thay đổi đường dẫn cho đúng
                echo '<td>
                        <img class="img-fluid" src="' . $image_path . '" alt="Ảnh sản phẩm" style="width: 50px; height: 50px; object-fit: cover;">
                      </td>';

                echo "<td>" . $detail['mau'] . "</td>";
                echo "<td>" . $detail['kichthuoc'] . "</td>";
                echo "<td>" . $detail['soluong'] . "</td>";
                
                // Hiển thị giá tiền (Nhân số lượng với giá sản phẩm)
                $total_price = $detail['gia_khi_mua'] * $detail['soluong']; // Tính tổng giá
                echo "<td>" . number_format($total_price) . " VNĐ</td>"; // Hiển thị tổng giá tiền

                echo "<td>" . $row['hinhthuc_thanhtoan'] . "</td>";
                echo "<td>" . $row['thoigian'] . "</td>";
                
                // Chuyển đổi giá trị trạng thái từ số sang chữ
                $tinhtrang_hien_thi = '';
                if ($row['tinhtrang'] == 0) {
                    $tinhtrang_hien_thi = 'Đơn hàng mới';
                } elseif ($row['tinhtrang'] == 1) {
                    $tinhtrang_hien_thi = 'Đang giao';
                } elseif ($row['tinhtrang'] == 2) {
                    $tinhtrang_hien_thi = 'Đã xác nhận';
                } else {
                    $tinhtrang_hien_thi = 'Trạng thái không xác định'; // Trường hợp không xác định
                }
                
                echo "<td>" . $tinhtrang_hien_thi . "</td>"; // Hiển thị trạng thái
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='10'>Không có chi tiết sản phẩm cho đơn hàng này.</td></tr>";
        }
    }
    echo "</tbody>";
    echo "</table>";
} else {
    echo "<p>Bạn chưa đặt hàng nào.</p>"; // Đảm bảo chữ màu đen
}

$connect->close();
?>
<style>
    <style>
    body {
        background-color: #00AEEF; /* Nền xanh nước biển */
    }
    .lietkesp {
        width: 100%;
        border-collapse: collapse;
        color: black; /* Màu chữ cho các ô dữ liệu */
        text-align: center;
    }
    .lietkesp th {
        background-color: limegreen; /* Màu xanh lá cây cho tiêu đề */
        color: black; /* Đổi màu chữ tiêu đề thành đen */
        padding: 2px; /* Thêm padding nhỏ để tạo không gian cho chữ */
        line-height: 1; /* Điều chỉnh line-height để sát với chữ */
        height: auto; /* Để chiều cao tự động theo nội dung */
    }
    .lietkesp td {
        padding: 2px; /* Thêm padding nhỏ để tạo không gian cho chữ */
        border: 1px solid black;
        line-height: 1; /* Điều chỉnh line-height để sát với chữ */
        height: auto; /* Để chiều cao tự động theo nội dung */
    }
    .header_lietke {
        font-weight: bold;
    }
    .inputdonhang {
        color: white;
        background-color: black;
        padding: 0 5px; /* Giảm padding chỉ ở hai bên để gọn nút */
        border-radius: 4px;
        text-decoration: none;
        font-size: 12px; /* Thu nhỏ font chữ */
        line-height: 1; /* Sát nội dung chữ */
    }
    .inputdonhang:hover {
        background-color: darkgray;
    }
    </style>
</style>
<?php include '../footer.php'; ?>