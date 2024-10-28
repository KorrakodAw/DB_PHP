<?php
// ตั้งค่าการเชื่อมต่อฐานข้อมูล
    $servername = "localhost";
    $username = "root";
    $password = "12345678"; // ใส่รหัสผ่าน MySQL ถ้ามี
    $dbname = "database_project";

// สร้างการเชื่อมต่อ
    $conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
    if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    } 

?>