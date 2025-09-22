<?php
$servername = "localhost";
$username = "root"; // เปลี่ยนตามจริง
$password = "";     // เปลี่ยนตามจริง
$dbname = "shop_it";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL สำหรับสร้างตาราง quotations
$sql1 = "CREATE TABLE IF NOT EXISTS quotations (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    quotation_no VARCHAR(50) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    recipient_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

// SQL สำหรับสร้างตาราง quotation_items
$sql2 = "CREATE TABLE IF NOT EXISTS quotation_items (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    quotation_id INT(11) NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    qty INT(11) DEFAULT 0,
    price DECIMAL(10,2) DEFAULT 0.00,
    unit VARCHAR(50) DEFAULT NULL,
    total DECIMAL(10,2) DEFAULT 0.00,
    FOREIGN KEY (quotation_id) REFERENCES quotations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

// รัน SQL
$conn->query($sql1);
$conn->query($sql2);

$conn->close();

// ✅ เสร็จแล้วเด้งกลับไปหน้า index.html
header("Location: index.html");
exit;
?>
