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

<!-- Tìm kiếm -->
<form id="check-timkiem" action="" method="GET" style="display: inline;">
    <button type="button" id="search-btn" style="background: none; border: none; cursor: pointer;">
        <i class="fa fa-search fa" style="color: black; padding-left: 55px;"> Tìm kiếm</i>
    </button>
</form>

<!-- Mờ phần còn lại của màn hình -->
<div id="overlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.8); z-index: 999;"></div>

<!-- Cửa sổ tìm kiếm -->
<div id="search-window" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 50vh; background: #fff; color: #000; z-index: 1000; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.3); transform: translateY(-100%); transition: transform 0.4s ease;">
    <button id="close-btn" style="background-color: red; border: none; color: white; font-size: 20px; position: absolute; top: 80px; right: 30px; cursor: pointer; padding: 2px 7px;">✕</button>
    <h2 style="font-size: 18px; font-weight: bold; margin-bottom: 15px;">Tìm kiếm sản phẩm</h2>
    <form id="search-form" action="" method="GET" style="display: flex; width: 100%; justify-content: center; align-items: center; margin-top: 10px;">
        <input type="text" name="search" placeholder="Tìm sản phẩm..." style="width: 60%; padding: 10px; font-size: 16px; border: 1px solid #ccc; border-radius: 5px; background-color: #f0f0f0;"/>
        <button type="submit" style="padding: 10px; background: none; border: none; cursor: pointer; border-radius: 5px;">
            <i class="fa fa-search" style="color: black;"></i>
        </button>
    </form>
</div>

<!-- Thêm CSS cho hiệu ứng fade -->
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

<script>
// Mở cửa sổ tìm kiếm khi nhấn vào biểu tượng kính lúp
document.getElementById('search-btn').addEventListener('click', function() {
    const searchWindow = document.getElementById('search-window');
    const overlay = document.getElementById('overlay');
    searchWindow.style.transform = 'translateX(0)'; // Hiển thị cửa sổ tìm kiếm trượt từ phải
    searchWindow.style.display = 'flex';
    overlay.style.display = 'block'; // Hiển thị lớp phủ mờ
});

// Đóng cửa sổ tìm kiếm khi nhấn vào nút Đóng
document.getElementById('close-btn').addEventListener('click', function() {
    const searchWindow = document.getElementById('search-window');
    const overlay = document.getElementById('overlay');
    searchWindow.style.transform = 'translateX(100%)'; // Trượt cửa sổ ra khỏi màn hình
    setTimeout(() => {
        searchWindow.style.display = 'none';
        overlay.style.display = 'none'; // Ẩn lớp phủ mờ
    }, 400);
});
</script>

<?php
// Kết nối tới cơ sở dữ liệu
include 'config.php'; // Đảm bảo rằng bạn đã có file config.php để kết nối
include '../header.php';
// Lấy từ khóa tìm kiếm từ URL
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Truy vấn tìm kiếm sản phẩm
$query = "SELECT * FROM sanpham WHERE tensanpham LIKE '%$search%'";
$result = mysqli_query($connect, $query);
?>

<div class="container-fluid pb-5" style="padding-top: 50px;"> <!-- Thêm padding-top -->
    <div style="display: flex; justify-content: flex-start;">
        <h2 style="font-size: 1.5rem; font-weight: bold; color: black; margin-bottom: 20px;"> <!-- Thêm margin-bottom -->
             <strong><?php echo htmlspecialchars($search); ?></strong>
        </h2>
    </div>
    <div class="row px-xl-5">
        <?php
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
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

                echo '            <img class="img-fluid w-100" src="/webbanhang/admin/modules/quanlysanpham/img/' . htmlspecialchars($row["anhsanpham"]) . '" alt="' . htmlspecialchars($row["tensanpham"]) . '">'; 
                echo '            <div class="product-action">';
                echo '                <a class="btn btn-outline-dark btn-square" href="giohang.php?id=' . $row["id_sanpham"] . '"><i class="fa fa-shopping-cart"></i></a>';
                echo '<a class="btn btn-outline-dark btn-square" href="/webbanhang/pages/main/chitietsp.php?id=' . $row["id_sanpham"] . '"><i class="far fa-eye"></i></a>';
                echo '            </div>';
                        // Hiển thị lớp phủ nếu sản phẩm hết hàng
        if ($row["soluongsanpham"] == 0) {
            echo '<div class="overlay position-absolute top-0 left-0 right-0 bottom-0 d-flex align-items-center justify-content-center">';
            echo '    <span>Hết hàng</span>';
            echo '</div>';
        }


                echo '        </div>';
                echo '        <div class="text-center py-4">';
                echo '            <h5 class="h6 text-decoration-none text-truncate">' . htmlspecialchars($row["tensanpham"]) . '</h5>';
                echo '<a class="btn" href="/webbanhang/pages/main/chitietsp.php?id=' . $row["id_sanpham"] . '" style="color: white; background-color: green; border: none; padding: 2px 10px; border-radius: 15px; display: inline-block; text-align: center; text-decoration: none; margin-top: 5px;"> <i class="fas fa-shopping-basket"></i>Mua ngay</a>';

                echo '            <div class="d-flex align-items-center justify-content-center mt-2">';
                echo '                <h5 style="color: red;">' . number_format($row["giasanpham"], 0, ',', '.') . ' VND</h5>';
                if ($row["giakhuyenmai"] > 0) {
                    echo '                <h6 class="text-muted ml-2"><del>' . number_format($row["giakhuyenmai"], 0, ',', '.') . ' VND</del></h6>';
                }
                echo '            </div>';
                echo '            <div class="d-flex align-items-center justify-content-center mb-1">';
// Hiển thị số sao
for ($i = 0; $i < 5; $i++) {
    if ($i < 4) {  // Hiển thị 4 sao đã đầy
        echo ' <i class="fa fa-star text-warning"></i>';
    } else {
        echo ' <i class="fa fa-star-half-alt text-warning"></i>';
    }
}
                echo '            </div>';
                echo '        </div>';
                echo '    </div>';
                echo '</div>';
            }
        } else {
            echo '<p style="color: black;">Không có sản phẩm nào phù hợp với tìm kiếm của bạn.</p>';
        }
        ?>
    </div>
</div>
<?php include '../footer.php'; ?>
