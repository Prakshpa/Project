<?php
if(!isset($_GET['logged'])) if(!$_GET['logged']) {
    
    header("location:Register.php");
    die();
}
?>