<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "catagorize"; 

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$sql = "SELECT * FROM bang1 "; 
$res =$conn-> query( $sql);
echo '<form  method ="GET" action="search1.php">';
echo '<select name="genre">';
if($res && $res->num_rows>0){
    while($row=$res->fetch_assoc()){
        echo $row['loaiphim'];
    }
}
else{
    echo "không tìm thấy";
}
?>
