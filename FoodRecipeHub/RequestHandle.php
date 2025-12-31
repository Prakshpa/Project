<?php
include "Connection.php";
$food=$cat='';
$i=0;
$recipes=[];
if($_SERVER['REQUEST_METHOD']=="GET"){
    if(isset($_GET['rsn'])){
        $sn=$_GET['rsn'];
        $query=$conn->query("DELETE FROM Recipes WHERE `id`=$sn");
        if(!$query) echo mysqli_error($conn);
        else echo "Successful";
    }elseif(isset($_GET['view'])){
        $sql="SELECT `id`, `Food name`, `Help files`, Categories, price, visits FROM `Recipes` ORDER BY `id` DESC";
        $result=mysqli_query($conn, $sql);
        if(!$result) echo mysqli_error($conn);
        else {
            while($row=$result->fetch_assoc()){
                $files=explode(",,", $row['Help files']);
                $files=preg_grep("/[A-Za-z0-9\D]{4,}/", $files);
                $files=implode(",,",$files);
                $array[$i]="{$row['id']}--{$row['Food name']}--$files--{$row['Categories']}--{$row['price']}--{$row['visits']}";
                // $recipes[$i]=implode("--", $array);
                $i++;
            }
            $recipes=$array;
            echo implode("$$", $array);
        }
    }elseif(isset($_GET['usn'])){
        $sn=$_GET['usn'];
        $searches=$_COOKIE['searches'];
        if($conn->query("UPDATE users SET searches=$searches WHERE id=$sn")) echo "Saved your information<br>";
        else echo "Failed to save your search information<br>";
        setcookie("sn", '', -1);
        setcookie("logout", "true");
        setcookie("Uname", '', -1);
        echo "Done";
    }
}
mysqli_close($conn);