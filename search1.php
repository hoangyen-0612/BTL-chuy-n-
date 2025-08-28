<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "catagorize";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Nhận thể loại từ form tìm kiếm
$genre = $_GET['genre'];

// Tìm kiếm phim theo thể loại
$sql = "SELECT *
        FROM bang1
        WHERE loaiphim.name = '$genre'
        ";
$result = $conn->query($sql);

// Hiển thị kết quả tìm kiếm
if ($result->num_rows > 0) {
    echo "<h2>Phim thuộc thể loại " . $genre . ":</h2>";
    while($row = $result->fetch_assoc()) {
        echo "<div>";
        echo "<h3>" . $row['loaiphim'] . "</h3>";
        echo "</div>";
    }
} else {
    echo "Không tìm thấy phim nào thuộc thể loại " . $genre . ".";
}

$conn->close();
?>
