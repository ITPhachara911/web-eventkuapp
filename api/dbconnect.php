<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kuevent"; // เปลี่ยนเป็นชื่อฐานข้อมูลของคุณ

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}





?>
