<style> /* Tạo hiệu ứng fade-in và fade-out cho discount-circle */
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

<?php
session_start(); // Khởi động session

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

// Kiểm tra nếu có sản phẩm được thêm vào giỏ hàng
if (isset($_POST['id'])) {
    $productId = $_POST['id'];

    // Kiểm tra xem biến màu sắc và kích thước có tồn tại không
    $color = isset($_POST['color']) ? $_POST['color'] : '';
    $size = isset($_POST['size']) ? $_POST['size'] : '';
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 1;

    // Tạo một khóa duy nhất cho sản phẩm trong giỏ hàng
    $cartKey = $productId . '|' . $color . '|' . $size;

    // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
    if (!array_key_exists($cartKey, $_SESSION['cart'])) {
        // Thêm sản phẩm vào giỏ hàng
        $_SESSION['cart'][$cartKey] = $quantity; // Số lượng
    } else {
        // Tăng số lượng nếu sản phẩm đã có trong giỏ hàng
        $_SESSION['cart'][$cartKey] += $quantity;
    }
}

// Xử lý xóa sản phẩm khỏi giỏ hàng
if (isset($_GET['xoa_sanpham'])) {
    $keyToDelete = $_GET['xoa_sanpham'];
    if (array_key_exists($keyToDelete, $_SESSION['cart'])) {
        unset($_SESSION['cart'][$keyToDelete]); // Xóa sản phẩm khỏi giỏ hàng
    }
    header('Location: giohang.php'); // Chuyển hướng lại giỏ hàng
    exit();
}

// Truy vấn thông tin sản phẩm từ cơ sở dữ liệu
$products = [];
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_map(function($key) {
        return explode('|', $key)[0]; // Lấy id sản phẩm từ khóa giỏ hàng
    }, array_keys($_SESSION['cart'])));

    // Cập nhật truy vấn SQL
    $query = "SELECT sp.id_sanpham, sp.tensanpham, sp.giasanpham, sp.anhsanpham, 
                     GROUP_CONCAT(DISTINCT m.tenmau) as colors, 
                     GROUP_CONCAT(DISTINCT k.tenkichthuoc) as sizes
              FROM sanpham sp
              LEFT JOIN mausanpham mp ON sp.id_sanpham = mp.id_sanpham
              LEFT JOIN mau m ON mp.id_mau = m.id_mau
              LEFT JOIN kichthuocsanpham ksp ON sp.id_sanpham = ksp.id_sanpham
              LEFT JOIN kichthuoc k ON ksp.id_kichthuoc = k.id_kichthuoc
              WHERE sp.id_sanpham IN ($ids)
              GROUP BY sp.id_sanpham";

    $result = $connect->query($query);

    while ($row = $result->fetch_assoc()) {
        $products[$row['id_sanpham']] = $row;
    }

    // Kiểm tra và xóa sản phẩm không tồn tại trong cơ sở dữ liệu
    foreach ($_SESSION['cart'] as $key => $quantity) {
        $productId = explode('|', $key)[0];
        if (!array_key_exists($productId, $products)) {
            unset($_SESSION['cart'][$key]); // Xóa sản phẩm không tồn tại khỏi giỏ hàng
        }
    }
}

// Xử lý cập nhật số lượng sản phẩm
if (isset($_POST['update'])) {
    foreach ($_POST['quantity'] as $key => $quantity) {
        $_SESSION['cart'][$key] = (int)$quantity; // Cập nhật số lượng
        // Nếu số lượng bằng 0 thì xóa sản phẩm khỏi giỏ hàng
        if ($_SESSION['cart'][$key] <= 0) {
            unset($_SESSION['cart'][$key]);
        }
    }
    header('Location: giohang.php'); // Chuyển hướng lại giỏ hàng
    exit();
}
include '../header.php'; 
?>
<!-- Cart Start -->
<style>
    .summary-box h4,
    .summary-box h5,
    table thead th,
    table tbody td {
        color: #000; /* Đảm bảo chữ màu đen */
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

<div class="container-fluid">
    <div class="row px-xl-5">
        <div class="col-lg-8 table-responsive mb-5">
            <form method="post" action="giohang.php"> <!-- Thêm form để gửi cập nhật -->
                <table class="table table-light table-borderless table-hover text-center mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>Thông tin Sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Tổng</th>
                            <th>Xóa</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle">
                        <?php
                        $total = 0; // Khởi tạo tổng tiền
                        foreach ($_SESSION['cart'] as $key => $quantity) {
                            $parts = explode('|', $key);
                            
                            // Kiểm tra số phần tử
                            if (count($parts) === 3) {
                                list($id, $color, $size) = $parts; // Lấy id, màu sắc và kích thước

                                if (isset($products[$id])) {
                                    $product = $products[$id];
                                    $subtotal = $product['giasanpham'] * $quantity;
                                    $total += $subtotal; // Cộng dồn tổng tiền
                                    echo '<tr>
                                            <td class="align-middle"><img src="/webbanhang/admin/modules/quanlysanpham/img/' . $product['anhsanpham'] . '" alt="' . $product['tensanpham'] . '" style="width: 50px;"> ' . $product['tensanpham'] . ' - Màu: ' . htmlspecialchars($color) . ' - Kích thước: ' . htmlspecialchars($size) . '</td>
                                            <td class="align-middle">' . number_format($product['giasanpham'], 0, ',', '.') . ' VND</td>
                                            <td class="align-middle">
                                                <div class="input-group quantity mx-auto" style="width: 100px;">
                                                    <div class="input-group-prepend">
                                                        <button class="btn btn-sm" style="background-color: #A8D25A; color: white; border: none;"type="button" onclick="updateQuantity(\'' . $key . '\', -1)">-</button>
                                                    </div>
                                                    <input type="text" name="quantity[' . $key . ']" class="form-control form-control-sm bg-secondary border-0 text-center" value="' . $quantity . '" readonly>
                                                    <div class="input-group-append">
                                                       <button class="btn btn-sm" style="background-color: #A8D25A; color: white; border: none;"type="button" onclick="updateQuantity(\'' . $key . '\', 1)">+</button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle total-price" data-key="' . $key . '" data-price="' . $product['giasanpham'] . '">' . number_format($subtotal, 0, ',', '.') . ' VND</td>
                                            <td class="align-middle">
                                                <a href="giohang.php?xoa_sanpham=' . $key . '" onclick="return confirm(\'Bạn có chắc chắn muốn xóa sản phẩm này không?\')" class="btn btn-sm btn-danger">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                            </td>
                                        </tr>';
                                        
                                } else {
                                    // Nếu sản phẩm không tồn tại trong cơ sở dữ liệu
                                    echo '<tr>
                                            <td colspan="5" class="text-danger">Có lỗi xảy ra với sản phẩm ID: ' . htmlspecialchars($id) . '</td>
                                          </tr>';
                                }
                            } else {
                                // Xử lý trường hợp không đủ dữ liệu
                                echo '<tr>
                                        <td colspan="5" class="text-danger">Có lỗi xảy ra với giỏ hàng ID: ' . htmlspecialchars($key) . '</td>
                                      </tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
                <button type="submit" name="update" class="btn btn-sm" style=" border: 2px solid #A8D25A;border-radius: 50px; color: black;background-color: transparent; padding: 10px 20px;font-weight: bold;text-decoration: none;transition: all 0.3s ease;" >Cập nhật giỏ hàng</button> 
            </form>
        </div>

<div class="col-lg-4">
    <div class="border-bottom mb-4 summary-box">
        <h4 class="mb-4">Tổng Tiền Giỏ Hàng</h4>
        <h5 class="font-weight-bold" id="totalAmount">Tổng: <?php echo number_format($total, 0, ',', '.'); ?> VND</h5>
        <!-- Form mã giảm giá -->
        <form action="" method="POST" class="discount-form mt-3">
            <input 
                type="text" 
                name="discount_code" 
                placeholder="Nhập mã giảm giá (nếu có)" 
                class="form-control discount-input">
            <button type="submit" class="btn btn-sm discount-btn">Áp dụng</button>
        </form>
        
        <!-- Nút thanh toán -->
        <a href="thanhtoan.php" class="btn btn-sm custom-btn">Thanh toán</a>
    </div>
</div>
<?php
// Nếu giỏ hàng trống, hiện thông báo "Giỏ hàng trống"
if (empty($_SESSION['cart'])) {
    echo '<p class="text-center mt-4">Giỏ hàng trống</p>';
}
?>

<script>
    // Thêm sự kiện click để kiểm tra giỏ hàng trước khi thanh toán
document.querySelector('.custom-btn').addEventListener('click', function(event) {
    // Kiểm tra giỏ hàng từ phía PHP
    <?php if (empty($_SESSION['cart'])): ?>
        event.preventDefault(); // Ngăn chặn hành động mặc định (điều hướng)
        alert("Giỏ hàng của bạn đang trống. Vui lòng thêm sản phẩm trước khi thanh toán.");
    <?php endif; ?>
});

</script>
<style>
    /* Phong cách khung tổng tiền */
    .summary-box {
        display: flex;            /* Sử dụng flexbox */
        flex-direction: column;   /* Căn chỉnh theo cột */
        align-items: center;      /* Căn giữa nội dung theo chiều ngang */
        justify-content: center;  /* Căn giữa nội dung theo chiều dọc */
        border: 2px solid #A8D25A; /* Khung màu xanh */
        border-radius: 10px;       /* Bo góc khung */
        padding: 20px;            /* Khoảng cách bên trong */
        background-color: #ececec; /* Màu nền xám nhạt */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Đổ bóng */
    }

    /* Phong cách nút "Thanh toán" */
    .custom-btn {
        border: 2px solid #A8D25A;
        border-radius: 50px;
        color: black;
        background-color: transparent;
        padding: 10px 20px;
        font-weight: bold;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .custom-btn:hover {
        background-color: #5edc15;
        color: black;
    }

    /* Phong cách form mã giảm giá */
    .discount-form {
        display: flex;           /* Sử dụng flexbox để căn chỉnh hàng ngang */
        gap: 10px;               /* Khoảng cách giữa input và button */
        margin-top: 20px;        /* Khoảng cách trên */
        width: 100%;             /* Độ rộng full */
        justify-content: center; /* Căn giữa nội dung */
    }

    .discount-input {
        flex: 1;                 /* Input chiếm toàn bộ chiều rộng còn lại */
        padding: 8px;
        border: 2px solid #A8D25A;
        border-radius: 5px;
        font-size: 14px;
    }

    .discount-btn {
        border: 2px solid #A8D25A;
        background-color: transparent;
        border-radius: 50px;
        padding: 8px 16px;
        color: red;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .discount-btn:hover {
        background-color: #5edc15;
        color: red;
    }
</style>
    </div>
</div>
<!-- Cart Start -->
<script>
function updateQuantity(key, change) {
    const inputField = document.querySelector(`input[name="quantity[${key}]"]`);
    let currentValue = parseInt(inputField.value);
    currentValue += change;

    // Giữ số lượng tối thiểu là 1
    if (currentValue < 1) {
        currentValue = 1;
    }

    inputField.value = currentValue;

    // Lấy giá sản phẩm cho mỗi hàng dựa trên key
    const pricePerItem = parseFloat(document.querySelector(`.total-price[data-key="${key}"]`).dataset.price);
    const totalPriceCell = document.querySelector(`.total-price[data-key="${key}"]`);
    const subtotal = pricePerItem * currentValue;
    totalPriceCell.innerText = new Intl.NumberFormat('vi-VN', { style: 'decimal' }).format(subtotal) + ' VND';

    // Cập nhật tổng số tiền
    let totalAmount = 0;
    document.querySelectorAll('.total-price').forEach((cell) => {
        totalAmount += parseFloat(cell.innerText.replace(/\./g, '').replace(' VND', ''));
    });

    document.getElementById('totalAmount').innerText = 'Tổng: ' + new Intl.NumberFormat('vi-VN', { style: 'decimal' }).format(totalAmount) + ' VND';
}
</script>
<!-- Cart End -->
 <!-- Sản phẩm gợi ý -->
<div class="container-fluid pb-5">
<h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4 text-center" style="font-size: 1.5rem; font-weight: bold; color: black;">
        <span class="bg-secondary pr-3">Có thể bạn sẽ thích</span>
    </h2>     
       <div class="row px-xl-5">
            <?php
            // Lấy id sản phẩm từ tham số URL
            if (isset($_GET['id'])) {
                $id_sanpham = $_GET['id'];
            } else {
                $id_sanpham = 0; // Hoặc thông báo lỗi
            }

// Lấy sản phẩm gợi ý, chỉ hiển thị sản phẩm không phải hết hàng
$query_sanpham_goiy = "SELECT * FROM sanpham WHERE id_sanpham != $id_sanpham  ORDER BY RAND() LIMIT 12  "; // Lọc sản phẩm không phải hết hàng
$result_goiy = mysqli_query($connect, $query_sanpham_goiy);
            
            // Kiểm tra và hiển thị sản phẩm gợi ý
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
                           echo '<div class="discount-circle position-absolute top-0 start-0 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; background-color: red; color: white; border-radius: 50%; opacity: 1; transition: opacity 1s;">' . $phanTramGiam . '%</div>';
                                }

                    echo '            <img class="img-fluid w-100" src="/webbanhang/admin/modules/quanlysanpham/img/' . $row["anhsanpham"] . '" alt="">';
                            // Hiển thị lớp phủ nếu sản phẩm hết hàng
        if ($row["soluongsanpham"] == 0) {
            echo '<div class="overlay position-absolute top-0 left-0 right-0 bottom-0 d-flex align-items-center justify-content-center">';
            echo '    <span>Hết hàng</span>';
            echo '</div>';
        }

                    echo '        </div>';
                    echo '        <div class="text-center py-4">';
                    echo '            <h5 class="h6 text-decoration-none text-truncate">' . $row["tensanpham"] . '</h5>';

                    echo '<a class="btn" href="/webbanhang/pages/main/chitietsp.php?id=' . $row["id_sanpham"] . '" style="color: white; background-color: green; border: none; padding: 2px 10px; border-radius: 15px; display: inline-block; text-align: center; text-decoration: none; margin-top: 10px;"> <i class="fas fa-shopping-basket"></i>Mua ngay</a>';
                    echo '            <div class="d-flex align-items-center justify-content-center mt-2">';
                    echo '               <h5 style="color: red;">' . number_format($row["giasanpham"], 0, ',', '.') . ' VND</h5>';
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
            ?>
        </div>
</div>
</div>
<!-- Đóng Sản phẩm gợi ý -->



<?php include '../footer.php'; // Bao gồm footer ?>
