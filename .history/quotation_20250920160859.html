<?php
require 'vendor/autoload.php'; // ถ้าใช้ Composer
use Dompdf\Dompdf;

// รับค่าจากฟอร์ม
$order_no = $_POST['order_no']; // เพิ่มตรงนี้
$customer = $_POST['customer_name'];
$address = $_POST['customer_address'];
$items = $_POST['items']; // array

// สร้าง HTML สำหรับ PDF
$html = "
<h2 style='text-align:center;'>ใบเสร็จรับเงิน</h2>
<p><b>เลขที่ใบสั่งซื้อ (Order No):</b> $order_no</p>
<p><b>ชื่อลูกค้า:</b> $customer</p>
<p><b>ที่อยู่:</b> $address</p>
<table border='1' width='100%' cellspacing='0' cellpadding='5'>
    <tr>
        <th>ลำดับ</th>
        <th>รายการ</th>
        <th>จำนวน</th>
        <th>หน่วย</th>
        <th>ราคา</th>
        <th>รวม</th>
    </tr>
";

// ใส่รายการสินค้า
$total = 0;
foreach ($items as $i => $item) {
    $sum = $item['qty'] * $item['price'];
    $total += $sum;
    $html .= "
    <tr>
        <td>".($i+1)."</td>
        <td>{$item['name']}</td>
        <td>{$item['qty']}</td>
        <td>{$item['unit']}</td>
        <td>{$item['price']}</td>
        <td>$sum</td>
    </tr>";
}

$html .= "
    <tr>
        <td colspan='5' align='right'><b>รวมทั้งหมด</b></td>
        <td><b>$total</b></td>
    </tr>
</table>
<p style='margin-top:50px;'>ลงชื่อผู้รับเงิน ...........................................</p>
";

// สร้าง PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// บันทึกเป็นไฟล์
$output = $dompdf->output();
$filename = "receipt_" . $order_no . "_" . time() . ".pdf"; // ใส่ order_no ในชื่อไฟล์
file_put_contents("receipt_pdfs/$filename", $output);

// โหลดไฟล์ให้ user ดาวน์โหลด
header("Content-type: application/pdf");
header("Content-Disposition: inline; filename=$filename");
echo $output;
?>
