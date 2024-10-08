<?php
include("server.php");
// รับข้อมูลจากฟอร์ม
$email = $_POST['email'];
$username = $_POST['username'];
$fullname = $_POST['fullname'];
$password = $_POST['password'];

// ตรวจสอบว่ามีข้อมูลซ้ำหรือไม่
$sql_check_duplicate = "SELECT * FROM user WHERE username = ?";
$stmt = $conn->prepare($sql_check_duplicate);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // หากพบข้อมูลซ้ำ
    echo "<script>alert('username นี้ถูกใช้ไปแล้ว!'); window.location.href='register.php';</script>";
} else {
    // หากไม่พบข้อมูลซ้ำ ให้เพิ่มข้อมูลใหม่
    $sql_insert = "INSERT INTO user (email, username, fullname, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("ssss", $email, $username, $fullname, $password);


    if ($stmt->execute()) {
        echo "<script>alert('เพิ่มสมาชิกสำเร็จ!'); window.location.href='login.php';</script>";
    } else {
        echo "Error: " . $sql_insert . "<br>" . $conn->error;
    }
}

// ปิดการเชื่อมต่อฐานข้อมูล
$stmt->close();
$conn->close();
?>
