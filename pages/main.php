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
    font-size: 1.1rem;
    z-index: 1;
    text-transform: uppercase;
    font-weight: bold;
    border-radius: 5px; /* Bo góc */
}

.product-item.out-of-stock img {
    filter: grayscale(100%); /* Làm đen trắng ảnh */
}
</style>

<!--css tin tức -->
<style>
.news-card {
    max-height: 350px; /* Chiều cao khung */
}

.small-text {
    font-size: 0.8rem; /* Kích thước chữ trong khung nhỏ hơn */
}

.view-more {
    color: rgb(12, 174, 12); /* Màu xanh lá cho chữ "Xem thêm" */
    cursor: pointer; /* Thay đổi con trỏ khi di chuột qua chữ "Xem thêm" */
    font-size: 0.8rem; /* Kích thước chữ bằng với mô tả */
    text-decoration: underline; /* Gạch chân chữ "Xem thêm" để tạo sự chú ý */
}

</style>

<!--css phần trăm giảm giá-->
<style>
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


<!-- Sản phẩm bán chạy-->
<div class="container-fluid pt-5 pb-3">
    <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4" style="font-weight: 800; color: #000;">
        <span class="bg-secondary pr-3">Sản phẩm </span>
    </h2>
    <div class="row px-xl-5">
    <?php
// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "vidu"; 

$connect = new mysqli($servername, $username, $password, $dbname);
if ($connect->connect_error) {
    die("Kết nối thất bại: " . $connect->connect_error);
}

$sql = "SELECT * FROM sanpham";
$result = $connect->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $giasanpham = $row["giasanpham"];
        $giakhuyenmai = $row["giakhuyenmai"];
        $phantramgiam = 0;
        if ($giakhuyenmai > 0) {
            $phantramgiam = round((($giakhuyenmai - $giasanpham) / $giakhuyenmai) * 100);
        }

        // Kiểm tra nếu sản phẩm hết hàng
        $hetHangClass = ($row["soluongsanpham"] == 0) ? 'out-of-stock' : '';

        echo '<div class="col-lg-3 col-md-4 col-sm-6 pb-1">';
        echo '    <div class="product-item bg-light mb-4 ' . $hetHangClass . '">';
        echo '        <div class="product-img position-relative overflow-hidden">';

        if ($phantramgiam > 0) {
            echo '            <div class="discount-circle position-absolute top-0 start-0 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; background-color: red; color: white; border-radius: 50%;">' . $phantramgiam . '%</div>';
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
        echo '<h5 style="color: red;">' . number_format($giasanpham) . ' VND</h5>';
        if ($giakhuyenmai > 0) {
            echo '                <h6 class="text-muted ml-2"><del>' . number_format($giakhuyenmai) . ' VND</del></h6>';
        }
        echo '            </div>';
        echo '        </div>';
        echo '    </div>';
        echo '</div>';
    }
} else {
    echo "Không có sản phẩm nào.";
}

$connect->close();
?>
    </div>
</div>

<!-- tin tức -->
<div class="container-fluid pt-5 pb-3">
<h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4" style="font-weight: 800; color: #000;">
    <span class="bg-secondary pr-3">Tin tức</span>
</h2>
    <div class="row px-xl-5">

    <?php
    // Kết nối cơ sở dữ liệu
    $connect = new mysqli('localhost', 'root', '', 'vidu'); // Cập nhật tên database, username và password nếu cần

    // Kiểm tra kết nối
    if ($connect->connect_error) {
        die("Kết nối thất bại: " . $connect->connect_error);
    }

    // Câu lệnh SQL để lấy tin tức
    $sql = "SELECT * FROM tintuc";
    $result = $connect->query($sql);

    if ($result->num_rows > 0) {
        // Hiển thị từng tin tức
        while($row = $result->fetch_assoc()) {
            echo '<div class="col-lg-3 col-md-4 mb-4">'; // Thay đổi thành col-lg-3 để sắp xếp 4 tin tức
            echo '    <div class="card h-100 news-card">'; // Thêm class news-card để định dạng
            
            // Phần nửa trên hiển thị ảnh
            echo '        <div class="card-img-top">';
            echo '            <img style="width: 100%; height: 150px; object-fit: cover;" src="/webbanhang/admin/modules/quanlytintuc/img/' . $row['hinhanh'] . '" alt="Ảnh tin tức">';
            echo '        </div>';
            
            // Phần nửa dưới hiển thị nội dung
            echo '        <div class="card-body d-flex flex-column justify-content-between">';
            echo '            <h5 class="card-title small-text">' . $row['tieude'] . '</h5>'; // Thay đổi class để giảm kích thước chữ
            echo '            <p class="card-text small-text">' . $row['mota'] . ' <a href="/webbanhang/pages/main/chitiettintuc.php?id=' . $row['id'] . '" class="view-more" style="color: black; font-weight: bold;" >Xem thêm</a></p>';
            echo '        </div>';
            
            echo '    </div>';
            echo '</div>';
        }
    } else {
        echo "<p>Không có tin tức nào được tìm thấy.</p>";
    }

    // Đóng kết nối
    $connect->close();
    ?>

    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
