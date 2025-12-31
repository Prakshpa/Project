<?php
include "Connection.php";
$rid=$uid=$tid=$data=$author='';
$price=0;
if(isset($_GET['rid'])){
    $rid=$_GET['rid'];
    // $data=substr($rid, strpos($rid, '?')+6);
    
    $query=mysqli_query($conn, "Select id, price, Author from recipes where id=$rid");
    if($query) if(mysqli_num_rows($query)>0) {
        $row=$query->fetch_assoc();
        $price=$row['price'];
        $author=$row['Author'];
    }else echo "No such recipe available";
    else echo mysqli_error($conn);
}else exit;
if(isset($_GET['uid'])){
    $uid=$_GET['uid'];
}else exit;
// if(isset($_COOKIE['tid'])){
//     $tid=$_COOKIE['tid'];
// }else exit;
if(isset($_GET['?data']))    $data=$_GET['?data'];
else die;
$data=base64_decode($data);
$data=json_decode($data, true);
print_r($data);
// die();
$query=mysqli_query($conn, "Insert into Purchases(user, recipe, Rupees_paid) Values($uid, $rid, $price)");
if($query) {
    echo "Payment successful,<br> Purchase successful";
    setcookie("user", $uid, time()+100000, "../");
    $total=
    $innerQuery=mysqli_query($conn, "Insert into transactions(ID, User, Recipe, Price, Status, Owner) VALUES(\"{$data['transaction_uuid']}\", $uid, $rid, {$data['total_amount']}, \"{$data['status']}\", \"$author\")");
    if(!$innerQuery) die(mysqli_error($conn));
}else echo mysqli_error($conn);
echo "<script type='text/javascript'>alert('Purchased successfully')</script>";
mysqli_close($conn);
?>