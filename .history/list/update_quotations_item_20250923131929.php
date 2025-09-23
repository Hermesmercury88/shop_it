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
  $vals:
  [0] = quotation_no
  [1] = subject
  [2] = recipient_name
  [3] = item_name
  [4] = qty
  [5] = price
  [6] = unit
  [7] = total
*/

$conn->begin_transaction();

try {
    // 1️⃣ update ตาราง quotation_items
    $stmt1 = $conn->prepare("UPDATE quotation_items SET item_name=?, qty=?, price=?, unit=?, total=? WHERE id=?");
    $stmt1->bind_param("sidssi", $vals[3], $vals[4], $vals[5], $vals[6], $vals[7], $id);
    $stmt1->execute();

    // 2️⃣ update ตาราง quotations (อิงจาก quotation_items.id -> quotation_id)
    $stmt2 = $conn->prepare("
        UPDATE quotations q
        JOIN quotation_items i ON i.quotation_id = q.id
        SET q.quotation_no=?, q.subject=?, q.recipient_name=?
        WHERE i.id=?
    ");
    $stmt2->bind_param("sssi", $vals[0], $vals[1], $vals[2], $id);
    $stmt2->execute();

    $conn->commit();
    echo json_encode(["success" => true]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

$conn->close();
?>
