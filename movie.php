<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "catagorize";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $movie_id = $_GET['id'];
    $sql = "SELECT title, genre, director, release_year, rating FROM movies WHERE id='$movie_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "Title: " . $row["title"]. "<br>";
            echo "Genre: " . $row["genre"]. "<br>";
            echo "Director: " . $row["director"]. "<br>";
            echo "Year: " . $row["release_year"]. "<br>";
            echo "Rating: " . $row["rating"]. "<br>";
        }
    } else {
        echo "No details found for this movie.";
    }
} else {
    echo "No movie ID provided.";
}
$conn->close();
?>
