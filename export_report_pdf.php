<?php
session_start();
include 'db_connection.php';
require 'vendor/autoload.php'; 

use TCPDF;

if (!isset($_SESSION['pharmacist_id'])) {
    header("Location: logininvent.php");
    exit;
}

$pharmacyId = $_SESSION['pharmacy_id'];
$startDate = $_POST['start_date'];
$endDate = $_POST['end_date'];

$reportData = generateStockReport($pharmacyId, $startDate, $endDate, 10000, 0); // Generate all data without pagination

$pdf = new TCPDF();
$pdf->AddPage();

$pdf->SetFont('helvetica', '', 12);

$html = '<h1>Stock Report</h1>';
$html .= '<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th>Category</th>
            <th>Product Name</th>
            <th>Stock Quantity</th>
            <th>Movement Type</th>
            <th>Quantity</th>
            <th>Movement Date</th>
            <th>Price</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>';

foreach ($reportData as $data) {
    $html .= '<tr>
        <td>' . htmlspecialchars($data['CategoryName']) . '</td>
        <td>' . htmlspecialchars($data['ProductName']) . '</td>
        <td>' . htmlspecialchars($data['StockQuantity']) . '</td>
        <td>' . htmlspecialchars($data['MovementType']) . '</td>
        <td>' . htmlspecialchars($data['Quantity']) . '</td>
        <td>' . htmlspecialchars($data['MovementDate']) . '</td>
        <td>' . htmlspecialchars(number_format($data['Price'], 2)) . '</td>
        <td>' . htmlspecialchars(number_format($data['Quantity'] * $data['Price'], 2)) . '</td>
    </tr>';
}

$html .= '<tr>
    <td colspan="7"><strong>Total Purchases:</strong></td>
    <td>' . htmlspecialchars(number_format($totalPurchases, 2)) . '</td>
</tr>';
$html .= '<tr>
    <td colspan="7"><strong>Total Sales:</strong></td>
    <td>' . htmlspecialchars(number_format($totalSales, 2)) . '</td>
</tr>';

$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Output('stock_report_' . date('Ymd') . '.pdf', 'D');
exit;
