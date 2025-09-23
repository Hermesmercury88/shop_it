<?php
header("Content-Type: application/json");

// เชื่อมต่อฐานข้อมูล
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "shop_it";
$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "เชื่อมต่อฐานข้อมูลล้มเหลว"]));
}

// รับข้อมูลจาก fetch
$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? '';

if (!$id) {
    echo json_encode(["success" => false, "message" => "ไม่พบ ID"]);
    exit;
}

$sql = "DELETE FROM quotation_items WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "ลบไม่สำเร็จ"]);
}

$stmt->close();
$conn->close();
?>
