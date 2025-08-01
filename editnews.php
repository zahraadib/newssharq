<?php
include 'db.php'; // اتصال به پایگاه داده

// بررسی دریافت شناسه خبر از URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $news_id = $_GET['id'];

    // گرفتن اطلاعات خبر از پایگاه داده
    $sql = "SELECT * FROM news WHERE id = $news_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $title = $row['title'];
        $content = $row['content'];
        $image = $row['image'];
        $category = $row['category'];
    } else {
        // اگر خبری با این شناسه وجود نداشت
        echo "خبر مورد نظر یافت نشد.";
        header("Location: managerpanel.php"); // هدایت به صفحه پنل مدیر در صورت عدم وجود خبر
        exit();
    }
} else {
    // اگر شناسه خبر در URL موجود نباشد
    echo "شناسه خبر در دسترس نیست.";
    header("Location: managerpanel.php"); // هدایت به صفحه پنل مدیر در صورت نبود شناسه
    exit();
}

if (isset($_POST['edit_news'])) {
    // دریافت مقادیر ویرایش شده از فرم
    $new_title = $_POST['title'];
    $new_content = $_POST['content'];
    $new_image = $_POST['image'];
    $new_category = $_POST['category'];

    // به‌روزرسانی خبر در پایگاه داده
    $update_sql = "UPDATE news SET title='$new_title', content='$new_content', image='$new_image', category='$new_category' WHERE id=$news_id";
    if ($conn->query($update_sql) === TRUE) {
        echo "خبر با موفقیت ویرایش شد.";
        
        // بررسی نقش کاربر برای هدایت به صفحه مناسب
     session_start();
        if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
            header("Location: managerpanel.php"); // اگر مدیر است، به پنل مدیر برود
        } else {
            header("Location: authorpanel.php"); // اگر نویسنده است، به پنل نویسنده برود
        }
        
        exit();
    } else {
        echo "خطا در ویرایش خبر: " . $conn->error;
    }


}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/addnews.css">
    <title>ویرایش خبر</title>
</head>
<body>
    <header>
        <h2>ویرایش خبر</h2>
    </header>

    <main>
        <div class="news-form-container">
            <h3>جزئیات خبر:</h3>
            <div class="news-form">
                <form action="editnews.php?id=<?php echo $news_id; ?>" method="POST">
                    <label for="title">عنوان خبر:</label>
                    <input type="text" name="title" id="title" required value="<?php echo htmlspecialchars($title); ?>">

                    <label for="content">متن خبر:</label>
                    <textarea name="content" id="content" rows="6" required><?php echo htmlspecialchars($content); ?></textarea>

                    <label for="image">آدرس عکس:</label>
                    <input type="text" name="image" id="image" required value="<?php echo htmlspecialchars($image); ?>">

                    <label for="category">دسته‌بندی:</label>
                    <select name="category" id="category">
                        <option value="politics" <?php echo ($category == 'politics') ? 'selected' : ''; ?>>سیاسی</option>
                        <option value="sports" <?php echo ($category == 'sports') ? 'selected' : ''; ?>>ورزشی</option>
                        <option value="art" <?php echo ($category == 'art') ? 'selected' : ''; ?>>هنری</option>
                    </select>

                    <button type="submit" name="edit_news"  >ویرایش خبر</button>

                </form>
            </div>    
        </div>
    </main>
</body>
</html>
