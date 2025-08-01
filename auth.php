<?php
session_start();
include ('db.php');

$message = ""; // برای نمایش پیام به کاربر

// بررسی نوع درخواست (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // بررسی اینکه آیا کاربر در حال ورود است
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);

        // بررسی وجود کاربر در دیتابیس
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            // بررسی صحت رمز عبور
            if (password_verify($password, $user["password"])) {
                if ($user["approved"] == "pending") {
                    $message = "حساب شما هنوز تأیید نشده است.";
                } elseif ($user["approved"] == "rejected") {
                    $message = "حساب شما رد شده است.";
                } else {
                    // تنظیم اطلاعات کاربر در سشن
                    $_SESSION["user_id"] = $user["id"];
                    $_SESSION["username"] = $user["username"];
                    $_SESSION["role"] = $user["role"];

                    // هدایت به پنل مناسب
                    if ($user["role"] == "admin") {
                        header("Location: ./managerpanel.php");
                    } else {
                        header("Location:./authorpanel.php");
                    }
                    exit();
                }
            } else {
                $message = "نام کاربری یا رمز عبور اشتباه است.";
            }
        } else {
            $message = "نام کاربری یا رمز عبور اشتباه است.";
        }
    }

    // بررسی اینکه آیا کاربر در حال ثبت‌نام است
    elseif (isset($_POST['fullname']) && isset($_POST['register-password'])) {
        $fullname = trim($_POST["fullname"]);
        $email = trim($_POST["email"]);
        $password = trim($_POST["register-password"]);
        $confirmPassword = trim($_POST["confirm-password"]);

        // بررسی اینکه رمز عبور با تایید رمز عبور مطابقت داشته باشد
        if ($password !== $confirmPassword) {
            $message = "رمز عبور و تایید آن مطابقت ندارد.";
        } else {
            // بررسی اینکه ایمیل یا نام کاربری قبلاً ثبت نشده باشد
            $checkQuery = "SELECT * FROM users WHERE email = ? OR username = ?";
            $stmt = $conn->prepare($checkQuery);
            $stmt->bind_param("ss", $email, $fullname);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $message = "نام کاربری یا ایمیل قبلاً استفاده شده است.";
            } else {
                // هش کردن رمز عبور
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // درج کاربر جدید در دیتابیس
                $insertQuery = "INSERT INTO users (username, email, password, role, approved) VALUES (?, ?, ?, 'writer', 'pending')";
                $stmt = $conn->prepare($insertQuery);
                $stmt->bind_param("sss", $fullname, $email, $hashedPassword);

                if ($stmt->execute()) {
                    $message = "ثبت‌نام با موفقیت انجام شد. لطفاً منتظر تأیید مدیر باشید.";
                } else {
                    $message = "خطایی رخ داد.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login-registerstyle.css">
    <title>ورود / ثبت‌نام</title>
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
            <a href="Index.php">خانه</a>
            <a href="archive.php?category=politics">سیاسی</a>
            <a href="archive.php?category=sports">ورزشی</a>
            <a href="archive.php?category=art">هنری</a>
        </div>
    </nav>   
    <div class="login-register-container">
        <!-- بخش ورود -->
        <div class="login-form">
            <h2 class="form-title">ورود</h2>
            <form action="auth.php" method="POST">
                <label for="username">نام کاربری</label>
                <input type="text" id="username" name="username" required>

                <label for="password">رمز عبور</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">ورود</button>
            </form>
            <p class="guide">حساب کاربری ندارید؟ <a class="link-guide" href="#" id="show-register-form">ثبت‌نام کنید</a></p>
            <?php if ($message) echo "<p class='message'>$message</p>"; ?>
        </div>

        <!-- بخش ثبت‌نام -->
        <div class="register-form" style="display: none;">
            <h2 class="form-title">ثبت‌نام</h2>
            <form action="auth.php" method="POST">
                <label for="fullname">نام کامل</label>
                <input type="text" id="fullname" name="fullname" required>

                <label for="email">ایمیل</label>
                <input type="email" id="email" name="email" required>

                <label for="register-password">رمز عبور</label>
                <input type="password" id="register-password" name="register-password" required>

                <label for="confirm-password">تایید رمز عبور</label>
                <input type="password" id="confirm-password" name="confirm-password" required>

                <button type="submit">ثبت‌نام</button>
            </form>
            <p class="guide">حساب کاربری دارید؟ <a class="link-guide" href="#" id="show-login-form">وارد شوید </a></p>
            <?php if ($message) echo "<p class='message'>$message</p>"; ?>
        </div>
    </div>

    <script>
        const loginForm = document.querySelector('.login-form');
        const registerForm = document.querySelector('.register-form');
        const showRegisterFormButton = document.getElementById('show-register-form');
        const showLoginFormButton = document.getElementById('show-login-form');

        showRegisterFormButton.addEventListener('click', () => {
            loginForm.style.display = 'none';
            registerForm.style.display = 'block';
        });

        showLoginFormButton.addEventListener('click', () => {
            registerForm.style.display = 'none';
            loginForm.style.display = 'block';
        });

        function updateClock() {
            var currentDate = new Date(); 
            var hours = currentDate.getHours().toString().padStart(2, '0');
            var minutes = currentDate.getMinutes().toString().padStart(2, '0');
            var seconds = currentDate.getSeconds().toString().padStart(2, '0');
            var timeString = hours + ':' + minutes + ':' + seconds;
            document.getElementById('date-time').innerHTML = timeString;
        }

        setInterval(updateClock, 1000);
    </script>
</body>
</html>
