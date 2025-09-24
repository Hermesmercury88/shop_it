<?php
$host="localhost";
$user="root";
$pass="";
$dbname="shop_it";

$conn = new mysqli($host,$user,$pass,$dbname);
if($conn->connect_error){
    die(json_encode(["success"=>false,"message"=>"เชื่อมต่อ DB ล้มเหลว"]));
}

if(!isset($_POST['delivery_id']) || !isset($_FILES['pdf_file'])){
    echo json_encode(["success"=>false,"message"=>"ข้อมูลไม่ครบ"]);
    exit;
}

$delivery_id = intval($_POST['delivery_id']);
$file = $_FILES['pdf_file'];

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if($ext != "pdf"){
    echo json_encode(["success"=>false,"message"=>"ไฟล์ต้องเป็น PDF"]);
    exit;
}

$uploadDir = "../uploads/deliveries/";
if(!is_dir($uploadDir)) mkdir($uploadDir,0777,true);

$newFileName = "delivery".$delivery_id."_".time().".pdf";
$uploadPath = $uploadDir . $newFileName;

if(move_uploaded_file($file['tmp_name'],$uploadPath)){
    $stmt = $conn->prepare("UPDATE deliveries SET pdf_file=? WHERE id=?");
    $stmt->bind_param("si", $newFileName, $delivery_id);
    if($stmt->execute()){
        echo json_encode(["success"=>true, "pdf_file"=>$newFileName]);
    } else {
        echo json_encode(["success"=>false,"message"=>"อัปเดตฐานข้อมูลล้มเหลว"]);
    }
    $stmt->close();
} else {
    echo json_encode(["success"=>false,"message"=>"อัปโหลดไฟล์ล้มเหลว"]);
}

$conn->close();
?>
