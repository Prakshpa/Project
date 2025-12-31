<!DOCTYPE html>
<?php
include "Connection.php";
$login=$pass=$loginErr='';
if(isset($_GET['lo']) &&$_GET['lo']=='1') {
    setcookie('Uname', '', -1);
    header("location:Login.php");
}elseif(!isset($_COOKIE['Uname']) || $_COOKIE['Uname']!=$_GET['login']) {
    header("location:Register.php");
}
if($_SERVER['REQUEST_METHOD']=='POST'){
    if(isset($_POST['Add'])){
        $login=$_POST['login'];
        $pass=$_POST['pass'];
        if(empty(trim($login))) $loginErr="Required";
        if(filter_var(trim($login), FILTER_VALIDATE_EMAIL) || filter_var(trim($login), FILTER_VALIDATE_INT) && strlen(trim($login))==10) $loginErr="";
        else $loginErr="Invalid userId";
        if(empty($pass)) $pass="admin";
        if($loginErr==''){
            $tlogin=trim($login);
            $hPassword=password_hash($pass, PASSWORD_DEFAULT);
            $sql="SELECT * FROM `admins` WHERE login='$tlogin'";
            $query=mysqli_query($conn, $sql);
            if($query){
                if(mysqli_num_rows($query)>0){
                    $loginErr="Enter new userId";
                    echo "<script>alert('Email or Phone is already entered');</script>";
                }else{
                    $sql="INSERT INTO `admins`(login, Password) VALUES('$tlogin', '$hPassword')";
                    $query=mysqli_query($conn, $sql);
                    if($query){
                        header("location:admin.php");
                        echo "Added admin successfully";
                    }else echo(mysqli_error($conn));
                }
            }else echo(mysqli_error($conn));
        }
    }
    if(isset($_POST['cancle'])){
        $login="";
        $pass="";
        $loginErr='';
        echo "Hello World!!!";
        header("location:admin.php?login=$login");
    }
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <style>
        a{
            /* margin: 10px 50px; */
            background-color: blue;
            color: white;
            padding: 20px 30px;
        }
        div{
            height: fit-content;
        }
        img{
            height: 10px;
            width: 10px;
        }
        tr img{
            height: 10px;
            font-size: small;
        }
        th+th+th{
            width: 50px;
        }
    </style>
</head>
<body>
    <div style="display: flex;">
        <table style="margin-right: 100px;">
            <tr>
                <th>SN</th>
                <th>Login</th>
                <th>Action</th>
            </tr>
            <?php 
            $admins="SELECT * FROM `admins`";
            $read=mysqli_query($conn, $admins);
            if($read){
                $sn=1;
                while($row=mysqli_fetch_assoc($read)){
                    $asn=$row['id'];
                    echo "<tr>
                            <td>$sn</td>
                            <td>".$row['login']."</td>
                            <td><button onclick='deleteA(this, $asn)'><img src='delete.jpg' ></button></td>
                        </tr>";
                    $sn++;
                }
            }
            ?>
        </table>
        <div>
            <button type="button">Add admin</button>
            <form action="" method="post" id="addAdmin" style="display: content;">
                <input type="text" name="login" value="" placeholder="Email or phone" required>
                <span></span><br>
                <input type="password" name="pass" placeholder="Password"><br>
                <input type="submit" name="Add" value="Add">
                <input type="reset" name="cancel" id="cancel" value="Cancel">
            </form>
        </div>
    </div><br>
    <nav style="display:flex; justify-content:space-around; background-color:lightgray; height:70px; position:relative; padding:5px;">
        <a href="Members.php" target="display">Members</a>
        <a href="Recipes.php" target="display">Recipes</a>
        <button type="submit" onclick="location.href='admin.php?lo=1'">Logout</button>
    </nav>
    <iframe src="" frameborder="0" width="100%" height="100%" name="display" style="position: absolute;"></iframe>
    <script type="text/javascript">
        var ajax=new XMLHttpRequest();
        document.querySelector("input[type=reset]").addEventListener("click", ()=>{
            document.getElementById("addAdmin").style.display='none';
        });
        document.querySelector("button[type=button]").addEventListener("click", ()=>{
            document.getElementById("addAdmin").setAttribute("style", "display:content");
        });
        ajax.onreadystatechange=()=>{
            if(ajax.readyState==4)
                if(ajax.status==200)
                    location.href="admin.php?login=<?php echo $login;?>";
        }
        function deleteA(button, sn){
            ajax.open('POST', "RequestHandle.php?asn="+sn, true);
            ajax.send();
        }
    </script>
</body>
</html>