<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "receipt_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

$customer_name = $_POST['customer_name'];
$customer_address = $_POST['customer_address'];
$grand_total = $_POST['grand_total'];

$sql = "INSERT INTO receipts (customer_name, customer_address, grand_total) 
        VALUES ('$customer_name', '$customer_address', '$grand_total')";

if ($conn->query($sql) === TRUE) {
    echo "บันทึกข้อมูลเรียบร้อยแล้ว <a href='index.html'>กลับ</a>";
} else {
    echo "เกิดข้อผิดพลาด: " . $conn->error;
}

$conn->close();
?>
