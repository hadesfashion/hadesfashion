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


?>
