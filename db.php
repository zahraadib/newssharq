<?php
$servername = "localhost"; // آدرس سرور
$username = "root"; // نام کاربری دیتابیس
$password = ""; // پسورد دیتابیس
$dbname = "news_agency"; // نام دیتابیس

// ایجاد اتصال به دیتابیس
$conn = mysqli_connect($servername, $username, $password, $dbname);

// بررسی اتصال
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    </head>
