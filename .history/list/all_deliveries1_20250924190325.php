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
        i.id AS item_id,  
        d.customer_name,
        d.delivery_no,
        i.item_name,
        i.qty,
        i.price,
        i.unit,
        i.total,
        d.pdf_file
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

<style>
body {
    margin: 0;
    font-family: Tahoma, sans-serif;
    background: #eeece6ff;
}

/* Sidebar */
.sidebar {
    width: 220px;
    height: 100vh;
    background: #57564F;
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
    margin: 15px 0;
    display: block;
    width: 100%;
    text-align: center;
    transition: background 0.3s;
}
.sidebar a:hover {
    background: #57564F;
    border-radius: 6px;
}

/* Content */
.content {
    margin-left: 220px;
    padding: 20px;
}

/* Topbar */
.topbar {
    background: #7A7A73;
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
    background: #7A7A73;
    font-weight: 600;
    color: #fff; /* ทำให้ตัวอักษรเป็นสีขาว */
}
tr:nth-child(even) {
    background: #fdf5ef;
}
tr:hover {
    background: #DDDAD0;
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
    border-color: #a3a2b1ff;
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

.action-btn {
    padding: 5px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin: 2px;
}

.btn-upload {
    margin-top: 20px;
    background: #7cf7b3ff;
    color: #000;
    padding: 10px 25px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.3s;
}
.btn-upload:hover {
    background: #3ad334ff;
}

.btn-edit { background: #4caf50; color: #fff; }
.btn-save { background: #2196f3; color: #fff; }
.btn-cancel { background: #777; color: #fff; }
.btn-delete { background: #f44336; color: #fff; }

/* Print PDF */
@media print {
    @page {
        size: A4 portrait;
        margin: 5mm;
    }

    body {
        background: #fff;
        margin: 0;
        font-size: 12pt;
    }

    .sidebar,
    .search-box,
    .btn-pdf,
    .btn-upload,
    .btn-edit,
    .btn-delete
    {
        display: none !important;
    }

    table {
        border-collapse: collapse;
        width: auto;            /* ขนาดตามเนื้อหา */
        margin: 0;              /* ชิดซ้ายสุด */
        border: 5px solid #000; /* เส้นรอบตารางหนาชัด */
    }

    th, td {
        border: 1px solid #000; /* เส้นด้านในปกติ */
        padding: 6px;
        text-align: center;
    }

    /* ซ่อนคอลัมน์สุดท้าย */
    table th:last-child,
    table td:last-child {
        display: none !important;
    }


}

</style>
</head>
<body>
<div class="sidebar">
    <a href="http://localhost/shop/list/all_quotations1.php">ใบเสนอราคา</a>
    <a href="http://localhost/shop/list/all_deliveries1.php">ใบส่งของ</a>
    <a href="http://localhost/shop/list/all_receipts1.php">ใบเสร็จรับเงิน</a>
</div>

<div class="content">
    <div class="topbar">
        <img src="../pic/logo.png" alt="LOGO">
    </div>

    <h2>รายการใบส่งของ</h2>

    <!-- ✅ search box เดิม -->
    <div class="search-box">
        <label for="searchColumn">ค้นหาโดย: </label>
        <select id="searchColumn">
            <option value="1">ชื่อลูกค้า</option>
            <option value="2">เลขที่ / NO.</option>
            <option value="3">รายการสินค้า</option>
        </select>
        <input type="text" id="searchInput" placeholder="พิมพ์คำค้นหา...">

        <label for="rowsPerPage">แสดงแถว: </label>
        <select id="rowsPerPage">
            <option value="5">5</option>
            <option value="10" selected>10</option>
            <option value="20">20</option>
            <option value="50">50</option>
        </select>
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
                <th>จัดการ</th> 
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr data-id='" . $row['item_id'] . "'>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td class='editable'>" . htmlspecialchars($row['customer_name']) . "</td>";
                    echo "<td class='editable'>" . htmlspecialchars($row['delivery_no']) . "</td>";
                    echo "<td class='editable'>" . htmlspecialchars($row['item_name']) . "</td>";
                    echo "<td class='editable col-qty'>" . $row['qty'] . "</td>";
                    echo "<td class='editable col-price'>" . number_format($row['price'],2) . "</td>";
                    echo "<td class='editable'>" . htmlspecialchars($row['unit']) . "</td>";
                    echo "<td class='editable col-total'>" . number_format($row['total'],2) . "</td>";
                    echo "<td>";
                    // ปุ่มดู PDF ถ้ามีไฟล์
                    if (!empty($row['pdf_file']) && file_exists($row['pdf_file'])) {
                        echo "<a href='" . $row['pdf_file'] . "' target='_blank' class='action-btn btn-pdf'>ดู PDF</a>";
        }
        echo " <button class='action-btn btn-edit'>แก้ไข</button>";
        echo " <button class='action-btn btn-delete'>ลบ</button>";
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='9'>ยังไม่มีข้อมูล</td></tr>";
}
?>

        </tbody>
    </table>

    <button class="btn-pdf" onclick="window.location.href='../index.html'">กลับหน้าหลัก</button>
    <button class="btn-pdf" onclick="window.print()">พิมพ์ PDF</button>
    <button class="btn-upload" onclick="window.open('../delivery1.html', '_blank')">เพิ่มใบส่งของ</button>
</div>

<script>
// ✅ ฟังก์ชันค้นหา
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

// ✅ ฟังก์ชันเลือกจำนวนแถว
function updateTableRows() {
    let rowsPerPage = parseInt(document.getElementById("rowsPerPage").value);
    let table = document.getElementById("deliveryTable");
    let rows = table.getElementsByTagName("tr");

    let count = 0;
    for (let i = 1; i < rows.length; i++) {
        if (rows[i].style.display !== "none") {
            count++;
            rows[i].style.display = (count <= rowsPerPage) ? "" : "none";
        }
    }
}
document.getElementById("rowsPerPage").addEventListener("change", updateTableRows);
updateTableRows();

// ✅ แก้ไข/บันทึก/ยกเลิก
document.querySelectorAll(".btn-edit").forEach(btn => {
    btn.addEventListener("click", function() {
        let row = this.closest("tr");
        let tds = row.querySelectorAll(".editable");

        if (this.textContent === "แก้ไข") {
            // แปลงเป็น input
            tds.forEach(td => {
                let val = td.textContent.trim();
                td.innerHTML = `<input value="${val}">`;
            });

            // ✅ จับช่อง qty, price, total
            let qtyInput = row.querySelector(".col-qty input");
            let priceInput = row.querySelector(".col-price input");
            let totalCell = row.querySelector(".col-total");

            // ฟังก์ชันคำนวณราคารวม
            function updateTotal() {
                let qtyStr = qtyInput.value.trim().replace(/,/g, "");
                let priceStr = priceInput.value.trim().replace(/,/g, "");

                if (!qtyStr) qtyStr = "0";
                if (!priceStr) priceStr = "0";

                let total = parseFloat(qtyStr) * parseFloat(priceStr);

                if (Number.isInteger(total)) {
                    totalCell.innerHTML = `<input value="${total}">`; // ถ้าเป็นเลขกลม
                } else {
                    totalCell.innerHTML = `<input value="${total.toFixed(2)}">`; // ถ้ามีเศษทศนิยม
                }
            }

            // ✅ คำนวณ total เดิมทันที
            updateTotal();

            qtyInput.addEventListener("input", updateTotal);
            priceInput.addEventListener("input", updateTotal);

            this.textContent = "บันทึก";
            this.className = "action-btn btn-save";

            let cancelBtn = document.createElement("button");
            cancelBtn.textContent = "ยกเลิก";
            cancelBtn.className = "action-btn btn-cancel";
            this.after(cancelBtn);
            cancelBtn.addEventListener("click", () => location.reload());

        } else {
            // ✅ เก็บค่า input ส่งไป PHP
            let data = [];
            tds.forEach(td => {
                let val = td.querySelector("input").value.trim().replace(/,/g, "");
                let num = parseFloat(val);

                if (!isNaN(num)) {
                    if (Number.isInteger(num)) {
                        data.push(num.toString()); // เก็บเป็น int ถ้าเลขกลม เช่น 7500
                    } else {
                        data.push(num.toFixed(2)); // เก็บทศนิยมถ้ามี เช่น 7500.50
                    }
                } else {
                    data.push(val); // เผื่อเป็นข้อความ เช่น "รายการ"
                }
            });
            let id = row.dataset.id;

            fetch("update_delivery_item.php", {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify({id, data})
            }).then(r => r.json()).then(res => {
                if (res.success) location.reload();
                else alert("บันทึกไม่สำเร็จ");
            });
        }
    });
});

document.querySelectorAll(".btn-delete").forEach(btn => {
    btn.addEventListener("click", function() {
        if (!confirm("คุณต้องการลบข้อมูลนี้หรือไม่?")) return;
        let row = this.closest("tr");
        let id = row.dataset.id;

        fetch("delete_delivery_item.php", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({id})
        }).then(r => r.json()).then(res => {
            if (res.success) row.remove();
            else alert("ลบไม่สำเร็จ");
        });
    });
});
</script>

</body>
</html>
<?php $conn->close(); ?>