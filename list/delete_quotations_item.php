<?php
header("Content-Type: application/json");

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "shop_it";

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "เชื่อมต่อฐานข้อมูลล้มเหลว"]);
    exit;
}

// รับค่า id จาก JSON
$data = json_decode(file_get_contents("php://input"), true);
$id = isset($data["id"]) ? intval($data["id"]) : 0;

if ($id <= 0) {
    echo json_encode(["success" => false, "message" => "ไม่พบค่า ID หรือค่าไม่ถูกต้อง"]);
    exit;
}

// ลบเฉพาะใน quotation_items
$sql = "DELETE FROM quotation_items WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => $stmt->error]);
}

$stmt->close();
$conn->close();
