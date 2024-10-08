<?php
// เชื่อมต่อกับฐานข้อมูล
include 'server.php';

if (isset($_GET['getCountries'])) {
    // ดึงรายชื่อประเทศจากฐานข้อมูล
    $sql = "SELECT DISTINCT country FROM us_country_2023";
    $result = $conn->query($sql);

    $countries = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $countries[] = $row['country'];
        }
    }
    
    // ส่งข้อมูลประเทศกลับไปในรูปแบบ JSON
    echo json_encode($countries);
    exit;
}

// รับค่าจาก JavaScript (ผ่าน AJAX หรือ fetch)
$selected_country = isset($_GET['country']) ? $_GET['country'] : '';
$selected_month = isset($_GET['month']) ? $_GET['month'] : ''; // รับค่าเดือนจาก GET

// ตรวจสอบว่ามีการเลือก country หรือไม่ ถ้าไม่ให้ส่ง error message
if (!$selected_country) {
    echo json_encode(['error' => 'No country selected']);
    exit;
}

// ดึงข้อมูลจากฐานข้อมูลตามประเทศที่เลือก และกรองตามเดือนจากคอลัมน์ date ที่เป็น DATE
$sql = "SELECT date, SUM(deaths) AS total_deaths, SUM(cases) AS total_cases 
        FROM us_country_2023 
        WHERE country = ?";
$params = [$selected_country];

if ($selected_month) {
    $sql .= " AND MONTH(date) = ?";
    $params[] = $selected_month;
}

// เพิ่ม GROUP BY เพื่อรวมข้อมูลในแต่ละวันที่ซ้ำกัน
$sql .= " GROUP BY date ORDER BY date ASC";

$stmt = $conn->prepare($sql);

// ผูกพารามิเตอร์
$types = str_repeat("s", count($params)); // "s" สำหรับ string, "i" สำหรับ integer
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
if ($result->num_rows > 0) {
    // เก็บข้อมูลลงใน array
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    // ส่งข้อความเตือนถ้าไม่มีข้อมูล
    echo json_encode(['error' => 'ยังไม่มีข้อมูลในเดือนนี้']);
    exit;
}

// ส่งข้อมูลกลับไปในรูปแบบ JSON
echo json_encode($data);

$conn->close();
?>
