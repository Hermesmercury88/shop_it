<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "shop_it";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

$id = $_GET['id'] ?? 0;

// ถ้ามีการ submit แก้ไข
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = $_POST['item_name'];
    $qty = $_POST['qty'];
    $price = $_POST['price'];
    $unit = $_POST['unit'];
    $total = $qty * $price;

    $stmt = $conn->prepare("UPDATE delivery_items SET item_name=?, qty=?, price=?, unit=?, total=? WHERE id=?");
    $stmt->bind_param("sidsdi", $item_name, $qty, $price, $unit, $total, $id);

    if ($stmt->execute()) {
        echo "<script>alert('แก้ไขสำเร็จ'); window.location='all_deliveries1.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// ดึงข้อมูลเก่า
$stmt = $conn->prepare("SELECT item_name, qty, price, unit FROM delivery_items WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>แก้ไขข้อมูล</title>
</head>
<body>
<h2>แก้ไขข้อมูลสินค้า</h2>
<form method="post">
    รายการ: <input type="text" name="item_name" value="<?php echo htmlspecialchars($data['item_name']); ?>"><br><br>
    จำนวน: <input type="number" name="qty" value="<?php echo $data['qty']; ?>"><br><br>
    ราคา: <input type="text" name="price" value="<?php echo $data['price']; ?>"><br><br>
    หน่วย: <input type="text" name="unit" value="<?php echo htmlspecialchars($data['unit']); ?>"><br><br>
    <input type="submit" value="บันทึก">
</form>
</body>
</html>
<?php $conn->close(); ?>
