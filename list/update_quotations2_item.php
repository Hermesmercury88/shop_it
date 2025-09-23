<?php
header("Content-Type: application/json");

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "shop_it2";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "เชื่อมต่อฐานข้อมูลล้มเหลว"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$id   = $data["id"] ?? null;
$vals = $data["data"] ?? [];

if (!$id || count($vals) < 8) {  
    // 8 ช่องเพราะมี quotation_no, subject, recipient_name, item_name, qty, price, unit, total
    echo json_encode(["success" => false, "message" => "ข้อมูลไม่ครบ"]);
    exit;
}

// รับค่าจาก frontend
$quotation_no   = $vals[0];
$subject        = $vals[1];
$recipient_name = $vals[2];
$item_name      = $vals[3];
$qty            = $vals[4];
$price          = $vals[5];
$unit           = $vals[6];
$total          = $vals[7];

// ✅ update quotation_items
$sql_item = "UPDATE quotation_items 
             SET item_name=?, qty=?, price=?, unit=?, total=? 
             WHERE id=?";
$stmt_item = $conn->prepare($sql_item);
$stmt_item->bind_param("sidssi", $item_name, $qty, $price, $unit, $total, $id);

// ✅ update quotations (เชื่อมโยงด้วยการ subquery หา quotation_id จาก item id)
$sql_main = "UPDATE quotations 
             SET quotation_no=?, subject=?, recipient_name=? 
             WHERE id=(SELECT quotation_id FROM quotation_items WHERE id=?)";
$stmt_main = $conn->prepare($sql_main);
$stmt_main->bind_param("sssi", $quotation_no, $subject, $recipient_name, $id);

$success = $stmt_item->execute() && $stmt_main->execute();

if ($success) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "อัปเดตไม่สำเร็จ"]);
}

$stmt_item->close();
$stmt_main->close();
$conn->close();
