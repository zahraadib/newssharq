<?php
session_start();
include('db.php'); // اتصال به دیتابیس

// بررسی وجود شناسه خبر در URL
if (isset($_GET['id'])) {
    $news_id = $_GET['id'];

    // دریافت اطلاعات خبر
    $query = "SELECT * FROM news WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $news_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // بررسی اینکه خبر وجود دارد یا نه
    if ($news = mysqli_fetch_assoc($result)) {
        $title = $news['title'];
        $content = $news['content'];
        $author = $news['author_id'];
        $date = $news['created_at'];
        $image = $news['image'];
        $category = $news['category'];
    } else {
        echo "<h2>خبر یافت نشد!</h2>";
        exit();
    }
} else {
    echo "<h2>شناسه خبر مشخص نشده است!</h2>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/fullnewsstyle.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <title><?php echo $title; ?></title>
</head>
<body>
    <header>
        <div class="newsagency-information">
            <div class="logo">
                <img src="img/hhh.png" alt="">
            </div>
            <div class="name">
                <p>خبرگزاری  شرق</p>
            </div>
        </div>
        <div class="time-date" id="date-time">
            <p>Loading</p>
        </div>
    </header>
    <nav>
        <div class="categories-list">
            <a href="index.php">خانه</a>
            <a href="archive.php?category=politics">سیاسی</a>
            <a href="archive.php?category=sports">ورزشی</a>
            <a href="archive.php?category=art">هنری</a>
        </div>
        <div class="login-button">
            <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <button type="button"><a href="managerpanel.php">پنل مدیریت</a></button>
            <?php else: ?>
                <button type="button"><a href="authorpanel.php">پنل نویسنده</a></button>
            <?php endif; ?>
            <?php else: ?>
            <button type="button"><a href="auth.php">ثبت نام / ورود</a></button>
            <?php endif; ?>
        </div>
    </nav>

    <!-- صفحه نمایش خبر -->
    <main class="full-news">
        <div class="news-details">
            <img src="<?php echo $image; ?>" alt="خبر">
            <h2><?php echo $title; ?></h2>
            <p><?php echo nl2br($content); ?></p>
            <p><strong>نویسنده: شماره</strong> <?php echo $author; ?></p>
            <p><strong>تاریخ:</strong> <?php echo $date; ?></p>
            <p><strong>دسته‌بندی:</strong>
                <?php 
                    if ($category == 'politics') {
                        echo "سیاسی";
                    } elseif ($category == 'sports') {
                        echo "ورزشی";
                    } elseif ($category == 'art') {
                        echo "هنری";
                    } else {
                        echo ucfirst($category); 
                    }
                ?>
            </p>
        </div>
        <!-- دکمه برای باز کردن نظرات -->
<button onclick="toggleComments()" class="  toggleCommentBtn btn border commentbt pb-2 m-2">نمایش نظرات کاربران</button>

<!-- باکس نظرات (پنهان در ابتدا) -->
<div id="comments-box" style="display:none; margin-top: 20px; border-top: 1px solid #ccc; padding-top: 10px;">
    <h3>نظرات کاربران</h3>
    <?php
    $commentsQuery = "SELECT * FROM comments WHERE news_id = $news_id AND status = 'approved'";
    $commentsResult = mysqli_query($conn, $commentsQuery);

    if (mysqli_num_rows($commentsResult) > 0) {
        while ($comment = mysqli_fetch_assoc($commentsResult)) {
            echo "<div class='comment-item'>";
            echo "<p>" . htmlspecialchars($comment['comment']) . "</p>";
            echo "<hr>";
            echo "</div>";
        }
    } else {
        echo "<p>نظری برای این خبر ثبت نشده است.</p>";
    }
    ?>
</div>

<!-- دکمه نمایش فرم ارسال نظر -->


<!-- فرم ارسال نظر (پنهان در ابتدا) -->
<div id="comment-form-box" style="display:none; margin-top: 15px;">
    <form action="submit_comment.php" method="POST">
        <input type="hidden" name="news_id" value="<?php echo $news_id; ?>">
        <textarea name="comment" rows="4" placeholder="نظر خود را بنویسید..." required></textarea>
        <br>
        <button type="submit">ارسال</button>
    </form>
</div>
        <!-- دکمه ثبت نظر جدید -->
<button id="toggleCommentFormBtn" class="  toggleCommentBtn btn border commentbt pb-2 m-2" type="button">ثبت نظر جدید</button>

<!-- فرم ارسال نظر (اول مخفی) -->
<form id="commentForm" action="submit_coment.php" method="post" style="display:none; margin-top:10px;">
    <input type="hidden" name="news_id" value="<?php echo $news_id; ?>">
    <textarea name="comment" rows="4" placeholder="نظر خود را بنویسید..." required></textarea>
    <br>
    <button type="submit"  class="  toggleCommentBtn btn border commentbt pb-2 m-2">ارسال نظر</button>
</form>

    </main>

    <script>
        function toggleComments() {
        var box = document.getElementById("comments-box");
        box.style.display = (box.style.display === "none") ? "block" : "none";
    }

    function toggleForm() {
        var form = document.getElementById("comment-form-box");
        form.style.display = (form.style.display === "none") ? "block" : "none";
    }
         document.getElementById('toggleCommentFormBtn').addEventListener('click', function() {
        const form = document.getElementById('commentForm');
        if(form.style.display === 'none') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    });
        function updateClock() {
            var currentDate = new Date(); 
            var hours = currentDate.getHours().toString().padStart(2, '0'); // ساعت
            var minutes = currentDate.getMinutes().toString().padStart(2, '0'); // دقیقه
            var seconds = currentDate.getSeconds().toString().padStart(2, '0'); // ثانیه

            var timeString = hours + ':' + minutes + ':' + seconds; // فرمت ساعت

            // نمایش ساعت در صفحه
            document.getElementById('date-time').innerHTML = timeString;
        }

        setInterval(updateClock, 1000);

        
    </script>

</body>
</html>
