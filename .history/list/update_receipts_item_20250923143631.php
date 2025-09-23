<?php
header("Content-Type: application/json");
$conn = new mysqli("localhost","root","","shop_it");

if($conn->connect_error){
    echo json_encode(["success"=>false,"message"=>"เชื่อมต่อฐานข้อมูลล้มเหลว"]);
    exit;
}

$data=json_decode(file_get_contents("php://input"),true);
$id=intval($data['id'] ?? 0);
$fields=$data['data'] ?? [];

if($id<=0 || !$fields){
    echo json_encode(["success"=>false,"message"=>"ข้อมูลไม่ถูกต้อง"]);
    exit;
}

$fields['total']=floatval($fields['qty'])*floatval($fields['price']);

$sql="UPDATE receipt_items SET 
        order_no=?, customer_name=?, customer_address=?,
        item_name=?, qty=?, price=?, unit=?, total=? 
      WHERE id=?";
$stmt=$conn->prepare($sql);
$stmt->bind_param(
    "sssssiddi",
    $fields['order_no'],
    $fields['customer_name'],
    $fields['customer_address'],
    $fields['item_name'],
    $fields['qty'],
    $fields['price'],
    $fields['unit'],
    $fields['total'],
    $id
);

if($stmt->execute()) echo json_encode(["success"=>true]);
else echo json_encode(["success"=>false,"message"=>$stmt->error]);

$stmt->close();
$conn->close();
?>
