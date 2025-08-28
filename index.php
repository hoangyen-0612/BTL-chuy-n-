<?php
include('config.php');
if((isset($_GET['timkiem']))){
    $khoa=$_GET['texttimkiem'];

    $sql_timkiem="SELECT * FROM find WHERE tenphim LIKE '%".$khoa."%'";
    $query_timkiem=mysqli_query($conn,$sql_timkiem);
    if (!$query_timkiem)
     { die("Lỗi truy vấn: " . mysqli_error($conn)); } } 
    else { 
        if(mysqli_num_rows($query_timkiem)>0){
            while($rowtimkiem=mysqli_fetch_assoc($query_timkiem)){
                echo "<div>";
                echo htmlspecialchars($rowtimkiem['tenphim']);
                echo htmlspecialchars( $rowtimkiem['url']);
                echo htmlspecialchars($rowtimkiem['ten']);
                echo "</div>";
            }
        }
        else{
            echo ' không thấy kết quả nào';
        }


}
?>
