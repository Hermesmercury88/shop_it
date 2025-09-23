<?php
header('Content-Type: application/json');

// เชื่อมต่อฐานข้อมูล
$host="localhost";
$user="root";
$pass="";
$dbname="shop_it";

$conn=new mysqli($host,$user,$pass,$dbname);
if($conn->connect_error){
    echo json_encode(['success'=>false,'msg'=>'เชื่อมต่อฐานข้อมูลล้มเหลว']);
    exit;
}

// รับ JSON จาก fetch
$data = json_decode(file_get_contents('php://input'), true);

if(!isset($data['id']) || !isset($data['data'])){
    echo json_encode(['success'=>false,'msg'=>'ข้อมูลไม่ครบ']);
    exit;
}

$id = intval($data['id']);
$item = $data['data'];

// ตรวจสอบและคำนวณ total
$qty = floatval($item['qty'] ?? 0);
$price = floatval($item['price'] ?? 0);
$total = $qty * $price;

// ปรับข้อมูล SQL (อัปเดตรายการใน receipt_items)
$sql = "UPDATE receipt_items SET 
        qty=?, price=?, total=?, item_name=?, unit=?
        WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ddsssi",
    $qty,
    $price,
    $total,
    $item['item_name'],
    $item['unit'],
    $id
);

if($stmt->execute()){
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'msg'=>$stmt->error]);
}
$stmt->close();
$conn->close();
?>
