<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "shop_it";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "เชื่อมต่อฐานข้อมูลล้มเหลว"]));
}

$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data['id']);

// ลบเฉพาะใน delivery_items
$sql = "DELETE FROM delivery_items WHERE id=$id";

if ($conn->query($sql)) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => $conn->error]);
}
$conn->close();
