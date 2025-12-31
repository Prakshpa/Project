
<?php
include "Connection.php";

$output=mysqli_query($conn,"Truncate table purchases");
if($output){
    echo mysqli_info($conn);
}else echo mysqli_error($conn);