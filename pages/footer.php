<?php

// Kết nối đến cơ sở dữ liệu
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "vidu";

// Tạo kết nối
$connect = new mysqli($servername, $username, $password, $database);

// Kiểm tra kết nối
if ($connect->connect_error) {
    die("Kết nối thất bại: " . $connect->connect_error);
}
  // Truy vấn danh sách tên cửa hàng
  $sql = "SELECT tencuahang FROM hethongcuahang";
  $result = $connect->query($sql);

// Lấy tất cả các thông tin liên hệ từ cơ sở dữ liệu
$sql_lienhe = "SELECT * FROM lienhe";
$result_lienhe = $connect->query($sql_lienhe);

// Lấy tất cả các chính sách từ cơ sở dữ liệu
$sql_chinhsach = "SELECT id_chinhsach, tenchinhsach FROM chinhsach";
$result_chinhsach = $connect->query($sql_chinhsach);
?>
<style>
.footer-bg .col-lg-3 {
    margin-bottom: 0 !important; /* Loại bỏ khoảng cách lề dưới cùng */
    padding-bottom: 0 !important; /* Loại bỏ khoảng cách đệm dưới cùng */
}
.footer-bg .mb-5 {
    margin-bottom: 0 !important; /* Đảm bảo không có khoảng cách lề phía dưới */
}

</style>
<div class="container-fluid bg-dark footer-bg mt-2 pt-2">
    <div class="row px-xl-2 pt-2">
        <div class="d-flex justify-content-between flex-wrap">
            <div class="col-lg-3 col-md-6 mb-5 pr-3 pr-xl-5 d-flex flex-column">
                <h5 class="text-secondary text-uppercase mb-4" style="font-size: 16px;">Thông tin liên hệ</h5>

                <?php if ($result_lienhe->num_rows > 0): ?>
                    <?php while ($row = $result_lienhe->fetch_assoc()): ?>
                        <p class="mb-2">
                            <?php if (strpos($row['tenlienhe'], '@') !== false): ?>
                                <i class="fa fa-envelope text-primary mr-3"></i>
                            <?php elseif (preg_match('/\d/', $row['tenlienhe'])): ?>
                                <i class="fa fa-phone-alt text-primary mr-3"></i>
                            <?php else: ?>
                                <i class="fa fa-map-marker-alt text-primary mr-3"></i>
                            <?php endif; ?>
                            <?php echo htmlspecialchars($row['tenlienhe']); ?>
                        </p>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-secondary">Không có thông tin liên hệ nào.</p>
                <?php endif; ?>
            </div>
            <div class="col-lg-3 col-md-6 mb-5 d-flex flex-column">
            <h5 class="text-secondary text-uppercase mb-4" style="font-size: 16px;">Chính sách</h5>
            <div class="d-flex flex-column justify-content-start">
                    <?php if ($result_chinhsach->num_rows > 0): ?>
                        <?php while ($row = $result_chinhsach->fetch_assoc()): ?>
                            <a class="text-secondary mb-2" href="/webbanhang/pages/chinhsach.php?id=<?php echo $row['id_chinhsach']; ?>">
                                <?php echo htmlspecialchars($row['tenchinhsach']); ?>
                            </a>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-secondary">Không có chính sách nào.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-5 d-flex flex-column">
    <h5 class="text-secondary text-uppercase mb-4"style="font-size: 16px;">Hệ thống cửa hàng</h5>
    <div class="d-flex flex-column justify-content-start">
  <?php
    if ($result->num_rows > 0) {
            // Hiển thị tên từng cửa hàng
            while ($row = $result->fetch_assoc()) {
                echo "<p class='text-secondary mb-2' style='font-size: 14px;'>{$row['tencuahang']}</p>";
            }
        } else {
            echo "<p class='text-secondary mb-2' style='font-size: 14px;'>Chưa có cửa hàng nào</p>";
        }

        $connect->close();
        ?>
    </div>
</div>            <div class="col-lg-3 col-md-6 mb-5 d-flex flex-column">
                <h5 class="text-secondary text-uppercase mb-4"style="font-size: 16px;">Đăng ký</h5>
                <p>Bạn có thể đăng ký tại đây!</p>
                <form action="">
                    <div class="input-group">
                        <div class="input-group-append">
                            <button class="btn btn-primary">Đăng ký</button>
                        </div>
                    </div>
                </form>
                <h6 class="text-secondary text-uppercase mt-4 mb-3">Liên hệ</h6>
                <div class="d-flex">
                    <a class="btn btn-primary btn-square mr-2" href="#"><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-primary btn-square mr-2" href="#"><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-primary btn-square mr-2" href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a class="btn btn-primary btn-square" href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="row border-top mx-xl-5 py-4" style="border-color: rgba(256, 256, 256, .1) !important;">
        <div class="col-md-6 px-xl-0">
        </div>
        <div class="col-md-6 px-xl-0 text-center text-md-right">
        </div>
    </div>
</div>
<!-- Footer End -->

<!-- Back to Top -->
<a href="#" class="btn btn-primary back-to-top"><i class="fa fa-angle-double-up"></i></a>

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
<script src="/webbanhang/lib/easing/easing.min.js"></script>
<script src="/webbanhang/lib/owlcarousel/owl.carousel.min.js"></script>

<!-- Contact Javascript File -->
<script src="/webbanhang/mail/jqBootstrapValidation.min.js"></script>
<script src="/webbanhang/mail/contact.js"></script>
<!-- Thêm jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Thêm Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<!-- Template Javascript -->
<script src="/webbanhang/js/main.js"></script>
</body>

</html>
