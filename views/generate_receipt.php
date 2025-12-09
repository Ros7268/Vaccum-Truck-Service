<?php
require_once '../vendor/autoload.php';
require_once '../library/functions.php';

// Start output buffering
ob_start();

// Validate and sanitize input
if (!isset($_GET['booking_id']) || empty($_GET['booking_id']) || !is_numeric($_GET['booking_id'])) {
    http_response_code(400);
    die("Invalid or missing booking ID.");
}

$bookingId = intval($_GET['booking_id']);

// Use existing dbQuery function
$sql = "
    SELECT 
        r.reservation_id,
        r.address AS booking_address,
        ru.booking_date,
        ru.updated_amount,
        ru.quantity,
        ru.unit_price,
        ru.item_description,
        ru.withholding_tax,
        u.first_name AS user_first_name,
        u.last_name AS user_last_name,
        u.phone AS user_phone,
        u.email AS user_email,
        iu.first_name AS issued_firstname,
        iu.last_name AS issued_lastname
    FROM tbl_revenue_updates ru
    LEFT JOIN tbl_reservations r ON ru.reservation_id = r.reservation_id
    LEFT JOIN tbl_users u ON r.user_id = u.user_id
    LEFT JOIN tbl_users iu ON ru.issued_by = iu.user_id
    WHERE ru.reservation_id = " . $bookingId . "
";

$result = dbQuery($sql);
$booking = dbFetchAssoc($result);

if (!$booking) {
    http_response_code(404);
    die("Booking not found.");
}

// Clear output buffer
ob_clean();

// Load Thai font
$fontPath = TCPDF_FONTS::addTTFfont('../vendor/tecnickcom/tcpdf/fonts/THSarabunNew Bold.ttf', 'TrueTypeUnicode', '', 32);
$fontPathRegular = TCPDF_FONTS::addTTFfont('../vendor/tecnickcom/tcpdf/fonts/THSarabunNew.ttf', 'TrueTypeUnicode', '', 32);

// Initialize PDF with custom settings
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetCreator("Tanawat Service System");
$pdf->SetAuthor("Tanawat Service");
$pdf->SetTitle("ใบเสร็จรับเงิน - " . $booking['user_first_name'] . " " . $booking['user_last_name']);
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->AddPage();

// Company section with styling
$pdf->SetLineWidth(0.5);
$pdf->SetDrawColor(0, 51, 102); // Dark blue border for professional look
$pdf->RoundedRect(15, 15, 180, 34, 2, '1111', 'DF', array(), array(240, 248, 255)); // Light blue background

// Company logo
$pdf->Image('../assets/logo.jpg', 20, 20, 25, '', 'JPG');

// Company details
$pdf->SetFont($fontPath, 'B', 22);
$pdf->SetTextColor(0, 51, 102); // Dark blue text
$pdf->SetXY(50, 20);
$pdf->Cell(80, 10, 'ธนวัฒน์ เซอร์วิส', 0, 1, 'L');

$pdf->SetFont($fontPathRegular, '', 12);
$pdf->SetTextColor(0, 0, 0); // Black text
$pdf->SetXY(50, 30);
$pdf->Cell(80, 6, 'ณิชาภัทร โชคสมัย', 0, 1, 'L');
$pdf->SetXY(50, 35);
$pdf->Cell(80, 6, '53/1 ถ.สวรรคโลก เขตดุสิต แขวงสวนจิตรลดา 10300', 0, 1, 'L');
$pdf->SetXY(50, 40);
$pdf->Cell(80, 6, 'โทร.0842059886', 0, 1, 'L');

// Receipt header with styling
$pdf->SetLineWidth(0.5);
$pdf->SetDrawColor(0, 51, 102);
$pdf->RoundedRect(145, 15, 50, 32, 2, '1111', 'DF', array(), array(240, 248, 255));


$pdf->SetFont($fontPath, 'B', 18);
$pdf->SetTextColor(0, 51, 102);
$pdf->SetXY(150, 17);
$pdf->Cell(40, 10, 'ใบเสร็จรับเงิน', 0, 1, 'C');

// Receipt details
$receiptNumber = 'RE' . date('Ym', strtotime($booking['booking_date'])) . str_pad($booking['reservation_id'], 3, '0', STR_PAD_LEFT);
$issueDate = date('d/m/Y', strtotime($booking['booking_date']));

$pdf->SetFont($fontPathRegular, '', 12);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetXY(145, 27);
$pdf->Cell(50, 6, 'เลขที่: ' . $receiptNumber, 0, 1, 'C');
$pdf->SetXY(145, 33);
$pdf->Cell(50, 6, 'วันที่: ' . $issueDate, 0, 1, 'C');
$pdf->SetXY(145, 39);
$pdf->Cell(50, 6, 'เลขที่การจอง: ' . $booking['reservation_id'], 0, 1, 'C');
// Customer information with styling
$pdf->SetLineWidth(0.5);
$pdf->SetDrawColor(0, 51, 102);
$pdf->RoundedRect(15, 51, 180, 27, 2, '1111', '');

$pdf->SetFont($fontPath, 'B', 14);
$pdf->SetTextColor(0, 51, 102);
$pdf->SetXY(20, 55);
$pdf->Cell(40, 8, 'ข้อมูลลูกค้า:', 0, 1);

$pdf->SetFont($fontPathRegular, '', 13);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetXY(20, 63);
$pdf->Cell(170, 6, 'ลูกค้า: ' . htmlspecialchars($booking['user_first_name'] . ' ' . $booking['user_last_name']), 0, 1);
$pdf->SetXY(20, 69);
$pdf->Cell(170, 6, 'ที่อยู่: ' . htmlspecialchars($booking['booking_address']), 0, 1);

// Calculate values correctly - since updated_amount is AFTER withholding tax
$netAmount = $booking['updated_amount']; // This is the amount after withholding tax
$withholding = 0;
$originalAmount = $netAmount;

// If there's withholding tax, calculate the original amount before tax
if ($booking['withholding_tax']) {
    // If net amount is 97% of original (after 3% deduction)
    // Then original = net / 0.97
    $originalAmount = $netAmount / 0.97;
    $withholding = $originalAmount * 0.03;
}

// Round to 2 decimal places
$originalAmount = round($originalAmount, 2);
$withholding = round($withholding, 2);
$netAmount = round($netAmount, 2);

// Service details table with styling
$pdf->Ln(5);

$pdf->SetLineWidth(0.3);
$pdf->SetDrawColor(0, 51, 102);
$pdf->SetFillColor(240, 248, 255);

$html = '
<table border="1" cellspacing="0" cellpadding="5" style="border-color: #003366;">
    <tr style="background-color: #E6F0FF; color: #003366;">
        <th align="center" width="8%"><strong>#</strong></th>
        <th align="center" width="49%"><strong>รายละเอียดบริการ</strong></th>
        <th align="center" width="13%"><strong>จำนวน</strong></th>
        <th align="center" width="15%"><strong>ราคา/หน่วย</strong></th>
        <th align="center" width="15%"><strong>จำนวนเงิน</strong></th>
    </tr>
    <tr>
        <td align="center">1</td>
        <td>' . htmlspecialchars($booking['item_description'] ?? 'ค่าบริการ') . '</td>
        <td align="center">' . number_format($booking['quantity'], 0) . '</td>
        <td align="right">' . number_format($originalAmount / $booking['quantity'], 2) . '</td>
        <td align="right">' . number_format($originalAmount, 2) . '</td>
    </tr>
';

// Add empty rows if needed for better appearance
for ($i = 0; $i < 2; $i++) {
    $html .= '
    <tr>
        <td align="center"></td>
        <td></td>
        <td align="center"></td>
        <td align="right"></td>
        <td align="right"></td>
    </tr>
    ';
}

$html .= '</table>';
$pdf->writeHTML($html, true, false, true, false, '');

// Summary of amounts with styling
$pdf->SetLineWidth(0.3);
$pdf->SetDrawColor(0, 51, 102);
$pdf->SetFillColor(240, 248, 255);

$summaryHtml = '
<table border="0" cellspacing="0" cellpadding="5">
    <tr>
        <td width="65%"></td>
        <td width="20%" align="right" style="border-bottom: 1px solid #003366;"><strong>รวมเป็นเงิน:</strong></td>
        <td width="15%" align="right" style="border-bottom: 1px solid #003366;">' . number_format($originalAmount, 2) . '</td>
    </tr>
';

if ($booking['withholding_tax']) {
    $summaryHtml .= '
    <tr>
        <td width="65%"></td>
        <td width="20%" align="right" style="border-bottom: 1px solid #003366;"><strong>หัก ณ ที่จ่าย 3%:</strong></td>
        <td width="15%" align="right" style="border-bottom: 1px solid #003366;">' . number_format($withholding, 2) . '</td>
    </tr>
    ';
}

$summaryHtml .= '
    <tr>
        <td width="65%"></td>
        <td width="20%" align="right" style="border-bottom: 2px double #003366; border-top: 1px solid #003366;"><strong>ยอดสุทธิ:</strong></td>
        <td width="15%" align="right" style="border-bottom: 2px double #003366; border-top: 1px solid #003366;"><strong>' . number_format($netAmount, 2) . '</strong></td>
    </tr>
</table>
';

$pdf->writeHTML($summaryHtml, true, false, true, false, '');

// Amount in words
$pdf->Ln(5);
$pdf->SetFont($fontPathRegular, 'I', 13);
$pdf->Cell(0, 10, 'จำนวนเงินตัวอักษร: ' . bahtText($netAmount) . '', 0, 1, 'R');

$pdf->Ln(15);

// Signature sections
$pdf->SetLineWidth(0.2);
$pdf->Line(30, $pdf->GetY(), 90, $pdf->GetY());
$pdf->Line(120, $pdf->GetY(), 180, $pdf->GetY());

$pdf->SetFont($fontPathRegular, '', 12);
$pdf->SetXY(30, $pdf->GetY() + 3);
$pdf->Cell(60, 6, 'ลงชื่อ..............................................ผู้รับเงิน', 0, 0, 'C');
$pdf->SetXY(120, $pdf->GetY());
$pdf->Cell(60, 6, 'ลงชื่อ..............................................ผู้จ่ายเงิน', 0, 1, 'C');

$pdf->SetXY(30, $pdf->GetY() + 6);
$pdf->Cell(60, 6, '(ณิชาภัทร โชคสมัย)', 0, 0, 'C');
$pdf->SetXY(120, $pdf->GetY());
$pdf->Cell(60, 6, '(' . htmlspecialchars($booking['user_first_name'] . ' ' . $booking['user_last_name']) . ')', 0, 1, 'C');



// Helper function to convert number to Thai Baht text
function bahtText($amount) {
    $number = number_format($amount, 2, '.', '');
    $arr = explode('.', $number);
    $baht = convertNumberToText($arr[0]);
    $satang = convertNumberToText($arr[1]);
    
    return $baht . 'บาท' . ($satang != '' ? $satang . 'สตางค์' : 'ถ้วน');
}

function convertNumberToText($number) {
    $number = intval($number);
    $textValues = array('ศูนย์', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า');
    $unitPosition = array('', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน');
    
    if ($number == 0) return '';
    
    $text = '';
    $digits = str_split((string) $number);
    $digitsLen = count($digits);
    
    for ($i = 0; $i < $digitsLen; $i++) {
        $digit = (int) $digits[$i];
        if ($digit > 0) {
            // Special case for "หนึ่ง" in the tens position, which is pronounced as "สิบ", not "หนึ่งสิบ"
            if ($digit == 1 && $digitsLen - $i == 2) {
                $text .= $unitPosition[$digitsLen - $i - 1];
            }
            // Special case for "สอง" in the tens position, which is pronounced as "ยี่สิบ", not "สองสิบ"
            else if ($digit == 2 && $digitsLen - $i == 2) {
                $text .= 'ยี่' . $unitPosition[$digitsLen - $i - 1];
            }
            // Special case for "หนึ่ง" in the ones position, which is pronounced as "เอ็ด", not "หนึ่ง"
            else if ($digit == 1 && $digitsLen - $i == 1 && $digitsLen > 1) {
                $text .= 'เอ็ด';
            } else {
                $text .= $textValues[$digit] . $unitPosition[$digitsLen - $i - 1];
            }
        }
    }
    
    return $text;
}

// Output PDF
$pdf->Output('receipt_' . $bookingId . '.pdf', 'D');
exit;

?>