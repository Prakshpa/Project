<!DOCTYPE html>
<html>
<head>
    <?php
    include("Connection.php");
    setcookie('Uname', "", -1);
    $login=$password="";
    $loginErr=$passwordErr=$error="";
    $clear=false;
    $LoggedIn=false;
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $login=$password="";
        $loginErr=$passwordErr=$error="";
        $clear=false;
        $login=$_POST["userId"];
        $password=$_POST["pass"];
        if(empty(trim($login))) $loginErr="Required";
        elseif (filter_var(trim($login), FILTER_VALIDATE_EMAIL) || filter_var(trim($login), FILTER_VALIDATE_INT) && strlen(trim($login))==10) {
            $loginErr="";
        } 
        else $loginErr="Invalid login!!";
        if(empty($password)) $passwordErr="Required";
        elseif(strlen($password)<8) $passwordErr="Incorrect Password";
        else $passwordErr="";
        if($loginErr=="" && $passwordErr=="" && $error==""){
            $pw="SELECT * FROM `Users` WHERE login='$login'";
            $pw=mysqli_query($conn, $pw);
            if(mysqli_num_rows($pw)>0){
                while($row=mysqli_fetch_assoc($pw)){
                    if(password_verify($password, $row['password'])){
                        echo "Hello";
                        echo $row['id'];
                        setcookie("logout", "",-1);
                        setcookie("Uname", $login, time()+100000);
			            setcookie("login", $login, time()+100000);
                        if($row['role']=="Administrator") header("location:admin.php?login=$login");
                        else header("location:views/index.view.php?login=$login");
                    }else $passwordErr="Incorrect password";
                    break;
                }                
            }else $loginErr="Invalid login";
            // else{
            //     header("index.php");
            //     die();
            // }
            // $s=mysqli_query($conn, $sql);
            // echo "(string)$s";
            // print_r($s);
            // if(mysqli_num_rows($s)>0) {
            //     while($position=mysqli_fetch_assoc($s)){
            //         if(PASSWORD_Verify($password)){
            //             if($position['position']){
            //                 header("admin.php");
            //                 die;
            //             }
            //             else {
            //                 $GLOBALS["LoggedIn"]=true;
            //                 header("index.php");
            //                 die;
            //             }
            //         }
            //         else $error="Invalid Login and password";
            //     }
            // }
            // else {
            //     $error="Invalid Login and password";
            // }
        }
    }
    elseif($clear){
        $login=$password="";
        $loginErr=$passwordErr=$error="";
        header("location:Login.php");
    }
    ?>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="form">
        <h1>Login form</h1>
        <form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post">
            <table>
                <tr>
                    <td><label for="userId">User ID</label></td>
                    <td><input type="text" name="userId" id="userId" value="<?php echo $login;?>" placeholder="Email or Phone">
                        <span class="error"><?php echo $loginErr;?></span></td>
                </tr>
                <tr>
                    <td><label for="pass">Password</label></td>
                    <td><input type="password" id="pass" name="pass" value="<?php echo $password;?>"><br>
                        <span class="error"><?php echo $passwordErr;?></span></td>
                </tr>
            </table><br><br>
            <span class="error"><?php echo $error; ?></span>
            <p>Don't have an account?<a href="Register.php">Sign up</a></p><br><br>
            <button type="submit" id="submit">Login</button>
            <input type="button" name="clear" value="Clear" onclick="<?php $clear=true;?>">
        </form>
    </div>
    <!-- <script type="text/javascript" src="action.js"></script> -->
</body>
</html>