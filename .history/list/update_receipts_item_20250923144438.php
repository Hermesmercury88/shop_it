<?php
header("Content-Type: application/json");

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "shop_it";

$conn = new mysqli($host,$user,$pass,$dbname);
if($conn->connect_error){
    echo json_encode(["success"=>false,"message"=>"เชื่อมต่อ DB ล้มเหลว"]); exit;
}

$data = json_decode(file_get_contents("php://input"),true);

$id = isset($data['id']) ? intval($data['id']) : 0;
$order_no = $data['order_no'] ?? '';
$customer_name = $data['customer_name'] ?? '';
$customer_address = $data['customer_address'] ?? '';
$item_name = $data['item_name'] ?? '';
$qty = isset($data['qty']) ? floatval($data['qty']) : 0;
$price = isset($data['price']) ? floatval($data['price']) : 0;
$unit = $data['unit'] ?? '';
$total = $qty * $price;

if($id<=0){
    echo json_encode(["success"=>false,"message"=>"ID ไม่ถูกต้อง"]); exit;
}

$sql = "UPDATE receipt_items SET item_name=?, qty=?, price=?, unit=?, total=? WHERE id=?";
$stmt=$conn->prepare($sql);
$stmt->bind_param("sddssi",$item_name,$qty,$price,$unit,$total,$id);

if($stmt->execute()){
    echo json_encode(["success"=>true]);
}else{
    echo json_encode(["success"=>false,"message"=>$stmt->error]);
}

$stmt->close();
$conn->close();
?>
