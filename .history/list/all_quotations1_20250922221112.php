// ✅ ฟังก์ชันเลือกจำนวนแถวที่จะแสดง
function updateTableRows() {
    let rowsPerPage = parseInt(document.getElementById("rowsPerPage").value);
    let table = document.getElementById("quotationTable");
    let rows = table.getElementsByTagName("tr");

    let count = 0;
    for (let i = 1; i < rows.length; i++) {
        if (rows[i].style.display !== "none") { // ถ้าไม่ได้ถูกซ่อนจากการค้นหา
            count++;
            rows[i].style.display = (count <= rowsPerPage) ? "" : "none";
        }
    }
}

document.getElementById("rowsPerPage").addEventListener("change", updateTableRows);

// เรียกตอนโหลดหน้า
updateTableRows();
