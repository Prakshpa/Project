<?php
include("Connection.php");
function dd($var)
{
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
    die();
}
// dd($query);
$update=false;
$sn=$ingredients=$steps=$files=$food=$ic=$qc=$fc='';
if(isset($_GET['recipe']) && isset($_GET['user']) && $_COOKIE['user']==$_GET['user']){
    $sn=$_GET['recipe'];
    $query=$conn->query("SELECT * FROM Users WHERE `id`={$_COOKIE['user']}");
    if(!$query){ echo "Error 1"; }
    if(mysqli_num_rows($query)==0) die();
}else die("An error occured");
$sql="SELECT * FROM `Recipes` WHERE `id`=$sn";
$query=$conn->query($sql);
if($query){
    if(mysqli_num_rows($query)==0) die();
    $recipe=$query->fetch_assoc();
    $ingredients=$recipe['Ingredients'];
    $steps=$recipe['Instructions'];
    $food=$recipe['Food name'];
    $files=$recipe['Help files'];
    $price=$recipe['price'];
    $update=true;
}else die("An error occured.");
if($price>0){
    $query=mysqli_query($conn, "SELECT * FROM purchases WHERE User={$_GET['user']} AND Recipe=$sn");
    if(!$query) die(mysqli_error($conn));
    elseif(mysqli_num_rows($query)==0) header("location:checkout.php?user={$_COOKIE['user']}&&recipe=$sn");
}elseif(isset($_COOKIE['visits'])){
    $visits=$_COOKIE['visits'];
    if(!preg_match("/[ ]*($sn){1}[ ]*/",$visits)) {
        $sql="UPDATE Recipes SET visits=visits+1 WHERE id=$sn";
        $query=$conn->query($sql);
        if(!$query) echo mysqli_error($conn)+"Error 2";
        else {
            if(strlen($visits)>40) $visits=substr($visits, strpos($visits, " ", strlen($visits)-40)+1);
            setcookie("visits", "$visits $sn", time()+60*60*24);
        }
    }
}
$searches=$_COOKIE['searches'];
while(strlen($searches)>200) {
    if(strpos($searches, "<foodName>")<strlen($searches)-200 || strpos($searches, ",")>strpos($searches, "<foodName>")){
        $searches=explode("<foodName>",$searches,2);
        $searches[1]=substr($searches[1], strpos($searches[1],",")+2);
        $searches=implode("<foodName>", $searches);
    }else $searches=substr($searches,strpos($searches,",", strlen($searches)-200)+2);
}
$sql="UPDATE users SET searches='$searches' WHERE id={$_COOKIE['user']}";
$query=$conn->query($sql);
if(!$query) echo mysqli_error($conn);
$ingredients=explode(',,', $ingredients);
$ingredients=preg_grep("/[a-z0-9]/", $ingredients);
$c=1;
foreach($ingredients as $ingredient){
    $ingredient=explode('&&', $ingredient);
    $ic.="<tr> <td>".$c++."</td> <td>$ingredient[0]</td> <td>$ingredient[1]</td> </tr>";
}
$steps=explode(',,', $steps);
$steps=preg_grep("/[a-z]/", $steps);
$c=1;
foreach($steps as $step){
    $qc.="<tr> <td>".$c++."</td> <td>$step</td> </tr>";
}
$files=explode(',,', $files);
$files=preg_grep("/[a-z0-9_ \s\-\.]/", $files);
foreach($files as $file){
    $file=trim($file);
    if(preg_match("/[(\.jpg)|(\.png)|(\.jpeg)|(\.psd)]$/i", $file)){
        $fc.="<img src='$file'>";
    }else $fc.="<video src='$file' controls>";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style></style>
</head>
<body>
    <h1>Recipe of <?php echo $food; ?></h1>
    <table style="float:inline-start; margin:0px 50px">
        <caption>Ingredients</caption>
        <tr> 
            <th>S.No</th>
            <th>Ingredient</th>
            <th>Quantity</th>
        </tr> <?php echo $ic; ?>
    </table>
    <table>
        <caption>Steps to prepare</caption>
        <tr>
            <th>Step</th>
            <th>Instruction</th>
        </tr> <?php echo $qc; ?>
    </table><br><br>
    <div id="files" style="margin: 20px 30px; justify-content: space-around"> <?php echo $fc; ?> </div>";
</body>
</html>