<?php
session_start();
session_unset(); // Xóa tất cả các biến session
session_destroy(); // Hủy session
header("Location: http://localhost/webbanhang/"); // Chuyển hướng về trang chủ
exit();
?>
