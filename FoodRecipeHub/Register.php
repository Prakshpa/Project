<?php 
session_start();
if(!isset($_SESSION['sn'])){
    $_SESSION['sn']=6;
}
include "Connection.php";
$fname=$lname=$uname=$pw=$rpw=$mname=$dob=$gender=$address=$tncErr=$role='';
$confirmPass=$nameErr=$genderErr=$passwordErr=$addressErr=$loginErr=$dobErr='';
$role="Member";
$error="Invalid information";
function validName($value){
    if(preg_match("/^[a-zA-Z ]*$/", $value))
        return true;
    else
        return false;
}
function validAddress($value) {
    if(!preg_match("/^[a-zA-Z0-9 \s,-]{3,}$/", $value)){
        return false;
    }elseif($value[0]==',' || $value[0]=='-' || $value[strlen($value)-1]==',' || $value[strlen($value)-1]=='-') return false;
    else return true;
}
if(isset($_COOKIE['clear'])) if($_COOKIE['clear']){
    $fname=$lname=$uname=$pw=$rpw=$mname=$dob=$gender=$address=$role='';
    $confirmPass=$nameErr=$genderErr=$passwordErr=$addressErr=$loginErr=$dobErr='';
    header("location:Register.php");
}
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['submit'])){
    $fname=$_POST["fname"];
    $mname=$_POST["mname"];
    $lname=$_POST['lname'];
    $uname=$_POST['emailOrPass'];
    $dob=$_POST['dob'];
    $gender=isset($_POST['gender'])? $_POST['gender']:'';
    $address=$_POST['address'];
    $pw=$_POST['password'];
    $rpw=$_POST['rePassword'];
    $role=($_POST['role']=="Administrator")? "Administrator":"Member";
    if(empty(trim($fname))||empty(trim($lname))) $nameErr="Required";
    elseif(!validName(trim($fname)) || !validName(trim($mname)) || !validName(trim($lname))) $nameErr="Invalid name";
    else {
        $nameErr="";
        $fullname=trim($fname).' '.trim($mname).' '.trim($lname);
    }
    if(empty(trim($uname))) $loginErr="Required";
    elseif(filter_var(trim($uname), FILTER_VALIDATE_EMAIL)){
        $temp=trim($uname);
        switch($temp[0]){
            case "0":
            case "1":
            case "2":
            case '3':
            case "4":
            case "5":
            case '6':
            case '7':
            case '8':
            case '9':
                $loginErr="Emaiil must start with an alphabet";
                break;
            default: $loginErr="";
        }
        $temp=substr($temp, strpos($temp, "@")+1, strrpos($temp, ".")-strpos($temp, "@")+1);
        switch($temp[0]){
            case "0":
            case "1":
            case "2":
            case '3':
            case "4":
            case "5":
            case '6':
            case '7':
            case '8':
            case '9':
                $loginErr="domain after @ must start with an alphabet";
                break;
            default: $loginErr="";
        }
    } elseif(filter_var(trim($uname), FILTER_VALIDATE_INT)){
        if(strlen(trim($uname))!=10) $loginErr="Phone number should be of 10 digits";
        elseif(((int)trim($uname)>=9700000000) && (int)trim($uname)<9900000000) $loginErr="";
        else $loginErr="Phone number should begin with 97 or 98";
    }else $loginErr="Invalid login";
    $today=date_create();
    $date=new DateTime($dob);
    if(empty($dob)) $dobErr="Required";
    elseif($date>$today){
        $dobErr="Invalid date of birth";
    } 
    if(empty($gender)) $genderErr="Required";
    elseif($gender=="Male" || $gender=="Female") $genderErr="";
    else header("Location:Register.php");
    if(empty($address)) $addressErr="Required";
    elseif(!validAddress($address)) $addressErr="Invalid address";
    if(empty($pw)) $passwordErr="Required";
    elseif(strlen($pw)<8) $passwordErr="Password must be at least 8 characters";
    if($pw!=$rpw) $confirmPass="Confirm password";
    if(!isset($_POST["t&c"])) $tncErr="You must accept Terms and conditions";
    if(empty($nameErr) && empty($loginErr) && empty($dobErr) && empty($genderErr) && empty($addressErr) && empty($passwordErr) && empty($confirmPass)) {
        $tuname=trim($uname);
        $taddress=trim($address);
        $genderi=($gender=="Male")? true:false;
        $hpassword=password_hash($pw, PASSWORD_BCRYPT);
        $tdob=date_create($dob);
        $GLOBALS["LoggedIn"]=true;
        password_hash($pw, PASSWORD_DEFAULT);
        $sql="SELECT * FROM `Users` WHERE login='$tuname'";
        $query=mysqli_query($conn, $sql);
        if($query){
            if(mysqli_num_rows($query)>0){
                echo "<script>alert('Email or Phone is already registered')</script>";
                $loginErr="Enter new login";
            }
            else{
                if($role=="Administrator") {
                    $sql="SELECT * FROM `admins` WHERE login='$tuname'";
                    $query=mysqli_query($conn, $sql);
                    if($query){
                        if(mysqli_num_rows($query)>0) $role="Administrator";
                        else $role="Member";
                    }
                }
                $_SESSION['sn']++;
                $sql="INSERT INTO `Users` (`Full Name`, login, Address, password, gender, dob, role) values('$fullname', '$tuname', '$taddress', '$hpassword', $genderi, '$dob', '$role')";
                $query=mysqli_query($conn, $sql);
                if($query){
                    session_start();
                    setcookie('login', $tuname, time()+1000);
		    setcookie('user', $_SESSION['sn'], time()+1000000);
                    if($role=="Administrator") {
                        header("location:admin.php");
                        exit();
                    }
                    header("location:views/index.view.php?login=$tuname"); 
                    mysqli_close($conn);      
                }else echo mysqli_error($conn);
            }
        }else echo mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Registration Form</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <h1>Registration Form</h1>
        <div id="form">
            <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
                <fieldset>
                    <legend>Personal information</legend>
                    <label for="fname, mname, lname">Username<span class="req">*</span>: </label>
                    <span class="req">-</span><input type="text" name="fname" placeholder="First name" value="<?php echo $fname;?>" required> 
                    <input type="text" name="mname" placeholder="Middle name" value=<?php echo $mname;?>> 
                    <span class="req">-</span><input type="text" name="lname" placeholder="Last name" value="<?php echo $lname;?>" required><br>
                    <span><?php echo $nameErr.'<br>';?></span>
                    <label for="dob">Date of Birth<span class="req">*</span>: </label>
                    <input type="date" name="dob" id="dob" placeholder="YYYY/MM/DD" <?php if(isset($dob)) echo"value= '$dob'";?> required>
                    <span><?php echo $dobErr.'<br>';?></span>
                    <label for="gender">Gender<span class="req">*</span>: </label>
                    <input type="radio" name="gender" value="Male" id="gender" <?php if($gender=='Male') echo 'checked="checked"';?> required>Male
                    <input type="radio" name="gender" value="Female" <?php if($gender=='Female') echo 'checked="checked"';?>>Female
                    <span><?php echo $genderErr.'<br>';?></span>
                    <label for="address" style="vertical-align: top;">Address<span class="req">*</span>: </label>
                    <textarea name="address" id="address" placeholder="address" cols="50" required><?php echo $address;?></textarea>
                    <span><?php echo $addressErr.'<br>';?></span>
                </fieldset><br>
                <fieldset>
                    <legend>Account Information</legend>
                    <label for="emailOrPass">User ID<span class="req">*</span>: </label>
                    <input type="text" name="emailOrPass" id="userId" placeholder="Email/Phone" value="<?php echo $uname;?>" required>
                    <span><?php echo $loginErr.'<br>';?></span>
                    <label for="password">Password<span class="req">*</span>: </label>
                    <input type="password" name="password" id="pass" value="<?php echo $pw;?>" required>
                    <span><?php echo $passwordErr;?></span><br>
                    <label for="confirm">Confirm Pw<span class="req">*</span>: </label>
                    <input type="password" name="rePassword" id="passRe" value="<?php echo $rpw;?>" required>
                    <span><?php echo $confirmPass;?></span><br> 
                    <label for="role">Role:</label>
                    <select name="role" id="role" >
                        <option value="Administrator" <?php if($role==true) echo "default";?>>Administrator</option>
                        <option value="Member" <?php if($role!=true) echo "default";?>>Member</option>
                    </select>
                </fieldset><br>
                <input type="checkbox" name="t&c" id="t&c" <?php if(isset($_POST['t&c'])) echo 'checked="checked"';?> required> I accept <a href="TAndC.txt" target="_blank">Terms and Conditions</a><br>
                <span><?php echo $tncErr."<br>";?></span><br>
                <input type="submit" id="submit" name="submit" value="Register">
                <input type="reset" name="" id="clear" value="Clear"> <br> <br>
                <p>Already have an account? <a href="Login.php">Login</a></p>
            </form>
        </div>
        
        <!-- <script type="text/javascript" src="action.js "></script> -->
    </body>
    
</html>