
    <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $fullname = htmlspecialchars($_POST["fullname"]);
            $email = htmlspecialchars($_POST["email"]);
            $password = htmlspecialchars($_POST["password"]);
            $birthday = htmlspecialchars($_POST["birthday"]);

            echo "<p>Họ và tên: " . $fullname . "</p>";
            echo "<p>Email: " . $email . "</p>";
            echo "<p>Mật khẩu: " . $password . "</p>";
            echo "<p>Ngày sinh: " . $birthday . "</p>";
        } else {
            echo "<p>Không có thông tin đăng ký.</p>";
        }
    ?>

