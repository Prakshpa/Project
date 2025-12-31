<?php
include "Connection.php";
$recipe=false;
$user=false;
$price=$uid=$rid=0;
if(isset($_GET['user'])){
    $query=mysqli_query($conn, "SELECT id, `Full Name` FROM Users WHERE id={$_GET['user']}");
    if(!$query) die(mysqli_error($conn));
    elseif(mysqli_num_rows($query)==0) die("Unidentified");
    else {
        $row=$query->fetch_assoc();
        $user=true;
        $uid=$row['id'];
    }
} else echo "Error";
if(isset($_GET['recipe'])){
    $query=mysqli_query($conn, "SELECT id, `Food Name`, price From Recipes WHERE id={$_GET['recipe']}");
    if(!$query) die(mysqli_error($conn));
    elseif(mysqli_num_rows($query)==0) die("Unidentified");
    else {
        $row=$query->fetch_assoc();
        $recipe=true;
        $price=$row['price'];
        $rid=$row['id'];
    }
}
if($user && $recipe) {
    $query=mysqli_query($conn, "SELECT User, Recipe, Rupees_paid FROM purchases WHERE User=$uid AND Recipe=$rid");
    if(!$query) die(mysqli_error($conn));
    elseif(mysqli_num_rows($query)>0){
        echo "Payment was made";
        header("location:viewRecipe.php?user=$uid&&recipe=$rid");
        die();
    }
    elseif(mysqli_num_rows($query)>0) die("Payment was made"); 
    
}else die();
$success_url="http://localhost/ecommerce/success.php?uid=$uid&rid=$rid&";
$failure_url="http://localhost/ecommerce/failure.php?uid=$uid&rid=$rid&";
$transaction_uuid=uniqid("TXN_");
$secret_key="8gBm/:&EnhH.1/q";
require_once("functions.php");
$signature=generateEsewaSignature($price, $transaction_uuid, "EPAYTEST", $secret_key);
?>
<body>
    <form action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST" id="form0" name="form0" target="new">
        <input type="text" name="amount" value="<?php echo $price; ?>">
        <input type="text" name="tax_amount" value="0">
        <input type="text" name="total_amount" value="<?php echo $price; ?>">
        <input type="text" name="transaction_uuid" value="<?php echo $transaction_uuid; ?>">
        <input type="text" name="product_code" value="EPAYTEST">
        <input type="text" name="product_service_charge" value="0">
        <input type="text" name="product_delivery_charge" value="0">
        <input type="text" name="success_url" value="<?php echo $success_url; ?>">
        <input type="text" name="failure_url" value="<?php echo $failure_url; ?>">
        <input type="text" name="signed_field_names" value="total_amount,transaction_uuid,product_code">
        <input type="text" name="signature" value="<?php echo $signature; ?>">
        <input type="submit" value="Submit" onclick="submit()">
    </form>
    <script>
        function submit(){
            document.getElementById("form0").submit();
        }
    </script>
</body>