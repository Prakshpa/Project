<!DOCTYPE html>
<html>
    <head>
        <title>Registration Form</title>
        <?php 
        $fname=$lname=$uname=$pw=$rpw=$mname=$dob=$gender=$address='';
        $confirmPass='';
        $error="Invalid information";
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $fname=trim($_POST["fname"]);
            $mname=trim($_POST["mname"]);
            $lname=trim($_POST['lname']);
            $uname=trim($_POST['emailOrPass']);
            $dob=$_POST['dob'];
            $gender=$_POST['gender'];
            $address=trim($_POST['address']);
            $pw=$_POST['password'];
            $rpw=$_POST['rePassword'];
        }
        if(preg_match(""))
        if($pw!=$rpw){
            $confirmPass="Confirm password";
            echo $error;
        }
        else echo "Successful submission";
        ?>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <h1>Registration Form</h1>
        <div id="form">
            <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
                <fieldset>
                    <legend>Personal information</legend>
                    <label for="fname, mname, lname">Username<span class="req">*</span>: </label>
                    <input type="text" name="fname" placeholder="First name" <?php echo "required"?>> 
                    <input type="text" name="mname" placeholder="Middle name"> 
                    <input type="text" name="lname" placeholder="Last name" <?php echo "required";?>><br>
                    <label for="dob">Date of Birth<span class="req">*</span>: </label>
                    <input type="date" name="dob" id="dob" placeholder="YYYY/MM/DD" <?php echo "required";?>><br>
                    <label for="gender">Gender<span class="req">*</span>: </label>
                    <input type="radio" name="gender" value="Male" id="gender" <?php echo "required";?>>Male
                    <input type="radio" name="gender" value="Female">Female<br><br>
                    <label for="address" style="vertical-align: top;">Address<span class="req">*</span>: </label>
                    <textarea name="address" id="address" placeholder="address" cols="50" <?php echo "required";?>></textarea>
                </fieldset><br>
                <fieldset>
                    <legend>Account Information</legend>
                    <label for="emailOrPass">User ID<span class="req">*</span>: </label>
                    <input type="text" name="emailOrPass" id="userId" placeholder="Email/Phone" <?php echo "required";?>><br>
                    <label for="password">Password<span class="req">*</span>: </label>
                    <input type="password" name="password" id="pass" <?php echo "required";?>><br>
                    <label for="confirm">Confirm Pw<span class="req">*</span>: </label>
                    <input type="password" name="rePassword" id="passRe" <?php echo "required";?>>
                    <span><?php echo $confirmPass;?></span><br> 
                </fieldset>
                <p><input type="checkbox" name="t&c" id="t&c" <?php echo "required";?>> I accept <a href="TAndC.txt" target="_blank">Terms and Conditions</a></p>
                <button type="submit" id="submit">Register</button>
                <input type="reset" name="" id="" value="Clear">
                <p>Already have an account? <a href="Login.html">Login</a></p>
            </form>
        </div>
        
        <script type="text/javascript" src="action.js"></script>
    </body>
</html>
