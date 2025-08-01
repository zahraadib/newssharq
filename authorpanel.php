<?php
// اتصال به دیتابیس
include('db.php');
session_start();

// بررسی اینکه آیا نویسنده وارد شده است یا نه
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');  // در صورتی که وارد نشده باشد، به صفحه لاگین هدایت می‌شود
    exit();
}

// دریافت شناسه نویسنده از session
$userId = $_SESSION['user_id'];
  
//حذف خبر
if (isset($_POST['delete_news'])) {
    $newsId = $_POST['delete_news'];
    $sql = "DELETE FROM news WHERE id = $newsId";
    if ($conn->query($sql) === TRUE) {
        echo "خبر حذف شد.";
    } else {
        echo "خطا در حذف خبر: " . $conn->error;
    }
}

// نمایش اخبار نویسنده
$sql = "SELECT * FROM news WHERE author_id = '" . $conn->real_escape_string($_SESSION['user_id']) . "'";  
// فقط اخبار نویسنده لاگین شده را می‌آورد
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/authorpanel.css">
    <title>پنل نویسنده</title>
</head>
<body>
    <header>
        <div class="newsagency-information">
            <div class="logo">
                <img src="img/hhh.png" alt="">
            </div>
            <div class="name">
                <p> خبرگزاری  شرق - پنل نویسندگان</p>
            </div>
        </div>
    </header>

    <nav>
        <div class="categories-list">
            <a href="index.php">خانه</a>
            <a href="#" onclick="confirmLogout()">خروج</a>
        </div>
    </nav>

    <div class="panel">
        <h2>پنل مدیریت اخبار</h2>
        <div class="button-group">
            <button onclick="location.href='addnews.php'"  >نوشتن خبر جدید</button>
        </div>
    </div>

    <div class="news-list">
        <h2>اخبار شما</h2>
        <table>
            <thead>
                <tr>
                    <th>عنوان</th>
                    <th>تاریخ انتشار</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row["title"] . "</td>
                                <td>" . $row["created_at"] . "</td>
                                <td class='" . strtolower($row["status"]) . "'>
                                    " . ($row["status"] == "approved" ? "تایید شده" : 
                                    ($row["status"] == "pending" ? "در انتظار تایید" : 
                                    ($row["status"] == "rejected" ? "رد شده" : "نامشخص"))) . "
                                </td>

                                <td>
                                    <form method='POST'>
                                        <button type='button' onclick=\"window.location.href='editnews.php?id=" . $row["id"] . "'\">ویرایش</button>
                                        <button type='submit' name='delete_news' value='" . $row["id"] . "' onclick='return confirm(\"آیا از حذف این خبر مطمئن هستید؟\")'>حذف</button>
                                    </form>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>هیچ خبری برای نمایش وجود ندارد.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>



    <script>
        
        function confirmLogout() {
            if (confirm("آیا مطمئن هستید که می‌خواهید خارج شوید؟")) {
                window.location.href = "logout.php"; // انتقال به logout.php برای خروج
            }
        }    
    
    </script>

</body>
</html>
