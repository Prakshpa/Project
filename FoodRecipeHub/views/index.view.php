<?php

include "../Connection.php";
$pp=$name=$sn=$header='';
if(!isset($_COOKIE['login']) || !isset($_GET['login']) || $_COOKIE['login']!=$_GET['login']) {
    
    header("location:../Register.php");
    die("Register or login first");
}
$uname=$_COOKIE['login'];
$query=$conn->query("SELECT * FROM `users` WHERE login='$uname'");
if(!$query) echo mysqli_error($conn);
elseif(mysqli_num_rows($query)==0){
    die ("Unrecognized user id");
    header("location:../Register.php");
}else{
    $info=$query->fetch_assoc();
    $pp=$info['PP'];
    $name=$info['Full name'];
    $sn=$info['id'];
    setcookie("user", $sn, time()+1000);
}
function click(){
    if(isset($_COOKIE['logout'])) {
        header("location:../Login.php");
        die;
    }
    setcookie("Uname", $GLOBALS['uname'], time()+10000); 
    return $GLOBALS['uname'];
}
function profile(){
    $header="../Profile.php?sn={$GLOBALS['sn']}";
    setcookie("sn", "{$GLOBALS['sn']}", time()+100000, "/");
    return $header;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        nav{
            position: fixed;
            opacity: 90%;
            background-color: green;
            height: 50px;
            display: flex;
            justify-content: space-between;
        }
        a{
            text-decoration: none;
            font-size: larger;
            color: #00ffff;
            padding-bottom: 20px;
            vertical-align: middle;
            min-height: fit-content;
            opacity: 100%;
            margin-right: 100px;
        }
        #display{
            margin-top: 100px;
        }
        
    </style>
</head>
<body>
    <nav id="navigation">
        <a href="<?php echo profile() ?>" id="profileI" target="display"><img src="../<?php echo $pp;?>" alt="My Profile" id="pp"></a>
        <a href="index.view.php?login=<?php echo click(); ?>" id="home" target="_self"> Recipe</a>
        <a href="../helpDesk.php" target="display">Get Help</a>
        <a href="#about" >About</a>
        <a href="#contact">Contact</a>
    </nav>
    <iframe src="../Home.php?usn=<?php echo $sn;?>" frameborder="1" id="display" name="display" ><iframe id="forProfile" name="forProfile" src="" frameborder="0"></iframe></iframe>
    <div id="about">
        <h3>About us</h3>
        <p>This web site is developed to allow the multiple users to share the food recipe that are stored in the database which can be accessed 
            by any users who visit the site by entering the food name and/or category(s). In the current state this site functions only in a single device. But 
            it is planned to be working in multiple devices after the site and database has been uploaded in an active domain address.
        </p>
    </div>
    <div id="contact">
        <h3>Contact us at</h3>
        <p> St. Lawrence College    Chabahil, Kathmandu <br>
            <a href="mailto:parajuliprakash28@gmail.com">Email us</a><br>
            <a href="https://facebook.com/prakash.parajuli.3576">Facebook</a><br>
            <a href="tel:+9779842515667">Phone</a>
        </p>
    </div><br><br><br><br>
</body>
</html>