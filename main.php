
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "catagorize";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, title FROM movies";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    
    while($row = $result->fetch_assoc()) {
        echo "<a href='movie.php?id=" . $row["id"] . "'>" . $row["title"] . "</a><br>";
    }
} else {
    echo "No movies found.";
}
$conn->close();
?>
