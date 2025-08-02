<?php session_start(); ?>
<?php include './config.php'; ?>
<?php include '../header.php'; ?>

<!-- css sản phẩm-->
<style>
    .product-image {
        width: 100px;
        height: auto;
        object-fit: cover;
    }
    .product-item {
        margin: 10px; /* Tạo khoảng cách giữa các sản phẩm */
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
    .pagination a {
        color: black; /* Màu chữ cho các số trang */
    }
    .pagination .active a {
        font-weight: bold; /* Đánh dấu số trang hiện tại */
    }
    .d-flex.align-items-center.justify-content-between.mb-9 {
    margin-top: 30px; /* Khoảng cách trên */
    margin-bottom: 30px; /* Khoảng cách dưới */
}
.btn-group {
    margin-right: 10px; /* Khoảng cách giữa các nút */
}

</style>
<!-- css hết hàng -->
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
<!-- css phân trang-->
<style>
.pagination-container {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

.pagination {
    display: flex;
    list-style-type: none;
    padding: 0;
}

.pagination a {
    color: black;
    padding: 8px 16px;
    text-decoration: none;
    border: 1px solid #ddd;
    margin: 0 4px;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.pagination a:hover {
    background-color: #f0f0f0;
}

.pagination .active a {
    font-weight: bold;
    background-color: #ddd;
}

.pagination .arrow {
    font-weight: bold;
    padding: 8px 12px;
}
</style>
<!-- css nút danh mục-->
<style>
.category-btn {
    min-width: 100px; /* Đặt độ rộng tối thiểu để tất cả các nút bằng nhau */
    margin: 0 5px; /* Khoảng cách ngang giữa các nút */
    border: 1px solid #ddd; /* Viền cho từng nút */
    padding: 8px 15px; /* Đệm để nút trông cân đối */
    border-radius: 5px; /* Bo góc cho nút */
    transition: background-color 0.3s, box-shadow 0.3s; /* Hiệu ứng hover */
    text-align: center; /* Căn giữa chữ trong nút */
}

.category-btn:hover {
    background-color:#45d10a; /* Màu nền khi hover */
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); /* Hiệu ứng bóng mờ khi hover */
}

</style>

<!-- truy vấn danh mục -->
<div class="d-flex align-items-center justify-content-between mb-9">
    <div>
        <h3 style="padding-left: 30px;">Danh mục sản phẩm</h3>
    </div>
    <div class="ml-2">
        <div class="btn-group">
            <a href="/webbanhang/pages/main/sanpham.php">
                <button type="button" class="btn btn-sm btn-light category-btn">Tất cả</button>
            </a>
            <?php
            $connect = mysqli_connect("localhost", "root", "", "vidu");
            $query_danhmuc = mysqli_query($connect, "SELECT * FROM danhmuc");

            if ($query_danhmuc) {
                while ($row_danhmuc = mysqli_fetch_array($query_danhmuc)) {
                    echo '<a href="?id_danhmuc=' . $row_danhmuc['id_danhmuc'] . '">';
                    echo '<button type="button" class="btn btn-sm btn-light category-btn">' . $row_danhmuc['tendanhmuc'] . '</button>';
                    echo '</a>';
                }
            }
            ?>
        </div>
    </div>
</div>
<!--hiên thị sản phẩm theo danh mục và phân trang -->
<div class="container-fluid p-0">
    <div class="row mx-0">
        <div class="col-lg-12 col-md-12">
            <div class="row pb-3">
                <?php
                $items_per_page = 8;
                $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
                $offset = ($current_page - 1) * $items_per_page;

                $connect = mysqli_connect("localhost", "root", "", "vidu");
                $categoryId = isset($_GET['id_danhmuc']) ? $_GET['id_danhmuc'] : '';
                
                $sql_count = $categoryId ? "SELECT COUNT(*) FROM sanpham WHERE id_danhmuc='$categoryId'" : "SELECT COUNT(*) FROM sanpham";
                $total_items = $connect->query($sql_count)->fetch_row()[0];
                $total_pages = ceil($total_items / $items_per_page);

          // Truy vấn sản phẩm theo danh mục 
          $sql = $categoryId
              ? "SELECT * FROM sanpham WHERE id_danhmuc='$categoryId' LIMIT $items_per_page OFFSET $offset"
              : "SELECT * FROM sanpham LIMIT $items_per_page OFFSET $offset";
                          $result = $connect->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $giasanpham = $row["giasanpham"];
                        $giakhuyenmai = $row["giakhuyenmai"];
                        $phantramgiam = ($giakhuyenmai > 0 && $giasanpham < $giakhuyenmai)
                            ? round((($giakhuyenmai - $giasanpham) / $giakhuyenmai) * 100)
                            : 0;

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
                        echo '            <a class="btn" href="/webbanhang/pages/main/chitietsp.php?id=' . $row["id_sanpham"] . '" style="color: white; background-color: green; border: none; padding: 2px 10px; border-radius: 15px;">Mua ngay</a>';
                        echo '            <div class="d-flex align-items-center justify-content-center mt-2">';
                        echo '                <h5 style="color: red;">' . number_format($giasanpham) . ' VND</h5>';
                        if ($giakhuyenmai > 0) {
                            echo '                <h6 class="text-muted ml-2"><del>' . number_format($giakhuyenmai) . ' VND</del></h6>';
                        }
                        echo '            </div>';
                        echo '        </div>';
                        echo '    </div>';
                        echo '</div>';
                    }
                } else {
                    echo "<p class='text-center'>Không có sản phẩm nào.</p>";
                }

                $connect->close();
                ?>
            </div>
        </div>
    </div>
</div>
<!-- phân trang -->
<div class="pagination text-center">
    <?php
    for ($page = 1; $page <= $total_pages; $page++) {
        $is_active = $page == $current_page ? 'class="active"' : '';
        echo "<a href='?page=$page&id_danhmuc=$categoryId' $is_active>$page</a>";
    }
    ?>
</div>
<?php include '../footer.php'; ?>
