<?php
session_start();
include('db.php'); // اتصال به دیتابیس

// گرفتن اخبار سیاسی
$query_political = "SELECT * FROM news WHERE category = 'politics' and status = 'approved' ORDER BY created_at DESC LIMIT 4";
$result_political = mysqli_query($conn, $query_political);

// گرفتن اخبار ورزشی
$query_sports = "SELECT * FROM news WHERE category = 'sports' and status = 'approved' ORDER BY created_at DESC LIMIT 4";
$result_sports = mysqli_query($conn, $query_sports);

// گرفتن اخبار فرهنگی
$query_cultural = "SELECT * FROM news WHERE category = 'art' and status = 'approved' ORDER BY created_at DESC LIMIT 4";
$result_cultural = mysqli_query($conn, $query_cultural);

// تابع برای گرفتن نظرات هر خبر
function getComments($conn, $news_id) {
    $sql = "SELECT * FROM comments WHERE news_id = $news_id AND status = 'approved' ORDER BY created_at DESC";
    return mysqli_query($conn, $sql);
}
// فرض بر اینکه اتصال به دیتابیس انجام شده و $conn موجوده
function getLatestNewsByCategory($conn, $category) {
    $stmt = $conn->prepare("SELECT * FROM news WHERE category = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

$latestPolitics = getLatestNewsByCategory($conn, 'politics');
$latestSports   = getLatestNewsByCategory($conn, 'sports');
$latestCulture  = getLatestNewsByCategory($conn, 'culture');
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/css3.css">
    

    <!-- بوت‌استرپ CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <title>خبرگزاری شرق</title>
</head>
<body>
    <<style>

.slider-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(128, 0, 128, 0.2); /* هاله بنفش */
  z-index: 1;
}

.carousel-caption {
  z-index: 2;
}
.carousel-control-prev,
.carousel-control-next {
  top: 50%;
  transform: translateY(-50%);
  bottom: auto; /* اگه بوت‌استرپ مقدار bottom داده بود */
  width: 5%; /* دلخواه، برای کوچک‌تر شدن فلش‌ها */
  height: auto;
  opacity: 0.8;
}
.carousel-control-prev:hover,
.carousel-control-next:hover {
  opacity: 1; /* شفافیت بیشتر در هاور */
}

.slider-title {
  font-size: 2.2rem;
  background-color: rgba(0, 0, 0, 0.4);
  padding: 8px 100%;
  border-radius: 10px;
  display: inline-block;
  color: #fff;
  font-weight: bold;
  font-family: 'Tahoma', sans-serif;
}
.slider-img {
  object-fit:cover; /* پوشش کامل فضای موجود */
  height: 450px; /* ارتفاع ثابت برای جلوگیری از پرش */
   image-rendering: optimizeQuality; 
   filter: contrast(1.1) brightness(1.1);
    object-position: center;
     box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  position: relative;
}

    </style>
<header>
    <div class="newsagency-information">
        <div class="logo">
            <img src="img/hhh.png" alt="">
        </div>
        <div class="name">
            <p>خبرگزاری شرق</p>
        </div>
    </div>
    <div class="time-date" id="date-time">
        <p>Loading</p>
    </div>
</header>
<nav>
    <div class="categories-list">
        <a href="Index.php">خانه</a>
        <a href="archive.php?category=politics">سیاسی</a>
        <a href="archive.php?category=sports">ورزشی</a>
        <a href="archive.php?category=art">هنری</a>
    </div>
    <!-- serch -->
    <form action="search.php" method="get">
        <label for="category">دسته بندی خبر:</label>
        <select id="category" name="category">
            <option value="politics">سیاسی</option>
            <option value="art">هنری</option>
            <option value="sports">ورزشی</option>
</select>

 <label for="title">عنوان </label>
        <input type="text" id="title" name="title" placeholder="عنوان را وارد کنید">
        
        <label for="author">نویسنده:</label>
        <input type="text" id="author" name="author" placeholder="نام نویسنده را وارد کنید">

        <label for="date">تاریخ:</label>
        <input type="date" id="date" name="date">

        <button type="submit" class="m-1 search">جستجو</button>
    </form>
    <!-- end serch -->
    <div class="login-button">
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <button type="button"><a href="managerpanel.php"   >پنل مدیریت</a></button>
            <?php else: ?>
                <button type="button"><a href="authorpanel.php">پنل نویسنده</a></button>
            <?php endif; ?>
        <?php else: ?>
            <button type="button"><a href="auth.php">ثبت نام / ورود</a></button>
        <?php endif; ?>
    </div>
</nav>
<!-- اسلایدری -->
<section class="my-5">
  <div id="newsCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">

      <!-- اسلاید اول -->
      <div class="carousel-item active">
        <div class="slider-overlay"></div>
        <img src="img/اسلایدر1.jpg" class="d-block w-100 slider-img" alt="خبر 1">
        <div class="carousel-caption d-none d-md-block">
          <h5 class="slider-title ">خبرگزاری شرق</h5>
        </div>
      </div>

      <!-- اسلاید دوم -->
      <div class="carousel-item">
        <div class="slider-overlay"></div>
        <img src="img/اسلایدر3.jpg" class="d-block w-100 slider-img" alt="خبر 2">
        <div class="carousel-caption d-none d-md-block">
          <h5 class="slider-title">خبرگزاری شرق</h5>
        </div>
      </div>

      <!-- اسلاید سوم -->
      <div class="carousel-item">
        <div class="slider-overlay"></div>
        <img src="img/اسلایدر 2.jpg" class="d-block w-100 slider-img" alt="خبر 3">
        <div class="carousel-caption d-none d-md-block">
          <h5 class="slider-title">خبرگزاری شرق</h5>
        </div>
      </div>

    </div>

    <!-- کنترل‌های قبل/بعد -->
    <button class="carousel-control-prev" type="button" data-bs-target="#newsCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#newsCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon"></span>
    </button>
  </div>
</section>
<div class="news">
    <!-- اخبار سیاسی -->
    <div class="political">
        <?php while ($news = mysqli_fetch_assoc($result_political)): ?>
            <div class="newsblock">
                <div class="news-information">
                    <img src="<?php echo $news['image']; ?>" alt="خبر فوری">
                    <h3><?php echo $news['title']; ?></h3>
                    <p><?php echo substr($news['content'], 0, 100); ?>...</p>
                    <p>نویسنده: شماره <?php echo $news['author_id']; ?></p>
                    <!-- دکمه و فرم ارسال نظر برای هر کارت -->
<button class="  toggleCommentBtn btn border commentbt pb-4 m-2" type="button" data-newsid="<?php echo $news['id']; ?>">ثبت نظر جدید</button>

<form class="commentForm" action="submit_coment.php" method="post" style="display:none; margin-top:10px;">
    <input type="hidden" name="news_id" value="<?php echo $news['id']; ?>">
    <textarea name="comment" rows="3" placeholder="نظر خود را بنویسید..." required></textarea>
    <br>
    <button type="submit"  class=" toggleCommentBtn btn border commentbt pb-2 m-2">ارسال نظر</button>
</form>
                    <div class="comments-section">
    <div class="accordion" id="accordionComments<?php echo $news['id']; ?>">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingComments<?php echo $news['id']; ?>">
                <button class="accordion-button collapsed commentbt" type="button" data-bs-toggle="collapse" data-bs-target="#collapseComments<?php echo $news['id']; ?>" aria-expanded="false" aria-controls="collapseComments<?php echo $news['id']; ?>">
                    نظرات کاربران
                </button>
            </h2>
            <div id="collapseComments<?php echo $news['id']; ?>" class="accordion-collapse collapse" aria-labelledby="headingComments<?php echo $news['id']; ?>" data-bs-parent="#accordionComments<?php echo $news['id']; ?>">
                <div class="accordion-body">
                    <?php
                    $comments_result = getComments($conn, $news['id']);
                    if ($comments_result && mysqli_num_rows($comments_result) > 0) {
                        while ($comment = mysqli_fetch_assoc($comments_result)) {
                            echo "<p><strong>" . htmlspecialchars((string)$comment['user_id']) . ":</strong> " . htmlspecialchars($comment['comment']) . "</p>";
                        }
                    } else {
                        echo "<p>هیچ نظری وجود ندارد.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
                    </div>
                    <div class="show-news">
                        <button type="button"><a href="fullnews.php?id=<?php echo $news['id']; ?>">نمایش کامل خبر</a></button>
                    </div>
                </div>
            <?php endwhile; ?>
            <div class="newsblock show-all">
                <button type="button">
                    <a href="archive.php?category=politics">نمایش همه اخبار سیاسی</a>
                </button>
            </div>
        </div>
       
        <!-- اخبار ورزشی -->
    <div class="sports">
        <?php while ($news = mysqli_fetch_assoc($result_sports)): ?>
            <div class="newsblock">
                <div class="news-information">
                    <img src="<?php echo $news['image']; ?>" alt="خبر فوری">
                    <h3><?php echo $news['title']; ?></h3>
                    <p><?php echo substr($news['content'], 0, 100); ?>...</p>
                    <p>نویسنده: شماره <?php echo $news['author_id']; ?></p>
                    <!-- دکمه و فرم ارسال نظر برای هر کارت -->
<button class=" toggleCommentBtn btn border commentbt pb-4 m-2" type="button" data-newsid="<?php echo $news['id']; ?>">ثبت نظر جدید</button>

<form class="commentForm" action="submit_coment.php" method="post" style="display:none; margin-top:10px;">
    <input type="hidden" name="news_id" value="<?php echo $news['id']; ?>">
    <textarea name="comment" rows="3" placeholder="نظر خود را بنویسید..." required></textarea>
    <br>
    <button type="submit" class="toggleCommentBtn btn border commentbt pb-2 m-2">ارسال نظر</button>
</form>
                    <div class="comments-section">
    <div class="accordion" id="accordionComments<?php echo $news['id']; ?>">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingComments<?php echo $news['id']; ?>">
                <button class="accordion-button collapsed commentbt" type="button" data-bs-toggle="collapse" data-bs-target="#collapseComments<?php echo $news['id']; ?>" aria-expanded="false" aria-controls="collapseComments<?php echo $news['id']; ?>">
                    نظرات کاربران
                </button>
            </h2>
            <div id="collapseComments<?php echo $news['id']; ?>" class="accordion-collapse collapse" aria-labelledby="headingComments<?php echo $news['id']; ?>" data-bs-parent="#accordionComments<?php echo $news['id']; ?>">
                <div class="accordion-body">
                    <?php
                    $comments_result = getComments($conn, $news['id']);
                    if ($comments_result && mysqli_num_rows($comments_result) > 0) {
                        while ($comment = mysqli_fetch_assoc($comments_result)) {
                            echo "<p><strong>" . htmlspecialchars((string)$comment['user_id']) . ":</strong> " . htmlspecialchars($comment['comment']) . "</p>";
                        }
                    } else {
                        echo "<p>هیچ نظری وجود ندارد.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
                </div>
                <div class="show-news">
                    <button type="button"><a href="fullnews.php?id=<?php echo $news['id']; ?>">نمایش کامل خبر</a></button>
                </div>
            </div>
        <?php endwhile; ?>
        <div class="newsblock show-all">
            <button type="button">
                <a href="archive.php?category=sports">نمایش همه اخبار ورزشی</a>
            </button>
        </div>
    </div>

    <!-- اخبار فرهنگی -->
    <div class="cultural">
        <?php while ($news = mysqli_fetch_assoc($result_cultural)): ?>
            <div class="newsblock">
                <div class="news-information">
                    <img src="<?php echo $news['image']; ?>" alt="خبر فوری">
                    <h3><?php echo $news['title']; ?></h3>
                    <p><?php echo substr($news['content'], 0, 100); ?>...</p>
                    <p>نویسنده: شماره <?php echo $news["author_id"]; ?></p>
                    <!-- دکمه و فرم ارسال نظر برای هر کارت -->
<button class=" toggleCommentBtn btn border commentbt pb-4 m-2" type="button" data-newsid="<?php echo $news['id']; ?>">ثبت نظر جدید</button>

<form class="commentForm" action="submit_coment.php" method="post" style="display:none; margin-top:10px;">
    <input type="hidden" name="news_id" value="<?php echo $news['id']; ?>">
    <textarea name="comment" rows="3" placeholder="نظر خود را بنویسید..." required></textarea>
    <br>
    <button type="submit" class="toggleCommentBtn btn border commentbt pb-2 m-2">ارسال نظر</button>
</form>

                    <div class="comments-section">
    <div class="accordion" id="accordionComments<?php echo $news['id']; ?>">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingComments<?php echo $news['id']; ?>">
                <button class="accordion-button collapsed commentbt" type="button" data-bs-toggle="collapse" data-bs-target="#collapseComments<?php echo $news['id']; ?>" aria-expanded="false" aria-controls="collapseComments<?php echo $news['id']; ?>">
                    نظرات کاربران
                </button>
            </h2>
            <div id="collapseComments<?php echo $news['id']; ?>" class="accordion-collapse collapse" aria-labelledby="headingComments<?php echo $news['id']; ?>" data-bs-parent="#accordionComments<?php echo $news['id']; ?>">
                <div class="accordion-body">
                    <?php
                    $comments_result = getComments($conn, $news['id']);
                    if ($comments_result && mysqli_num_rows($comments_result) > 0) {
                        while ($comment = mysqli_fetch_assoc($comments_result)) {
                            echo "<p><strong>" . htmlspecialchars((string)$comment['user_id']) . ":</strong> " . htmlspecialchars($comment['comment']) . "</p>";
                        }
                    } else {
                        echo "<p>هیچ نظری وجود ندارد.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
                </div>
                <div class="show-news">
                    <button type="button"><a href="fullnews.php?id=<?php echo $news['id']; ?>">نمایش کامل خبر</a></button>
                </div>
            </div>
        <?php endwhile; ?>
        <div class="newsblock show-all">
            <button type="button">
                <a href="archive.php?category=art">نمایش همه اخبار هنری</a>
            </button>
        </div>
    </div>
</div>
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
        btn.addEventListener('click', function() {
            const form = this.nextElementSibling;
            form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
        });
    });
    function updateClock() {
        var currentDate = new Date(); // دریافت تاریخ و زمان فعلی میلادی
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