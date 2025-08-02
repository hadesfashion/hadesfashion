<style>
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
 /* Tạo hiệu ứng fade-in và fade-out cho discount-circle */
 @keyframes fadeInOut {
        0%, 100% { opacity: 1; }
        50% { opacity: 0; }
    }

    .discount-circle {
        width: 40px;
        height: 40px;
        background-color: red;
        color: white;
        border-radius: 50%;
        font-weight: bold;
        animation: fadeInOut 2s infinite; /* Hiệu ứng mờ dần và hiện lên */
    }
</style>
<!--css hết hàng -->
<style>
.product-item.out-of-stock {
    position: relative;
    opacity: 1.5; /* Làm mờ sản phẩm */
    pointer-events: none; /* Vô hiệu hóa click */
}

.product-item.out-of-stock .overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.8); /* Lớp phủ đen mờ */
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    z-index: 1;
    text-transform: uppercase;
    font-weight: bold;
    border-radius: 5px; /* Bo góc */
}

.product-item.out-of-stock img {
    filter: grayscale(100%); /* Làm đen trắng ảnh */
}
</style>

<style>
    /* Căn chỉnh hình ảnh và văn bản trong bảng */
    .table tbody td {
        vertical-align: middle; /* Căn giữa theo chiều dọc */
        text-align: left; /* Căn văn bản sang trái */
        padding: 10px; /* Khoảng cách trong ô */
    }

    .table img {
        display: inline-block; /* Đảm bảo ảnh là phần tử nội tuyến */
        vertical-align: middle; /* Căn giữa với văn bản */
        margin-right: 10px; /* Tạo khoảng cách giữa ảnh và tên sản phẩm */
        width: 50px; /* Đảm bảo kích thước đồng nhất */
        height: 50px; /* Đảm bảo kích thước đồng nhất */
        object-fit: cover; /* Cắt ảnh cho đồng nhất */
    }

    .table thead th {
        text-align: center; /* Căn giữa tiêu đề bảng */
    }
</style>
 
<style>
    .summary-box h4,
    .summary-box h5,
    table thead th,
    table tbody td {
        color: #000; /* Đảm bảo chữ màu đen */
    }
</style>


<?php
session_start(); // Khởi động session

// Cập nhật thông tin khách hàng vào session nếu form được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['tenkhachhang'] = $_POST['hoten'];
    $_SESSION['dienthoai'] = $_POST['dienthoai'];
    $_SESSION['diachi'] = $_POST['diachi'];
}

// Kiểm tra nếu giỏ hàng chưa được khởi tạo
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

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

// Truy vấn thông tin sản phẩm từ cơ sở dữ liệu
$products = [];
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_map(function($key) {
        return explode('|', $key)[0]; // Lấy id sản phẩm từ khóa giỏ hàng
    }, array_keys($_SESSION['cart'])));

    $query = "SELECT sp.id_sanpham, sp.tensanpham, sp.giasanpham, sp.anhsanpham 
              FROM sanpham sp
              WHERE sp.id_sanpham IN ($ids)";

    $result = $connect->query($query);

    while ($row = $result->fetch_assoc()) {
        $products[$row['id_sanpham']] = $row;
    }
}

// Đặt phí vận chuyển
$phi_van_chuyen = 10000; 

// Lấy thông tin khách hàng từ session (nếu đã đăng nhập)
$tenkhachhang = isset($_SESSION['tenkhachhang']) ? $_SESSION['tenkhachhang'] : '';
$sdtkhachhang = isset($_SESSION['dienthoai']) ? $_SESSION['dienthoai'] : '';
$diachikhachhang = isset($_SESSION['diachi']) ? $_SESSION['diachi'] : '';

// Hiển thị trang thanh toán
include '../header.php'; // Bao gồm header
?>

<!-- Checkout Start -->
<div class="container-fluid">
    <div class="row px-xl-5">
        <div class="col-lg-8 table-responsive mb-5">
            <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3">Chi tiết đơn hàng</span></h5>
            <table class="table table-light table-borderless table-hover text-center mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>Thông Tin Sản Phẩm</th>
                        <th>Giá tiền</th>
                        <th>Số lượng</th>
                        <th>Tổng</th>
                    </tr>
                </thead>
                <tbody class="align-middle">
                    <?php
                    $total = 0; // Khởi tạo tổng tiền
                    foreach ($_SESSION['cart'] as $key => $quantity) {
                        $parts = explode('|', $key);
                        
                        if (count($parts) === 3) {
                            list($id, $color, $size) = $parts;

                            if (isset($products[$id])) {
                                $product = $products[$id];
                                $subtotal = $product['giasanpham'] * $quantity;
                                $total += $subtotal; // Cộng dồn tổng tiền
                                echo '<tr>
                                        <td class="align-middle"><img src="/webbanhang/admin/modules/quanlysanpham/img/' . $product['anhsanpham'] . '" alt="' . $product['tensanpham'] . '" style="width: 50px;"> ' . $product['tensanpham'] . ' - Màu: ' . htmlspecialchars($color) . ' - Kích thước: ' . htmlspecialchars($size) . '</td>
                                        <td class="align-middle">' . number_format($product['giasanpham'], 0, ',', '.') . ' VND</td>
                                        <td class="align-middle">' . $quantity . '</td>
                                        <td class="align-middle">' . number_format($subtotal, 0, ',', '.') . ' VND</td>
                                    </tr>';
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="col-lg-4">
            <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3">Tóm tắt đơn hàng</span></h5>
            <div class="bg-light p-30 mb-5">
                <div class="border-bottom pb-2">
                    <div class="d-flex justify-content-between mb-3">
                        <h6>Tổng tiền sản phẩm</h6>
                        <h6><?php echo number_format($total, 0, ',', '.') . ' VND'; ?></h6>
                    </div>
                    <div class="d-flex justify-content-between">
                        <h6>Phí vận chuyển</h6>
                        <h6><?php echo number_format($phi_van_chuyen, 0, ',', '.') . ' VND'; ?></h6>
                    </div>
                </div>
                <div class="border-top pt-2">
                    <div class="d-flex justify-content-between mt-2">
                        <h5>Tổng cộng</h5>
                        <h5><?php echo number_format($total + $phi_van_chuyen, 0, ',', '.') . ' VND'; ?></h5>
                    </div>
                </div>
            </div>

            <!-- Form thông tin khách hàng -->
            <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3">Thông tin khách hàng</span></h5>
            <div class="bg-light p-30 mb-5">
                <form action="xulydonhang.php" method="post"> <!-- Đặt action="" để gửi lại chính trang này -->
                    <div class="form-group">
                        <label>Họ và tên</label>
                        <input class="form-control" type="text" name="hoten" value="<?php echo htmlspecialchars($tenkhachhang); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input class="form-control" type="text" name="dienthoai" value="<?php echo htmlspecialchars($sdtkhachhang); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Địa chỉ nhận hàng</label>
                        <input class="form-control" type="text" name="diachi" value="<?php echo htmlspecialchars($diachikhachhang); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Hình thức thanh toán</label>
                        <select class="form-control" name="hinhthucthanhtoan" required>
        <option value="Tiền mặt">Thanh toán khi nhận hàng</option>
    </select>                    </div>
                    <button class="btn btn-primary btn-block custom-green-button" type="submit">Xác nhận thanh toán</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Checkout End -->
 
<!-- Sản phẩm gợi ý -->
<div class="container-fluid pb-5">
    <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4 text-center" style="font-size: 1.5rem; font-weight: bold; color: black;">
        <span class="bg-secondary pr-3">Gợi ý tiếp tục mua sắm</span>
    </h2>
    <div class="row px-xl-5">
        <?php
        if (isset($_GET['id'])) {
            $id_sanpham = $_GET['id'];
        } else {
            $id_sanpham = 0; // Hoặc thông báo lỗi
        }

// Lấy sản phẩm gợi ý, chỉ hiển thị sản phẩm không phải hết hàng
$query_sanpham_goiy = "SELECT * FROM sanpham WHERE id_sanpham != $id_sanpham  ORDER BY RAND() LIMIT 12 "; // Lọc sản phẩm không phải hết hàng
$result_goiy = mysqli_query($connect, $query_sanpham_goiy);
        
        if ($result_goiy && mysqli_num_rows($result_goiy) > 0) {
            while ($row = mysqli_fetch_assoc($result_goiy)) {
            // Tính phần trăm giảm giá
            $giaKhuyenMai = $row["giakhuyenmai"];
            $giaSanPham = $row["giasanpham"];
            $phanTramGiam = ($giaKhuyenMai > 0) ? round((($giaKhuyenMai - $giaSanPham) / $giaKhuyenMai) * 100) : 0;
  
        // Kiểm tra nếu sản phẩm hết hàng
        $hetHangClass = ($row["soluongsanpham"] == 0) ? 'out-of-stock' : '';

        echo '<div class="col-lg-3 col-md-4 col-sm-6 pb-1">';
        echo '    <div class="product-item bg-light mb-4 ' . $hetHangClass . '">';
        echo '        <div class="product-img position-relative overflow-hidden">';

                        // Hiển thị phần trăm giảm giá
        if ($phanTramGiam > 0) {
            echo '            <div class="discount-circle position-absolute top-0 start-0 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; background-color: red; color: white; border-radius: 50%; opacity: 1; transition: opacity 1s;">' . $phanTramGiam . '%</div>';
        }

                echo '            <div class="suggested-product-img">';
                echo '                <img class="img-fluid w-100" src="/webbanhang/admin/modules/quanlysanpham/img/' . $row["anhsanpham"] . '" alt="" style="height: 250px; object-fit: cover;">'; // Cập nhật ảnh
                        // Hiển thị lớp phủ nếu sản phẩm hết hàng
        if ($row["soluongsanpham"] == 0) {
            echo '<div class="overlay position-absolute top-0 left-0 right-0 bottom-0 d-flex align-items-center justify-content-center">';
            echo '    <span>Hết hàng</span>';
            echo '</div>';
        }


                echo '            </div>';
                echo '            <div class="product-action">';
                echo '                <a class="btn btn-outline-dark btn-square" href="giohang.php?id=' . $row["id_sanpham"] . '"><i class="fa fa-shopping-cart"></i></a>';
                echo '                <a class="btn btn-outline-dark btn-square" href="/webbanhang/pages/main/chitietsp.php?id=' . $row["id_sanpham"] . '"><i class="far fa-eye"></i></a>';
                echo '            </div>';
                echo '        </div>';
                echo '        <div class="text-center py-4">';
                echo '            <h5 class="h6 text-decoration-none text-truncate">' . $row["tensanpham"] . '</h5>';
                echo '<a class="btn" href="/webbanhang/pages/main/chitietsp.php?id=' . $row["id_sanpham"] . '" style="color: white; background-color: green; border: none; padding: 2px 10px; border-radius: 15px; display: inline-block; text-align: center; text-decoration: none; margin-top: 10px;"> <i class="fas fa-shopping-basket"></i>Mua ngay</a>';

                echo '            <div class="d-flex align-items-center justify-content-center mt-2">';
                echo '                <h5 style="color: red;">' . number_format($row["giasanpham"], 0, ',', '.') . ' VND</h5>';
                if ($row["giakhuyenmai"] > 0) {
                    echo '                <h6 class="text-muted ml-2"><del>' . number_format($row["giakhuyenmai"], 0, ',', '.') . ' VND</del></h6>';
                }
                echo '            </div>';
                echo '            <div class="d-flex align-items-center justify-content-center mb-1">';
// Hiển thị số sao
for ($i = 0; $i < 5; $i++) {
    echo '                <small class="fa fa-star text-primary mr-1"></small>';
}

// Hiển thị số lượt xếp hạng ngẫu nhiên từ 800 đến 10000 và định dạng lại
$xephang = rand(1000, 3000); // Tạo số ngẫu nhiên trong khoảng 800 đến 10000
$xephangFormatted = number_format($xephang / 1000, 1) . 'k'; // Định dạng số lượt xếp hạng thành dạng 3.5k
echo '                <small>(' . $xephangFormatted . ')</small>'; // Hiển thị xếp hạng

 // Hiển thị số lượt bán ngẫu nhiên từ 1000 trở lên và định dạng lại
        $luotban = rand(2000, 7000); // Tạo số ngẫu nhiên trong khoảng 1000 đến 5000
        $luotbanFormatted = number_format($luotban / 1000, 1) . 'k'; // Định dạng số lượt bán
        echo '                <small class="ml-2">(' . $luotbanFormatted . ' lượt bán)</small>'; // Hiển thị lượt bán
        
        echo '            </div>';
        echo '        </div>';
        echo '    </div>';
        echo '</div>';
            }
        } else {
            echo "<p class='text-center'>Không có sản phẩm nào.</p>";
        }
        $connect->close(); // Đóng kết nối

        ?>
    </div>
</div>
<!-- Đóng Sản phẩm gợi ý -->


<?php
include '../footer.php'; // Bao gồm footer
?>
