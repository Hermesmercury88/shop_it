<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "shop_it";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("DELETE FROM delivery_items WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>alert('ลบสำเร็จ'); window.location='all_deliveries1.php';</script>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
