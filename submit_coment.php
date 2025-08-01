<?php
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $news_id = $_POST['news_id'];
    $comment = trim($_POST['comment']);

    if (!empty($comment) && !empty($news_id)) {
        $stmt = $conn->prepare("INSERT INTO comments (news_id, comment, status, created_at) VALUES (?, ?, 'pending', NOW())");
        $stmt->bind_param("is", $news_id, $comment);

        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "خطا در ثبت نظر!";
        }

        $stmt->close();
    } else {
        echo "اطلاعات ناقص!";
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    </head>
