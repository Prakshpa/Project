<?php
$username="root";
$pass="";
$host="localhost";
$database="ecommerce";
$conn=mysqli_connect($host, $username, $pass, $database);
if(!$conn) die("An error occured");
?>