<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = htmlspecialchars($_POST["fullname"]);
    $gender = htmlspecialchars($_POST["gender"]);
    $dob = htmlspecialchars($_POST["dob"]);
    $country = htmlspecialchars($_POST["country"]);
    $postal = htmlspecialchars($_POST["postal"]);
    $email = htmlspecialchars($_POST["email"]);
    $password = htmlspecialchars($_POST["password"]);
    $repassword = htmlspecialchars($_POST["repassword"]);
    $alt_email = htmlspecialchars($_POST["alt_email"]);
    $security_question = htmlspecialchars($_POST["security_question"]);
    $security_answer = htmlspecialchars($_POST["security_answer"]);

    // Hiển thị dữ liệu đã nhận được
    echo "<h2>Thông Tin Đăng Ký</h2>";
    echo "Tên: " . $fullname . "<br>";
    echo "Giới Tính: " . $gender . "<br>";
    echo "Ngày Tháng Năm Sinh: " . $dob . "<br>";
    echo "Tôi Sống Tại: " . $country . "<br>";
    echo "Mã Bưu Chính: " . $postal . "<br>";
    echo "Yahoo! ID và Email: " . $email . "<br>";
    echo "Mật Khẩu: " . $password . "<br>";
    echo "Email Thay Thế Khác: " . $alt_email . "<br>";
    echo "Câu Hỏi Bảo Mật: " . $security_question . "<br>";
    echo "Câu Trả Lời Của Bạn: " . $security_answer . "<br>";

    // Lưu trữ dữ liệu vào tệp (ví dụ, data.txt)
    $file = fopen("data.txt", "a") or die("Unable to open file!");
    $txt = "Tên: " . $fullname . " | Giới Tính: " . $gender . " | Ngày Sinh: " . $dob . " | Quốc Gia: " . $country . " | Mã Bưu Chính: " . $postal . " | Email: " . $email . " | Mật Khẩu: " . $password . " | Email Thay Thế: " . $alt_email . " | Câu Hỏi Bảo Mật: " . $security_question . " | Câu Trả Lời: " . $security_answer . "\n";
    fwrite($file, $txt);
    fclose($file);

    echo "<p>Dữ liệu đã được lưu trữ thành công.</p>";
} else {
    echo "Invalid Request Method";
}
?>
