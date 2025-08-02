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
body {
    overflow-x: hidden; /* Ẩn cuộn ngang */
}

</style>

<?php
include '../header.php';
// Thông tin kết nối cơ sở dữ liệu
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

mysqli_set_charset($connect, 'utf8'); // Đặt kiểu dữ liệu utf8

// Lấy ID sản phẩm từ URL
$id_sanpham = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Truy vấn thông tin sản phẩm
$query = "SELECT * FROM sanpham WHERE id_sanpham = $id_sanpham";
$result = mysqli_query($connect, $query);
$sanpham = mysqli_fetch_assoc($result);

// Kiểm tra sản phẩm có tồn tại không
if ($sanpham) {
    // Lấy danh sách màu sắc của sản phẩm
    $query_mau = "SELECT mau.tenmau FROM mausanpham JOIN mau ON mausanpham.id_mau = mau.id_mau WHERE mausanpham.id_sanpham = $id_sanpham";
    $result_mau = mysqli_query($connect, $query_mau);
    $mau_sanpham = [];
    while ($row = mysqli_fetch_assoc($result_mau)) {
        $mau_sanpham[] = $row['tenmau'];
    }

    // Lấy danh sách kích thước của sản phẩm
    $query_kichthuoc = "SELECT kichthuoc.tenkichthuoc FROM kichthuocsanpham JOIN kichthuoc ON kichthuocsanpham.id_kichthuoc = kichthuoc.id_kichthuoc WHERE kichthuocsanpham.id_sanpham = $id_sanpham";
    $result_kichthuoc = mysqli_query($connect, $query_kichthuoc);
    $kichthuoc_sanpham = [];
    while ($row = mysqli_fetch_assoc($result_kichthuoc)) {
        $kichthuoc_sanpham[] = $row['tenkichthuoc'];
    }
    // Truy vấn để lấy số lượng còn lại cho sản phẩm
$query = "SELECT sanpham.soluongsanpham, 
SUM(donhang_details.soluong) AS total_quantity 
FROM sanpham
LEFT JOIN donhang_details ON sanpham.id_sanpham = donhang_details.id_sanpham
WHERE sanpham.id_sanpham = ?
GROUP BY sanpham.id_sanpham";

$stmt = $connect->prepare($query);
$stmt->bind_param("i", $id_sanpham);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
$row = $result->fetch_assoc();
$soluongcon = $row['soluongsanpham'] - $row['total_quantity'];
}

?>


<!-- Chi tiết sản phẩm  -->
<style>
/* Điều chỉnh chiều cao tối đa cho khung hình ảnh */
.col-lg-5.mb-30 {
    max-height: 500px; /* Chiều cao tối đa cho khung hình ảnh */
    overflow: hidden;
}

.col-lg-5 img {
    height: 100%;
    width: 100%;
    object-fit: cover; /* Đảm bảo ảnh lấp đầy khung mà không bị méo */
}

/* Điều chỉnh chiều cao tối đa cho khung chi tiết sản phẩm */
.col-lg-7.h-auto.mb-30 {
    max-height: 500px; /* Chiều cao tối đa cho khung chi tiết */
    overflow-y: auto; /* Tự động hiển thị thanh cuộn nếu nội dung vượt quá chiều cao khung */
}

/* Điều chỉnh khoảng cách và padding để thu ngắn nội dung */
.h-100.bg-light.p-30 {
    padding: 20px; /* Giảm padding để khung gọn hơn */
}
.carousel-control-prev-icon,
.carousel-control-next-icon {
    filter: invert(1); /* Chuyển màu từ trắng sang đen */
}

</style>

<div class="container-fluid pb-5">
    <div class="row px-xl-5">
        <div class="col-lg-5 mb-30">
            <?php
            $id_sanpham = $sanpham['id_sanpham'];

            // Lấy ảnh từ bảng chitietanhsanpham
            $query = "SELECT anh FROM chitietanhsanpham WHERE id_sanpham = ?";
            $stmt = $connect->prepare($query);
            $stmt->bind_param("i", $id_sanpham);
            $stmt->execute();
            $result = $stmt->get_result();

            // Kiểm tra xem sản phẩm có ảnh chi tiết không
            if ($result->num_rows > 0) {
                // Nếu có ảnh trong bảng chitietanhsanpham, hiển thị các ảnh này
                echo '<div id="product-carousel" class="carousel slide" data-ride="carousel">';
                echo '<div class="carousel-inner bg-light">';
                
                $first = true;
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="carousel-item ' . ($first ? 'active' : '') . '">';
                    echo '<img class="w-100 h-100" src="/webbanhang/admin/modules/quanlysanpham/img/' . $row['anh'] . '" alt="' . $sanpham['tensanpham'] . '">';
                    echo '</div>';
                    $first = false;
                }

                echo '</div>';
                echo '<a class="carousel-control-prev" href="#product-carousel" role="button" data-slide="prev">';
                echo '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
                echo '<span class="sr-only">Previous</span>';
                echo '</a>';
                echo '<a class="carousel-control-next" href="#product-carousel" role="button" data-slide="next">';
                echo '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
                echo '<span class="sr-only">Next</span>';
                echo '</a>';
                echo '</div>'; // Đóng carousel
            } else {
                // Nếu không có ảnh trong bảng chitietanhsanpham, hiển thị ảnh chính từ bảng sanpham
                echo '<img class="w-100 h-100" src="/webbanhang/admin/modules/quanlysanpham/img/' . $sanpham['anhsanpham'] . '" alt="' . $sanpham['tensanpham'] . '">';
            }
                    // Kiểm tra số lượng còn lại để hiện overlay "Hết hàng"
            if (isset($soluongcon) && $soluongcon < 1) {
                echo '<div id="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.8); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; z-index: 999;"></div>';
            }
            ?>
        </div>

        <div class="col-lg-7 h-auto mb-30">
<div class="h-100 bg-light p-30">
    <h3 style="color: #000;"><?php echo $sanpham['tensanpham']; ?></h3>  
    <!-- Hiển thị giá và giá khuyến mãi -->
    <div class="d-flex align-items-center mb-4">
        <h3 class="font-weight-semi-bold" id="total-price" style="color: #FF4500; margin-right: 15px;">
            <?php echo number_format($sanpham['giasanpham'], 0, ',', '.'); ?> VND
        </h3>       
        <!-- Nếu có giá khuyến mãi thì hiển thị -->
        <?php if (!empty($sanpham['giakhuyenmai'])): ?>
            <h4 class="text-muted" style="text-decoration: line-through; color: #000;">
                <?php echo number_format($sanpham['giakhuyenmai'], 0, ',', '.'); ?> VND
            </h4>
        <?php endif; ?>
    </div>
    <p class="mb-4" style="color: #000;"><?php echo $sanpham['motasanpham']; ?></p>
  <!-- Hiển thị số lượng còn lại -->
        <div class="d-flex align-items-center mb-2">
            <h5>Số lượng còn lại: <?php echo isset($soluongcon) ? $soluongcon : 0; ?></h5>
        </div> 
    <!-- Màu sắc sản phẩm -->
    <div class="d-flex mb-4">
        <strong class="text-dark mr-3" style="color: #000; ">Màu sắc:</strong>
        <form>
            <?php foreach ($mau_sanpham as $key => $mau) { ?>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" class="custom-control-input" id="color-<?php echo $key; ?>" name="color" value="<?php echo $mau; ?>" required>
                    <label class="custom-control-label" for="color-<?php echo $key; ?>" style="color: #000;"><?php echo $mau; ?></label>
                </div>
            <?php } ?>
        </form>
    </div>  
        <!-- kích thước sản phẩm -->
    <div class="d-flex mb-4">
                    <strong class="text-dark mr-3" style="color: #000;">Kích thước:</strong>
                    <form>
                        <?php foreach ($kichthuoc_sanpham as $key => $kichthuoc) { ?>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input" id="size-<?php echo $key; ?>" name="size" value="<?php echo $kichthuoc; ?>" required>
                                <label class="custom-control-label" for="size-<?php echo $key; ?>" style="color: #000;"><?php echo $kichthuoc; ?></label>
                            </div>
                        <?php } ?>
                    </form>
                </div>       
<!-- Nút Thêm vào giỏ hàng -->
<form action="giohang.php" method="POST">
    <div class="d-flex align-items-center mb-2 pt-2"> 
    <div class="input-group quantity mr-3" style="width: 130px;  height: 30px;"> 
    <div class="input-group-btn">
        <button class="btn" style="background-color:  green; color: white; border: none; height: 30px;" type="button" onclick="updateQuantity(-1)"> 
            <i class="fa fa-minus"></i>
        </button>
    </div>
    <input type="number" id="quantity" name="quantity" class="form-control bg-secondary text-center" value="1" min="1" max="99" style="height: 30px;">
    <div class="input-group-btn">
        <button class="btn" style="background-color:  green; color: white; border: none; height: 30px;" type="button" onclick="updateQuantity(1)"> 
            <i class="fa fa-plus"></i>
        </button>
    </div>
</div>

        <input type="hidden" name="id" value="<?php echo $sanpham['id_sanpham']; ?>">

        <!-- Thêm input cho màu sắc -->
        <input type="hidden" name="color" id="selectedColor" value="">
        <!-- Thêm input cho kích thước -->
        <input type="hidden" name="size" id="selectedSize" value="">

        <button type="submit" class="btn" style="background-color: green; color: white; border: none; height: 30px;" onclick="return validateForm()"> 
    <i class="fas fa-shopping-basket"></i> Thêm vào giỏ hàng
</button>
    </div>
</form>
<?php 
// Kiểm tra số lượng còn lại để hiện overlay "Hết hàng"
if (isset($soluongcon) && $soluongcon < 1) {
    echo '<div id="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.8); display: flex; align-items: center; justify-content: flex-start; padding-left: 50px; color: white; font-size: 2rem; z-index: 999;">Hết hàng</div>';
}

?>
<script>
function setSelectedValues() {
    // Lấy màu sắc được chọn
    const selectedColor = document.querySelector('input[name="color"]:checked');
    if (selectedColor) {
        document.getElementById('selectedColor').value = selectedColor.value;
    }

    // Lấy kích thước được chọn
    const selectedSize = document.querySelector('input[name="size"]:checked');
    if (selectedSize) {
        document.getElementById('selectedSize').value = selectedSize.value;
    }
}
function validateForm() {
    // Lấy giá trị được chọn
    const selectedColor = document.querySelector('input[name="color"]:checked');
    const selectedSize = document.querySelector('input[name="size"]:checked');

    // Kiểm tra từng trường hợp
    if (!selectedColor && !selectedSize) {
        alert('Vui lòng chọn màu sắc và kích thước !');
        return false; // Ngăn không cho form gửi đi
    } else if (!selectedColor) {
        alert('Vui lòng chọn màu sắc!');
        return false; // Ngăn không cho form gửi đi
    } else if (!selectedSize) {
        alert('Vui lòng chọn kích thước!');
        return false; // Ngăn không cho form gửi đi
    }

    // Gọi hàm setSelectedValues để lưu dữ liệu vào hidden input
    setSelectedValues();
    return true; // Cho phép form gửi đi
}
</script>

            </div>
        </div>
    </div>
</div>


<!-- Sản phẩm gợi ý -->
<div class="related-products mt-5">
<h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4" style="font-weight: 800; color: #000;">
    <span class="bg-secondary pr-3">Sản phẩm liên quan</span>
</h2>
    <div class="row px-xl-5">
        <?php
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
        echo '                 <h5 style="color: red;">' . number_format($row["giasanpham"], 0, ',', '.') . ' VND</h5>';
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
}        ?>
    </div>
</div>

<script>
    // Hàm cập nhật số lượng sản phẩm
    function updateQuantity(change) {
        var quantityInput = document.getElementById('quantity');
        var currentValue = parseInt(quantityInput.value);
        var newValue = currentValue + change;

        if (newValue >= 1 && newValue <= 99) {
            quantityInput.value = newValue;
        }
    }
</script>

<?php
} else {
    echo "<h3>Sản phẩm không tồn tại!</h3>";
}

// Đóng kết nối
mysqli_close($connect);
include '../footer.php';
?>
 