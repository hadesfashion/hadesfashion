<?php
include '../pages/header.php';
// Kết nối đến cơ sở dữ liệu
$servername = "localhost"; // Thay đổi nếu cần
$username = "root"; // Thay đổi thành tên người dùng của bạn
$password = ""; // Thay đổi thành mật khẩu của bạn
$database = "vidu"; // Thay đổi thành tên cơ sở dữ liệu của bạn

// Tạo kết nối
$connect = new mysqli($servername, $username, $password, $database);

// Kiểm tra kết nối
if ($connect->connect_error) {
    die("Kết nối thất bại: " . $connect->connect_error);
}

// Kiểm tra nếu có tham số 'id' trong URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Lấy ID từ URL và chuyển đổi sang kiểu số nguyên

    // Lấy thông tin chính sách theo ID
    $sql = "SELECT * FROM chinhsach WHERE id_chinhsach = $id";
    $result = $connect->query($sql);

    // Kiểm tra xem có dữ liệu không
    if ($result->num_rows > 0) {
        $policy = $result->fetch_assoc(); // Lấy thông tin chính sách
    } else {
        echo "Không tìm thấy chính sách.";
        exit; // Ngừng thực hiện mã nếu không tìm thấy
    }
} else {
    echo "Không có ID chính sách.";
    exit; // Ngừng thực hiện mã nếu không có ID
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo $id ? htmlspecialchars($policy['tenchinhsach']) : "Danh Sách Chính Sách"; ?></title>
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
    <style>
        .policy-content {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            color: #333;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .policy-content h1 {
            color: #2c3e50;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .policy-content p {
            font-size: 16px;
            line-height: 1.6;
            color: #000; /* Màu chữ đen */
        }

        .policy-content .text-secondary {
            color: #000; /* Màu chữ đen */
        }

        .policy-content .text-secondary a {
            text-decoration: none;
            color: #3498db; /* Màu xanh lam cho các liên kết */
            transition: color 0.3s ease;
        }

        .policy-content .text-secondary a:hover {
            color: #2980b9; /* Màu xanh đậm hơn khi hover */
        }
    </style>
</head>
<body>
    <?php if ($id && $policy): ?>
        <!-- Hiển thị nội dung chi tiết của chính sách -->
        <div class="policy-content">
            <h1><?php echo htmlspecialchars($policy['tenchinhsach']); ?></h1>
            <p><?php echo nl2br(htmlspecialchars($policy['noidung'])); ?></p>
        </div>
        <?php include '../pages/footer.php';?>
    <?php else: ?>
        <!-- Hiển thị danh sách chính sách -->
        <h1>Danh Sách Chính Sách</h1>
        <div class="col-md-4 mb-5">
            <h5 class="text-secondary text-uppercase mb-4">Chính sách</h5>
            <div class="d-flex flex-column justify-content-start">
                <?php
                // Lấy tất cả các chính sách từ cơ sở dữ liệu
                $sql = "SELECT id_chinhsach, tenchinhsach FROM chinhsach";
                $result = $connect->query($sql);

                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                    <a class="text-secondary mb-2" href="chinhsach.php?id=<?php echo $row['id_chinhsach']; ?>">
                        <i class="fa fa-angle-right mr-2"></i><?php echo htmlspecialchars($row['tenchinhsach']); ?>
                    </a>
                <?php
                    endwhile;
                else:
                ?>
                    <p class="text-secondary">Không có chính sách nào.</p>
                <?php endif; ?>
            </div>
        </div>
        <a href="them.php">Thêm Chính Sách</a> <!-- Liên kết đến trang thêm chính sách -->
    <?php endif; ?>

</body>
</html>

