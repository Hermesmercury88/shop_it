<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "shop_it";

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// ดึงข้อมูลจากตาราง deliveries + delivery_items
$sql = "
    SELECT 
        d.customer_name,
        d.delivery_no,
        i.item_name,
        i.qty,
        i.price,
        i.unit,
        i.total
    FROM deliveries d
    JOIN delivery_items i ON i.delivery_id = d.id
    ORDER BY d.id, i.id
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ใบส่งของ</title>

<link rel="shortcut icon" href="pic/logo.png" type="image/x-icon" />
<link rel="icon" href="pic/logo.png" type="image/x-icon" />
<link rel="apple-touch-icon" href="pic/logo.png" />
<link href="../css/delivery1.css" rel="stylesheet">

<style>
body {
    margin: 0;
    font-family: Tahoma, sans-serif;
    background: #f9f9ef;
}

/* Sidebar */
.sidebar {
    width: 220px;
    height: 100vh;
    background: #b89172;
    float: left;
    color: #fff;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 30px;
}
.sidebar a {
    color: #fff;
    text-decoration: none;
    margin: 10px 0;
    display: block;
    width: 100%;
    text-align: center;
    transition: background 0.3s;
}
.sidebar a:hover {
    background: rgba(255,255,255,0.2);
    border-radius: 6px;
}

/* Content */
.content {
    margin-left: 220px;
    padding: 20px;
}

/* Topbar */
.topbar {
    background: #d4b295;
    padding: 15px 0;
    text-align: center;
    margin-bottom: 25px;
    box-sizing: border-box;
    border-radius: 6px 6px 0 0;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
.topbar img {
    max-height: 60px;
    display: block;
    margin: 0 auto;
}

/* Headline */
h2 {
    margin: 20px 0;
    font-weight: 600;
    color: #4d3a2c;
}

/* Table */
table {
    width: 100%;
    border-collapse: collapse;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}
th, td {
    border: 1px solid #ccc;
    padding: 10px;
    text-align: center;
    transition: background 0.3s;
}
th {
    background: #e5c3a6;
    font-weight: 600;
}
tr:nth-child(even) {
    background: #fdf5ef;
}
tr:hover {
    background: #ffe8d0;
}

/* Search box */
.search-box {
    margin: 10px 0 15px;
}
input, select {
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    outline: none;
}
input:focus, select:focus {
    border-color: #b89172;
}

/* PDF button */
.btn-pdf {
    margin-top: 20px;
    background: #f77c7c;
    color: #000;
    padding: 10px 25px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.3s;
}
.btn-pdf:hover {
    background: #f55c5c;
}
</style>
</head>
<body>
<div class="sidebar">
    <a href="#">ใบเสนอราคา</a>
    <a href="#">ใบสั่งของ</a>
    <a href="#">ใบเสร็จรับเงิน</a>
</div>

<div class="content">
    <div class="topbar">
        <img src="../pic/logo.png" alt="LOGO">
    </div>

    <h2>รายการใบส่งของ</h2>

    <div class="search-box">
        <label for="searchColumn">ค้นหาโดย: </label>
        <select id="searchColumn">
            <option value="1">ชื่อลูกค้า</option>
            <option value="2">เลขที่ / NO.</option>
            <option value="3">รายการสินค้า</option>
        </select>
        <input type="text" id="searchInput" placeholder="พิมพ์คำค้นหา...">
    </div>

    <table id="deliveryTable">
        <thead>
            <tr>
                <th>ลำดับ</th>
                <th>ชื่อลูกค้า</th>
                <th>เลขที่ / NO.</th>
                <th>รายการ</th>
                <th>จำนวน</th>
                <th>ราคา</th>
                <th>หน่วย</th>
                <th>ราคารวม</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['delivery_no']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                    echo "<td>" . $row['qty'] . "</td>";
                    echo "<td>" . number_format($row['price'],2) . "</td>";
                    echo "<td>" . htmlspecialchars($row['unit']) . "</td>";
                    echo "<td>" . number_format($row['total'],2) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>ยังไม่มีข้อมูล</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <button class="btn-pdf" onclick="window.print()">พิมพ์ PDF</button>
</div>

<script>
// ฟังก์ชันค้นหา (client side)
document.getElementById("searchInput").addEventListener("keyup", function() {
    let input = this.value.toLowerCase();
    let table = document.getElementById("deliveryTable");
    let rows = table.getElementsByTagName("tr");
    let column = parseInt(document.getElementById("searchColumn").value);

    for (let i = 1; i < rows.length; i++) {
        let cell = rows[i].getElementsByTagName("td")[column];
        if (cell) {
            let text = cell.textContent.toLowerCase();
            rows[i].style.display = text.includes(input) ? "" : "none";
        }
    }
});
</script>

</body>
</html>
<?php $conn->close(); ?>
