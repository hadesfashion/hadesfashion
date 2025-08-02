<?php include '../header.php'; ?>
<?php
// Kết nối đến cơ sở dữ liệu
$connect = mysqli_connect("localhost", "root", "", "vidu"); // Thay đổi thông tin kết nối của bạn

// Kiểm tra xem có tham số 'quanly' và 'id' trong URL không
if (isset($_GET['quanly']) && $_GET['quanly'] == 'danhmuc' && isset($_GET['id'])) {
    $id_danhmuc = intval($_GET['id']); // Chuyển đổi sang số nguyên để bảo mật

    // Truy vấn để lấy tên danh mục
    $query_danhmuc = mysqli_query($connect, "SELECT tendanhmuc FROM danhmuc WHERE id_danhmuc='$id_danhmuc'");
    $row_danhmuc = mysqli_fetch_assoc($query_danhmuc);

    // Hiển thị tên danh mục
    if ($row_danhmuc) {
        echo '<h1 style="color: black; font-weight: bold; text-transform: uppercase; text-align: center;">' . htmlspecialchars($row_danhmuc['tendanhmuc']) . '</h1>';
    } else {
        echo '<h1>Danh mục không tồn tại.</h1>';
    }

    // Phân trang
    $limit = 10; // Số sản phẩm hiển thị trên mỗi trang
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Lấy số trang hiện tại từ URL, mặc định là 1
    $offset = ($page - 1) * $limit; // Tính toán offset

    // Truy vấn để lấy tổng số sản phẩm trong danh mục
    $total_query = mysqli_query($connect, "SELECT COUNT(*) AS total FROM sanpham WHERE id_danhmuc='$id_danhmuc'");
    $total_row = mysqli_fetch_assoc($total_query);
    $total_products = $total_row['total']; // Tổng số sản phẩm
    $total_pages = ceil($total_products / $limit); // Tính tổng số trang

    // Truy vấn để lấy sản phẩm theo danh mục với phân trang
    $query_sanpham = mysqli_query($connect, "SELECT * FROM sanpham WHERE id_danhmuc='$id_danhmuc' LIMIT $limit OFFSET $offset");

    // Kiểm tra và hiển thị sản phẩm
    if (mysqli_num_rows($query_sanpham) > 0) {
        echo '<div class="product-form">'; // Thêm div để chứa danh sách sản phẩm
        
        while ($row = mysqli_fetch_assoc($query_sanpham)) {
            // Tính toán phần trăm giảm giá
            $giasanpham = $row["giasanpham"];
            $giakhuyenmai = $row["giakhuyenmai"];
            $phantramgiam = 0;
            if ($giakhuyenmai > 0) {
                $phantramgiam = round((($giakhuyenmai - $giasanpham) / $giakhuyenmai) * 100);
            }

            echo '<div class="product-item">';
            echo '    <div class="product-info">';
            echo '        <div class="product-image">';
            echo '            <img class="img-fluid w-100" src="/webbanhang/admin/modules/quanlysanpham/img/' . htmlspecialchars($row["anhsanpham"]) . '" alt="">';
            echo '        </div>';
            echo '        <div class="product-details">';
            echo '            <h5>' . htmlspecialchars($row["tensanpham"]) . '</h5>';
            echo '            <div class="ratings">';
            for ($i = 0; $i < 5; $i++) {
                echo '                <small class="fa fa-star text-primary"></small>';
            }
            echo '                <small>(' . rand(1000, 3000) . ' lượt bán)</small>'; // Số lượt bán ngẫu nhiên
            echo '            </div>';
            echo '            <div class="discount-percentage">Giảm ' . $phantramgiam . '%</div>'; // Hiển thị phần trăm giảm giá
            echo '        </div>';
            echo '    </div>';
            echo '    <div class="product-price">';
            echo '        <div class="price">';
            echo '            <span class="original-price">' . number_format($giakhuyenmai) . ' VND</span>';
            echo '            <span class="discounted-price">' . number_format($giasanpham) . ' VND</span>';
            echo '        </div>';
            echo '<a class="btn" href="/webbanhang/pages/main/chitietsp.php?id=' . htmlspecialchars($row["id_sanpham"]) . '" style="color: white; background-color: green; border: none; padding: 2px 10px; border-radius: 15px; display: inline-block; text-align: center; text-decoration: none; margin-top: 10px;"> <i class="fas fa-shopping-basket"></i>Mua ngay</a>';
            echo '    </div>';
            echo '</div>';
        }
        echo '</div>'; // Đóng div chứa danh sách sản phẩm
    } else {
        echo "Không có sản phẩm nào.";
    }

    // Hiển thị phân trang
    echo '<div class="pagination">';
    for ($i = 1; $i <= $total_pages; $i++) {
        echo '<a href="?quanly=danhmuc&id=' . $id_danhmuc . '&page=' . $i . '" class="page-link' . ($i == $page ? ' active' : '') . '">' . $i . '</a> ';
    }
    echo '</div>'; // Đóng div phân trang

} else {
    // Nếu không có tham số danh mục, bạn có thể hiển thị nội dung mặc định hoặc thông báo
    echo '<h1>Vui lòng chọn một danh mục.</h1>';
}

// Đóng kết nối
mysqli_close($connect);
?>
<?php include '../footer.php'; ?>

<style>
.product-form {
    width: 100%; /* Giảm chiều dài khung lớn */
    margin: 20px auto;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 10px;
    background-color: #f9f9f9;
    display: flex; /* Cho phép các sản phẩm nằm trên cùng một hàng */
    flex-wrap: wrap; /* Để cho phép các sản phẩm xuống hàng nếu không đủ không gian */
}

.product-item {
    display: flex;
    width: calc(50% - 10px); /* Chiều dài khung sản phẩm bằng nửa chiều dài khung lớn với khoảng cách nhỏ */
    height: 4cm; /* Chiều cao khung sản phẩm */
    margin: 5px; /* Khoảng cách giữa các khung */
    background-color: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
}

.product-info {
    flex: 2; /* Chiếm 2/3 không gian */
    display: flex;
    padding: 10px;
}

.product-image {
    flex: 0.5; /* Chiếm không gian cho ảnh */
    overflow: hidden;
    display: flex;
    justify-content: center; /* Căn giữa ảnh */
    align-items: center; /* Căn giữa ảnh */
    padding: 5px; /* Thêm padding để tạo khoảng trống */
}

.product-image img {
    max-height: 200%; /* Giới hạn chiều cao của ảnh */
    max-width: 200%; /* Giới hạn chiều rộng của ảnh */
    object-fit: contain; /* Để giữ nguyên tỷ lệ mà không bị cắt */
}

.product-details {
    flex: 2.5; /* Chiếm không gian còn lại cho thông tin */
    padding: 10px;
    display: flex;
    flex-direction: column; /* Để các phần tử nằm dọc */
    justify-content: space-between; /* Đưa các phần lên và xuống */
}

.product-details h5 {
    font-size: 1.2rem;
    margin: 0;
}

.ratings {
    display: flex;
    align-items: center;
    margin-top: 5px;
}

.ratings .fa-star {
    color: #ffcc00;
}

.ratings small {
    margin-left: 10px;
    font-size: 0.9rem;
    color: #555;
}

.discount-percentage {
    font-weight: bold;
    color: red;
    margin-top: 5px;
}

.product-price {
    flex: 0.5; /* Chiếm 1/3 không gian */
    padding: 10px;
    background-color: #f3f3f3;
    display: flex;
    flex-direction: column;
    justify-content: space-between; /* Đưa các phần lên và xuống */
    border-left: 1px solid #ddd; /* Dấu ngăn giữa hai phần */
}

.price {
    margin-top: 10px;
}

.original-price {
    text-decoration: line-through;
    color: red; /* Màu sắc cho giá gốc */
}

.discounted-price {
    font-size: 1.2rem;
    font-weight: bold;
    color: green; /* Màu sắc cho giá giảm */
}

/* Phân trang */
.pagination {
    text-align: center; /* Căn giữa phân trang */
    margin-top: 20px; /* Khoảng cách với phần sản phẩm */
}

.page-link {
    margin: 0 5px;
    padding: 5px 10px;
    border: 1px solid #007bff; /* Màu sắc cho viền nút */
    border-radius: 5px; /* Đường viền tròn */
    color: #007bff; /* Màu sắc cho chữ */
    text-decoration: none; /* Bỏ gạch chân */
}

.page-link:hover {
    background-color: #007bff; /* Màu nền khi hover */
    color: white; /* Màu chữ khi hover */
}

.page-link.active {
    background-color: #007bff; /* Màu nền cho nút đang hoạt động */
    color: white; /* Màu chữ cho nút đang hoạt động */
}
</style>
