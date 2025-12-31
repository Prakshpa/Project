<?php
include "Connection.php";
$pp=$name=$pw=$query='';
$sn=-1;
if(isset($_REQUEST['sn'])) {
    $sn=$_GET['sn'];
    echo $sn;
    $sql="SELECT `id`, `Full Name`, PP FROM `users` WHERE `id`=$sn";
    $query=mysqli_query($conn, $sql);
    if(!$query) echo mysqli_error($conn);
    elseif(mysqli_num_rows($query)==0) die("unidentified user id");
    else {
        if($data=mysqli_fetch_assoc($query)){
            $name=$data['Full Name'];
            $pp=$data['PP'];
        }
    }
}elseif(isset($_COOKIE['sn'])) $sn=$_COOKIE['sn'];
else header("location:Login.php");
if(isset($_POST['change'])){
    $query=false;
    if($_POST['id']!='' && preg_match("/^[a-zA-Z ]{3,}$/", trim($_POST['id']))){
        $name=trim($_POST['id']);
    }
    if(move_uploaded_file($_FILES['pp']['tmp_name'], $_FILES['pp']['name'])) {
        $pp=$_FILES['pp']['name'];
        echo "file upload success";
    }else echo "file upload failed";
    $query=$conn->query('UPDATE `users` SET `Full Name`="'.$name.'", PP="'.$pp.'" WHERE `id`='.$sn);
    echo 'UPDATE `users` SET `Full Name`="'.$name.'" AND PP="'.$pp.'" WHERE `id`='.$sn;
    if($query){
        // header("location:Profile.php?sn=$sn");

        echo "Profile updated successfully";
    }else {
        // header("location:Profile.php?sn=$sn");
        echo mysqli_error($conn);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h3>Change Profile</h3>
    <form method="post" enctype="multipart/form-data"> 
        Profile Picture: <input type="file" name="pp" id="pp" value=""><br>
        Name: <input type="text" name="id" id="name" value="<?php echo $name;?>"><br>
        <button type="submit" name="change">Save</button>        
    </form>
    <!-- <script type="text/javascript" src="action.js"></script> -->
</body>
</html>