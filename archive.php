<?php
session_start();
include('db.php');

// دریافت دسته‌بندی از URL
$category = isset($_GET['category']) ? $_GET['category'] : '';

$sql = "SELECT * FROM news  ";
if (!empty($category)) {
    $sql .= " WHERE category='$category' and status = 'approved'";
}
$sql .= " ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/archievestyle.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <title>نمایش تمام اخبار</title>
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
    <main>
        <h2 class="page-title"> اخبار 
        <?php 
            if ($category == 'politics') {
                echo "  سیاسی";
            } elseif ($category == 'sports') {
                echo "  ورزشی";
            } elseif ($category == 'art') {
                echo "  هنری";
            } else {
                echo $category ? " - " . ucfirst($category) : '';
            }
        ?>
        </h2>
        <div class="all-news">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="news-block">';
                    echo '<img src="' . $row["image"] . '" alt="خبر">';
                    echo '<div class="news-info">';
                    echo '<h3>' . $row["title"] . '</h3>';
                    echo '<p>' . substr($row["content"], 0, 100) . '...</p>';
                    echo '<p><strong>نویسنده: شماره</strong> ' . $row["author_id"] . '</p>';
                    echo '<a href="fullnews.php?id=' . $row["id"] . '"><button>نمایش کامل</button></a>';
                
                    // دکمه فرم ارسال نظر
                    echo '<button class="toggleCommentBtn btn border-primary mx-2" type="button">ثبت نظر جدید</button>';
                    echo '<form class="commentForm" action="submit_coment.php" method="post" style="display:none; margin-top:10px;">';
                    echo '<input type="hidden" name="news_id" value="' . $row["id"] . '">';
                    echo '<textarea name="comment" rows="3" placeholder="نظر خود را بنویسید..." required></textarea><br>';
                    echo '<button type="submit" class="btn ">ارسال نظر</button>';
                    echo '</form>';
                
                    // دکمه نمایش نظرات
                    echo '<button class="toggleCommentsList btn btn-secondary" type="button">مشاهده نظرات کاربران</button>';
                    
                    // باکس لیست نظرات (کشویی و پنهان)
                    echo '<div class="commentsList" style="display:none; background:#f9f9f9; padding:10px; margin-top:10px;">';
                
                    // دریافت نظرات تأییدشده از دیتابیس
                    $news_id = $row["id"];
                    $comments_query = "SELECT * FROM comments WHERE news_id = $news_id AND status = 'approved' ORDER BY created_at DESC";
                    $comments_result = mysqli_query($conn, $comments_query);
                
                    if (mysqli_num_rows($comments_result) > 0) {
                        while ($comment = mysqli_fetch_assoc($comments_result)) {
                            echo '<div class="comment-item" style="border-bottom:1px solid #ccc; margin-bottom:10px; padding-bottom:5px;">';
                            echo '<p>' . htmlspecialchars($comment['comment']) . '</p>';
                            echo '<small style="color:gray;">' . $comment['created_at'] . '</small>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>هنوز نظری ثبت نشده است.</p>';
                    }
                
                    echo '</div>'; // پایان commentsList
                
                    echo '</div>'; // پایان news-info
                    echo '</div>'; // پایان news-block
                }
            } else {
                echo '<p>هیچ خبری در این دسته یافت نشد.</p>';
            }
            ?>
            
        </div>
        
    </main>
    <footer>
        
        <div class="about-us">
            <h3>خبرگزاری  شرق</h3>
            <p>خبرگزاری  شرق با هدف اطلاع‌رسانی سریع، دقیق و بی‌طرفانه در زمینه‌های مختلف سیاسی، اجتماعی، فرهنگی و اقتصادی در کشور عزیزمان فعالیت می‌کند. تیم ما متشکل از خبرنگاران حرفه‌ای و متخصص است که با به‌روزترین منابع اطلاعاتی، تلاش دارند اخبار را به شکلی شفاف و معتبر به شما عزیزان ارائه دهند. ما به دنبال حفظ اعتماد شما و گسترش فضای آگاهی در جامعه هستیم.</p>
        </div>
        <div class="contact-us">
            <h3>ارتباط با ما</h3>
            <p>تلفن تماس:</p>
            <p>09900000000</p>
            <p>ایمیل:</p>
            <p>sharghnews@examole.com</p>
            <p>آدرس:</p>
            <p>ایران - تهران -میدان انقلاب</p>
        </div>
        <div class="photo">
            <img src="img/rb_66059.png" alt="news agency">
        </div>
    </footer>

    <script>
          document.querySelectorAll('.toggleCommentBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const form = this.nextElementSibling;
            form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
        });
    });

    document.querySelectorAll('.toggleCommentsList').forEach(btn => {
        btn.addEventListener('click', function () {
            const commentsBox = this.nextElementSibling;
            commentsBox.style.display = (commentsBox.style.display === 'none' || commentsBox.style.display === '') ? 'block' : 'none';
        });
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

<?php $conn->close(); ?>
