<?php
include ('db.php');
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit();
}

// تایید کاربر
if (isset($_POST['user_id'])) {
    $userId = (int) $_POST['user_id'];
    $conn->query("UPDATE users SET approved = 'approved' WHERE id = $userId");
}

// حذف کاربر
if (isset($_POST['delete_user'])) {
    $userId = (int) $_POST['delete_user'];
    $conn->query("DELETE FROM users WHERE id = $userId");
}

// تغییر نقش کاربر
if (isset($_POST['change_role']) && isset($_POST['new_role'])) {
    $userId = (int) $_POST['change_role'];
    $newRole = $conn->real_escape_string($_POST['new_role']);
    $conn->query("UPDATE users SET role = '$newRole' WHERE id = $userId");
}

// تایید خبر
if (isset($_POST['approve_news'])) {
    $newsId = (int) $_POST['approve_news'];
    $conn->query("UPDATE news SET status = 'approved' WHERE id = $newsId");
}

// حذف خبر
if (isset($_POST['delete_news'])) {
    $newsId = (int) $_POST['delete_news'];
    $conn->query("DELETE FROM news WHERE id = $newsId");
}

?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>پنل مدیریت</title>
    <link rel="stylesheet" href="css/managerpanelstyle.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head>
<body>

<header style="background-color: #470962; color: white;" class="p-3 d-flex justify-content-between align-items-center">

    <div class="d-flex align-items-center">
        <img src="img/hhh.png" alt="لوگو" width="50" class="me-2">
        <h5 class="mb-0">پنل مدیریت خبرگزاری شرق</h5>
    </div>
    <div>
        <a href="index.php" class="btn btn-secondary">خانه</a>
        <button class="btn btn-danger" onclick="confirmLogout()">خروج</button>
    </div>
</header>

<div class="container mt-4">
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item"><a class="font nav-link active" data-bs-toggle="tab" href="#users">مدیریت کاربران</a></li>
        <li class="nav-item"><a class="font nav-link" data-bs-toggle="tab" href="#pending-users">کاربران تایید نشده</a></li>
        <li class="nav-item"><a class="font nav-link" data-bs-toggle="tab" href="#news">مدیریت اخبار</a></li>
        <li class="nav-item"><a class="font nav-link" data-bs-toggle="tab" href="#comments">مدیریت نظرات</a></li>
    </ul>

    <div class="tab-content">

        <!-- کاربران تایید شده -->
        <div class="tab-pane fade show active" id="users">
            <h5>لیست کاربران</h5>
            <table class="table table-striped">
                <thead><tr><th>نام کاربری</th><th>ایمیل</th><th>نقش</th><th>عملیات</th></tr></thead>
                <tbody>
                <?php
                $result = $conn->query("SELECT * FROM users WHERE approved = 'approved'");
                while($user = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <form method="post" class="d-flex gap-2">
                            <input type="hidden" name="change_role" value="<?= (int)$user['id'] ?>">
                            <select name="new_role" class="form-select form-select-sm">
                                <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>کاربر</option>
                                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>ادمین</option>
                            </select>
                            <button class="btn btn-sm toggleCommentBtn btn border commentbt">تغییر نقش</button>
                        </form>
                    </td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="delete_user" value="<?= (int)$user['id'] ?>">
                            <button class="btn btn-danger btn-sm" onclick="return confirm('حذف شود؟')">حذف</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- کاربران تایید نشده -->
        <div class="tab-pane fade" id="pending-users">
            <h5>کاربران در انتظار تایید</h5>
            <table class="table table-bordered">
                <thead><tr><th>نام کاربری</th><th>ایمیل</th><th>عملیات</th></tr></thead>
                <tbody>
                <?php
                $pending = $conn->query("SELECT * FROM users WHERE approved = 'pending'");
                while($user = $pending->fetch_assoc()):
                ?>
                <tr>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
                            <button class="btn btn-success btn-sm">تایید</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- مدیریت اخبار -->
        <div class="tab-pane fade" id="news">
            <h5>مدیریت اخبار</h5>

            <div class="mb-3">
                <a href="addnews.php" class="toggleCommentBtn btn border commentbt">افزودن خبر جدید</a>
            </div>

            <table class="table table-hover">
                <thead><tr><th>عنوان</th><th>محتوا</th><th>وضعیت</th><th>عملیات</th></tr></thead>
                <tbody>
                <?php
                $news = $conn->query("SELECT * FROM news ORDER BY id DESC");
                while($n = $news->fetch_assoc()):
                ?>
                <tr>
                    <td><?= htmlspecialchars($n['title']) ?></td>
                    <td><?= htmlspecialchars(mb_strimwidth($n['content'], 0, 50, "...")) ?></td>
                    <td><?= htmlspecialchars($n['status']) ?></td>
                    <td class="d-flex gap-1">
                        <?php if($n['status'] == 'pending'): ?>
                        <form method="post">
                            <input type="hidden" name="approve_news" value="<?= (int)$n['id'] ?>">
                            <button class="btn btn-success btn-sm">تایید</button>
                        </form>
                        <?php endif; ?>
                        <form method="post">
                            <input type="hidden" name="delete_news" value="<?= (int)$n['id'] ?>">
                            <button class="btn btn-danger btn-sm" onclick="return confirm('حذف شود؟')">حذف</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- مدیریت نظرات -->
        <div class="tab-pane fade" id="comments">
            <div class="text-center pt-3">
            <a href="manage_comments.php" class="btn btn-lg btn-purple">ورود به پنل مدیریت نظرات</a>
             
            <style>
  .btn-purple {
    background-color: #470962;
    color: white;
    border: none;
  }

  .btn-purple:hover {
    background-color: #5c0c7a;
    color: white;
  }
</style>

     
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function confirmLogout() {
        if (confirm("آیا مطمئن هستید که می‌خواهید خارج شوید؟")) {
            window.location.href = "logout.php";
        }
    }
</script>
</body>
</html>
