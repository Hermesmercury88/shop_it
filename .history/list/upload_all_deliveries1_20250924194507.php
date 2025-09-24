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

// ตรวจสอบนามสกุล PDF
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
if(strtolower($ext)!="pdf"){
    echo json_encode(["success"=>false,"message"=>"ไฟล์ต้องเป็น PDF"]);
    exit;
}

// สร้างโฟลเดอร์ถ้ายังไม่มี
$uploadDir = "../uploads/deliveries/"; // ../ ขึ้นไปหนึ่งระดับ
if(!is_dir($uploadDir)) mkdir($uploadDir,0777,true);

$newFileName = "delivery_".$delivery_id."_".time().".pdf";
$uploadPath = $uploadDir.$newFileName;

if(move_uploaded_file($file['tmp_name'],$uploadPath)){
    // อัพเดต database
    $stmt = $conn->prepare("UPDATE deliveries SET pdf_file=? WHERE id=?");
    $stmt->bind_param("si",$newFileName,$delivery_id);
    $stmt->execute();
    $stmt->close();

    // ส่งชื่อไฟล์กลับเพื่ออัปเดตลิงก์
    echo json_encode(["success"=>true,"pdf_file"=>$newFileName]);
} else {
    echo json_encode(["success"=>false,"message"=>"อัพโหลดไฟล์ล้มเหลว"]);
}

$conn->close();
