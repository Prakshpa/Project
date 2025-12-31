<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        tr button>img{
            width: 20px;
            height: 20px;
            border-radius: 20px;
            margin: 0px;
        }
        td{
            font-size: medium;
            color: #00ffff;
        }
        table td{
            height: 40px;
        }
        tr+tr{
            height: 40px;
        }
        td>a{
            height: 40px;
            margin: 0px;
        }
    </style>
    <?php 
    include "Connection.php";
    $recipes=$author=$header=$pp="";
    if(isset($_GET['sn']) && isset($_COOKIE['sn'])){
        $sn=$_GET['sn'];
        $sql="SELECT * FROM users Where `id`=$sn";
        $result=$conn->query($sql);
        if(!$result) echo mysqli_error($conn);
        else{
            $result=$result->fetch_assoc();
            $author=$result['Full name'];
            $pp=$result['PP'];
            $recipes="";
            $picture='';
            $sql="SELECT `id`, `Food name`, Author, `Help files` FROM Recipes WHERE Author='$author'";
            $result=$conn->query($sql);
            $c=1;
            if(!$result) echo mysqli_error($conn);
            else while($recipe=$result->fetch_assoc()){
                $rsn=$recipe['id'];
                $img=explode(",,", $recipe['Help files']);
                foreach($img as $test) if (preg_match("/\.jpg$/", $test) || preg_match("/\.jpeg$/", $test) || preg_match("/\.png$/", $test)){
                    $picture=$test;
                    break;
                }else $picture=$img[0];
                $GLOBALS['recipes'].="<tr> <td>$c</td> <td><a href='addRecipe.php?usn=$sn&&rsn=$rsn'><img src='$picture'>".$recipe['Food name']."</a></td> <td><button type='buttton' onclick='deleteI(this, $rsn)'><img src='delete.jpg'></button></td> </tr>";
            }
            $header="editProfile.php?sn=$sn";
        }
    }else echo "Not working";
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="forProfile">
        <div id="profile" style="float: left;">
            <a href="<?php echo $header;?>" id="ppAndName" target="subFrame">
                <img src="<?php echo $pp;?>" alt="Profile Picture" id="pp"><br>
                <span id="name" id="mn"><?php echo $author;?></span>
            </a>
            <button onclick="logOut(<?php echo $sn; ?>)">Log out</button>
            <table id="myRecipes" >
                <caption>My recipes</caption>
                <tr>
                    <th>S.No</th>
                    <th>Recipe name</th>
                    <th>Actions</th>
                </tr>
                <?php echo $recipes;?>
            </table>
            <a href="addRecipe.php?usn=<?php echo $sn;?>">Add Recipe</a>
        </div>
        <iframe src="" frameborder="0" name="subFrame" id="editP"></iframe>
    </div>
    <script type="text/javascript">
        var ajax=new XMLHttpRequest();
        ajax.onreadystatechange=function(){
            if(ajax.readyState==4){
                if(ajax.status==200){
                    if(ajax.responseText!="Done") document.write(ajax.responseText);
                    else document.write("Logged out");
                    <?php
                    // $sql="SELECT `S.No`, `Food name`, Author, `Help files` FROM Recipes WHERE Author='$author'";
                    // $GLOBALS['recipes']='';
                    // $result=$conn->query($sql);
                    // if(!$result) echo mysqli_error($conn);
                    // else while($recipe=$result->fetch_assoc()){
                    //     $img=explode(",,", $recipe['Help files']);
                    //     echo $recipe['Help files'];
                    //     foreach($img as $test) if (preg_match("/.jpg$/", $test) || preg_match("/.jpeg$/", $test) || preg_match("/.png$/", $test)){
                    //         $picture=$test;
                    //         break;
                    //     }else $picture=$img[0];
                    //     var_dump($img);
                    //     $GLOBALS['recipes'].="<tr> <td><img src='$picture'>".$recipe['Food name']."</td> <td><button type='buttton' onclick='deleteI(this, ".$recipe['S.No'].")'><img src='delete.jpg'></button></td> </tr>";
                    // }
                    ?>
                }
            }
        }
        // document.querySelector("#myRecipes td>buttton").addEventListener("click", ()=>{
        //     ajax.open("POST", "RequestHandle.php", true);
        //     ajax.setRequestHeader("Content-Type", "url-encoded");
        // })
        function deleteI(element, rsn){
            ajax.open('GET', "RequestHandle.php?rsn="+rsn, true);
            // data={'sn':sn};
            ajax.send();
        }
        function logOut(usn){
            console.log(`Testing ${usn}`);
            ajax.open("GET", "RequestHandle.php?usn="+usn, true);
            ajax.send();
        }
    </script>
    <!-- <script type="text/javascript" src="action.js"> </script> -->
</body>
</html>