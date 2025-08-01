<?php
include('db.php');

// گرفتن فیلترها از فرم
$category = isset($_GET['category']) ? $_GET['category'] : '';
$author = isset($_GET['author']) ? $_GET['author'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';
$title = isset($_GET['title']) ? $_GET['title'] : ''; // گرفتن فیلد عنوان

// ساختن کوئری پایه
$sql = "SELECT * FROM news WHERE status = 'approved'";
$conditions = [];

// اگر دسته‌بندی انتخاب شده بود
if (!empty($category)) {
    $conditions[] = "category = '$category'";
}

// اگر شماره نویسنده وارد شده بود
if (!empty($author)) {
    $conditions[] = "author_id = '$author'";
}

// اگر تاریخ وارد شده بود
if (!empty($date)) {
    $conditions[] = "DATE(created_at) = '$date'";
}

// اگر عنوان وارد شده بود (جستجو با LIKE)
if (!empty($title)) {
    $conditions[] = "title LIKE '%$title%'";
}

// اضافه کردن شرایط به کوئری
if (!empty($conditions)) {
    $sql .= " AND " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/search.css">
    <title >نتایج جستجو</title>
</head>
<body>
    <h2 class="natayej">نتایج جستجو:</h2>
    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div style="border: 1px solid purple; margin: 10px; padding: 10px;">
                <img src="<?php echo $row['image']; ?>" style="width:200px;"><br>
                <strong>عنوان:</strong> <?php echo $row['title']; ?><br>
                <strong>تاریخ:</strong> <?php echo $row['created_at']; ?><br>
                <strong>نویسنده:</strong> <?php echo $row['author_id']; ?><br>
                <strong>دسته‌بندی:</strong> <?php echo $row['category']; ?><br>
                <p><?php echo mb_substr($row['content'], 0, 100); ?>...</p>
                <a href="fullnews.php?id=<?php echo $row['id']; ?>" class="show">نمایش کامل</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>هیچ خبری مطابق با جستجو یافت نشد.</p>
    <?php endif; ?>
</body>
</html>