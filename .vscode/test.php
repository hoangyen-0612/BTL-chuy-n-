<?php
$servername="localhost";
$username="root";
$password="";
$dbname="catagorize";
$con=mysqli_connect($servername,$username,$password,$dbname);
    if($con->connect_errno){
      die("kết nối thất bại" .$con->connect_error);
    }
 $res=$con->query($sql);
 if($res->num_rows>0){
    while($row=$res->fetch_assoc()){
        echo "".$row["loaiphim"]."<br>";
    }
    
 }
 else{
    echo "không có dữ liệu ";
}
$con->close();
$st->close();
 ?>
