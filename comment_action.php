<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    </head>
<?php
session_start();
include('db.php');

// فقط ادمین اجازه داره
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: auth.php");
    exit();
}

// بررسی اینکه آیدی کامنت و اکشن ارسال شده باشه
if (isset($_GET['id']) && isset($_GET['action'])) {
    $comment_id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === 'approve') {
        $query = "UPDATE comments SET status = 'approved' WHERE id = ?";
    } elseif ($action === 'reject') {
        $query = "UPDATE comments SET status = 'rejected' WHERE id = ?";
    } elseif ($action === 'delete') {
        $query = "DELETE FROM comments WHERE id = ?";
    } else {
        // اکشن نامعتبر
        header("Location: manage_comments.php?error=invalid_action");
        exit();
    }

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $comment_id);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        header("Location: manage_comments.php?success=1");
        exit();
    } else {
        header("Location: manage_comments.php?error=not_found");
        exit();
    }

} else {
    // اگر پارامترها ارسال نشده بود
    header("Location: manage_comments.php?error=missing_params");
    exit();
}
?>