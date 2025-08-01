<?php

session_start(); // شروع سشن در ابتدای کد

include('db.php');

// بررسی اینکه کاربر وارد شده باشد
if (!isset($_SESSION['user_id'])) {
    die("خطا: شما وارد حساب کاربری خود نشده‌اید!");
}



// بررسی ارسال فرم
if (isset($_POST['submit_news'])) {
    // دریافت اطلاعات از فرم
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $image = $conn->real_escape_string($_POST['image']);
    $category = $conn->real_escape_string($_POST['category']);
    $author_id = $_SESSION['user_id'];  // دریافت شناسه کاربر از سشن

    // بررسی نقش کاربر و تنظیم وضعیت تأیید خبر
    if ($_SESSION['role'] == 'admin') {
        $approved = 'approved';  // اگر کاربر ادمین باشد، خبر مستقیماً تأیید شود
    } else {
        $approved = 'pending';  // اگر نویسنده باشد، خبر نیاز به تأیید دارد
    }

    // درج خبر در پایگاه داده
    $sql = "INSERT INTO news (title, content, image, category, author_id, status) 
            VALUES ('$title', '$content', '$image', '$category', '$author_id', '$approved')";

    if ($conn->query($sql) === TRUE) {

        if ($_SESSION['role'] == 'admin') {
            header("Location: managerpanel.php"); // هدایت مدیر به پنل مدیریت
            exit();
        } else {
            header("Location: authorpanel.php"); // هدایت نویسنده به پنل خودش
            exit();
        }
    
    } else {
        echo "❌ خطا در ثبت خبر: " . $conn->error;
}

}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/addnews.css">
    <title>افزودن خبر</title>
</head>
<body>
    <header>
        <h2>افزودن خبر</h2>
    </header>

    <main>
        <div class="news-form-container">
            <h3>خبر جدید را وارد کنید:</h3>
            <div class="news-form">
                <form action="addnews.php" method="POST">
                    <label for="title">عنوان خبر:</label>
                    <input type="text" name="title" id="title" required>

                    <label for="content">متن خبر:</label>
                    <textarea name="content" id="content" rows="6" required></textarea>

                    <label for="image">آدرس عکس:</label>
                    <input type="text" name="image" id="image" required>

                    <label for="category">دسته بندی:</label>
                    <select name="category" id="category">
                        <option value="politics">سیاسی</option>
                        <option value="sports">ورزشی</option>
                        <option value="art">هنری</option>
                    </select>

                    <button type="submit" name="submit_news">ارسال خبر</button>
                </form>
            </div>    
        </div>
    </main>
    
</body>
</html>
