<?php
session_start();
include('db.php');

// فقط ادمین اجازه دسترسی دارد
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: auth.php");
    exit;
}

// دریافت تمام کامنت‌ها همراه با عنوان خبر مربوطه، مرتب شده بر اساس جدیدترین‌ها
$query = "SELECT comments.id, comments.comment, comments.status, comments.created_at, news.title AS news_title 
          FROM comments 
          LEFT JOIN news ON comments.news_id = news.id
          ORDER BY comments.created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مدیریت نظرات کاربران</title>
    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            direction: rtl;
            padding: 20px;
            background-color: #f9f9f9;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background: #fff;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: right;
        }
        th {
            background-color: #470962;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        a {
            text-decoration: none;
            color: #470962;
            font-weight: bold;
            margin: 0 5px;
        }
        
        h2 {
            color:#470962;
            margin-bottom: 20px;
        }
        .back-link {
            margin-top: 15px;
            display: inline-block;
            padding: 8px 15px;
            background:#470962;
            color: white;
            border-radius: 4px;
        }
        .back-link:hover {
            background: #9b59b6;
        }
    </style>
</head>
<body>

<h2>مدیریت نظرات کاربران</h2>

<table>
    <thead>
        <tr>
            <th>خبر مربوطه</th>
            <th>متن نظر</th>
            <th>وضعیت</th>
            <th>تاریخ ثبت</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while($comment = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($comment['news_title']); ?></td>
                <td><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></td>
                <td>
                    <?php
                        if ($comment['status'] == 'pending') echo "منتظر تایید";
                        elseif ($comment['status'] == 'approved') echo "تایید شده";
                        elseif ($comment['status'] == 'rejected') echo "رد شده";
                        else echo htmlspecialchars($comment['status']);
                    ?>
                </td>
                <td><?php echo $comment['created_at']; ?></td>
                <td>
                    <?php if($comment['status'] == 'pending'): ?>
                        <a href="comment_action.php?action=approve&id=<?php echo $comment['id']; ?>">تأیید</a> |
                        <a href="comment_action.php?action=reject&id=<?php echo $comment['id']; ?>">رد</a> |
                    <?php endif; ?>
                    <a href="comment_action.php?action=delete&id=<?php echo $comment['id']; ?>" onclick="return confirm('آیا از حذف این نظر مطمئن هستید؟')">حذف</a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5" style="text-align:center;">هیچ نظری یافت نشد.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<a href="managerpanel.php" class="back-link">بازگشت به پنل مدیریت</a>

</body>
</html>