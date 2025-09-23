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
$id   = $data['id'] ?? '';
$vals = $data['data'] ?? [];

if (!$id || count($vals) < 8) {
    echo json_encode(["success" => false, "message" => "ข้อมูลไม่ครบ"]);
    exit;
}

/*
  โครงสร้าง $vals จากตาราง quotations
  [0] = เลขที่ใบเสนอราคา (quotation_no)
  [1] = เรื่อง (subject)
  [2] = เรียน (recipient_name)
  [3] = รายการสินค้า (item_name)
  [4] = จำนวน (qty)
  [5] = ราคา (price)
  [6] = หน่วย (unit)
  [7] = ราคารวม (total)
*/

// ✅ update 2 ตาราง (quotations + quotation_items)
$conn->begin_transaction();

try {
    // อัพเดทตาราง quotations
    $sql1 = "UPDATE quotations q
             JOIN quotation_items i ON i.quotation_id = q.id
             SET q.quotation_no = ?, q.subject = ?, q.recipient_name = ?,
                 i.item_name = ?, i.qty = ?, i.price = ?, i.unit = ?, i.total = ?
             WHERE i.id = ?";
    $stmt = $conn->prepare($sql1);
    $stmt->bind_param("ssssidsdi", 
        $vals[0], $vals[1], $vals[2], 
        $vals[3], $vals[4], $vals[5], $vals[6], $vals[7], 
        $id
    );
    $stmt->execute();

    $conn->commit();
    echo json_encode(["success" => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "message" => "บันทึกไม่สำเร็จ"]);
}

$conn->close();
?>
