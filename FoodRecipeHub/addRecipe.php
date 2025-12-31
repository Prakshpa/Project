<!DOCTYPE html>
<?php
    include "Connection.php";
    $ingredients=$categories=$procedure=$food="";
    $sn=1;
    $qty=$fErr=$user=$uname="";
    $topic="Add Recipe";
    $added=true;
    $quantity=false;
    $steps=$ing="";
    $files=$ic=$fc=$qc='';
    $action="Upload Recipe";
    $recipe=$user='';
    $type="free";
    $price=0;
    if(isset($_GET['usn'])) {
        $user=$_GET['usn'];
        $resultI=$conn->query("SELECT `id`, `Full name` FROM users WHERE `id`=$user");
        if(!$resultI) die("An error occured");
        else if (mysqli_num_rows($resultI)==0) die("Unidentified user");
        else {
            $result=$resultI->fetch_assoc();
            $uname=$result['Full name'].",$user";
        }
    }else die("Authentication error");
    if(isset($_GET['rsn'])) {
        $recipe=$_GET['rsn'];
        $resultI=$conn->query("SELECT * FROM `Recipes` WHERE `id`=$recipe");
        if(!$resultI) die(mysqli_error($conn));
        else if(mysqli_num_rows($resultI)==0) die();
        else {
            $topic="Edit recipe";
            $info=$resultI->fetch_assoc();
            $food=$info['Food name'];
            $ing=$info['Ingredients'];
            $ingredients=$info['Ingredients'];
            $procedure=$info['Instructions'];
            $steps=$info['Instructions'];
            $files=$info['Help files'];
            $categories=$info['categories'];
            $price=$info['price'];
        }
        $ingredients=explode(',,', $ingredients);
        $ingredients=preg_grep("/[a-z0-9 ]{5,}/", $ingredients);
        $c=1;
        for($i=0; $i<count($ingredients); $i++){
            if (trim($ingredients[$i])!=''){
                $ingredient=explode('&&', $ingredients[$i]);
                $ic.="<tr> <td>".$c++."</td> <td>$ingredient[0]</td> <td>$ingredient[1]</td> <td><button onclick='deleteRow(this, $c)'><img src='delete.jpg'></button></td> </tr>";
            }
        }
        $steps=explode(',,', $steps);
        $c=1;
        for($i=0; $i<count($steps)-1; $i++){
            $qc.="<tr> <td>".$c++."</td> <td>$steps[$i]</td> <td><button onclick='deleteS(this, $c)'><img src='delete.jpg'></button></td> </tr>";
        }
        $action="Save changes";
        $files=explode(',,', $files);
        foreach($files as $file){
            if(trim($file)!=''){
                if(preg_match("/\.jpg/i", $file) || preg_match("/\.png/i", $file) || preg_match("/\.jpeg/i", $file) || preg_match("/\.psd/i", $file)){
                    $fc.="<img src='$file'><button onclick=deleteF(this)><img src=delete.JPG width=20 height=20></button></img>";
                }else {
                    echo $file;
                    $fc.="<video src='$file' controls><button onclick=deleteF(this)><img src=delete.JPG width=20 height=10></button></video>";
                }
            }
        }
        // echo "<h1>Recipe of $food</h1>
        // <table> <caption>Ingredients</caption>
        // <tr> <th>S.No</th> <th>Ingredient</th> <th>Quantity</th> </tr> $ic </table> <br>
        // <table> <caption>Steps to prepare</caption>
        // <tr> <th>Step</th> <th>Instruction</th> </tr> $qc </table>
        // <div> $fc </div>";
    }
    // if(isset($_POST['addF'])){
    //     if(isset($_FILES['file']['error']) && $_FILES['file']['error']==0){
    //         if(move_uploaded_file($_FILES['file']['tmp_name'], $_FILES['file']['name'])) {
    //             echo "File uploaded successfully";
    //             $fErr='';
    //             $files+=$_FILES['file']['name'];
    //         }else $fErr="<br>Failed to upload file";
    //     }else $fErr="<br>Choose an image or video file";
    // }
    if($_SERVER['REQUEST_METHOD']=="POST"){
        $added=false;
        if($topic=="Edit recipe") $files=implode(',,', $files).',,';
        $ingredient=$_POST['ingredients'];
        $procedure=$_POST['instructions'];
        if(strlen($ingredient)<10 && strlen($procedure)<10) die("Ingredient and procedure required");
        $category=$_POST['categories'];
        $categories=trim(strtolower($categories));
        $food=$_POST["name"];
        $type=$_POST['type'];
        if($type=="paid"){
            if(isset($_POST['price'])){
                $price=$_POST['price'];
            }else $price=0;
        }else $price=0;
        if(isset($_FILES['file0'])){
            print_r($_FILES);
            for($i=0; $i<count($_FILES['file0']['name']); $i++){
                if(move_uploaded_file($_FILES['file0']['tmp_name'][$i], $_FILES['file0']['name'][$i])) $files.=$_FILES['file0']['name'][$i].",,";
                else echo "Failed to upload file ".$i+1;
            }
        }else die ("Error");
        echo $files;
        if($topic!="Edit recipe") $sql="INSERT INTO `Recipes` (`Food name`, Ingredients, Instructions, `Help files`, Categories, Author, price) VALUES(\"$food\", \"$ingredient\", \"$procedure\", \"$files\", \"$category\", \"$uname\", $price)";
        else $sql="UPDATE Recipes SET `Food name`=\"$food\", Ingredients=\"$ingredient\", Instructions=\"$procedure\", `Help files`='$files', Categories=\"$category\", Author=\"$uname\", price=$price WHERE `id`=$recipe";
        if($conn->query($sql)){
            echo "Successful $topic";
            header("location:Profile.php?sn=$user");
        }else echo mysqli_error($conn);

    }

    ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .deleteIcon{
            width: 30px;
        }
        tr button>img{
            height: 15px;
            width: 15px;
        }
    </style>
</head>
<body>
    <form method="post" enctype="multipart/form-data" data-netlify="true">
        <h2 id="title"><?php echo $topic;?></h2>
        <input type="text" name="ingredients" id="" style="display: none;" value="<?php echo $ing; ?>">
        <input type="text" name="instructions" style="display: none;" value="<?php echo $procedure; ?>">
        <input type="text" name="files" style="display: none;" value="<?php echo $files; ?>">
        <!-- <input type="text" name="" id=""> -->
        <input type="text" name="name" id="" placeholder="Food name" value="<?php echo $food; ?>" required><span id="nameErr"></span><br><br>
        <input type="text" name="categories" id="" placeholder="Food Category" value="<?php echo $categories; ?>"><br><br>
        
        <div id="Ingredients" name="ing">
            <h3>ingredients</h3>
            <table id="iL">
                <tr>
                    <th>S.N.</th>
                    <th>Name</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                    <?php if (isset($_GET['rsn']) && $topic=="Edit recipe") echo $ic;?>
                </tr>
            </table>
            <span class="container">
                <input type="text" name="ingredient">
                <input type="text" name="quantity">
                <button type="button" id="addI" >Add</button><br>
                <span id="iErr"></span>
                <span id="qErr"></span>
            </span>
        </div>
        <div id="steps">
            <h3>Steps</h3>
            <table id="step">
                <caption>Procedure</caption>
                <tr>
                    <th>S.No</th>
                    <th>Steps</th>
                    <th>Actions</th>
                </tr>
                <?php if($topic=="Edit recipe") echo $qc; ?>
            </table>
            <textarea name="step" id=""></textarea>
            <button type="button" id="addS">Add</button>
            <span id="sErr"></span>
        </div>
        <div id="files">
            <h2>Helping files</h2>
            <span id="files"><?php if($topic=="Edit recipe") echo $fc;?></span><br>
            <input type="file" name="file0[]" class="files" accept=".jpg, .png, .psd, .mp4, .jpeg, .ogg" multiple><br>
        </div><br>
        <div id="commerce">
            Recipe cost type: <select name="type" id="cost" onchange="change()">
                <option value="free" default>Free</option>
                <option value="paid" <?php if($type=="paid") echo "selected"; ?>>Paid</option>
            </select>
            <input type="number" value="<?php echo $price; ?>" name="price" id="paid" placeholder="Enter price for the recipe"><br>
        </div><br><br>

        <button type="submit"><?php echo $action;?></button>
    </form>
    <script type="text/javascript">
        // var ajax=new XMLHttpRequest();
        var fl=document.querySelector("input[name=files]").value;
        var filteredQ, filteredI, il=document.querySelector("input[name=ingredients]").value, sl=document.querySelector("input[name=instructions]").value, fl="";
        var validity=/^[a-zA-Z0-9_ \-\'\.\,]+$/;
        var numChar=/^[a-zA-Z0-9]/;
        var numCharI=/[a-zA-Z0-9]$/;
        var numV=/[0-9]{1,}(\.[0-9]{1,})?/;
        var unitV=/([a-z]{2,}( )?){1,2}/;
        var ingredient, sn=1, ssn=1;
        var string="Hello 1.2 is the test number";
        // ajax.onreadystatechange=()=>{
        //     if(ajax.readyState==4){
        //         if(ajax.status==200){
        //             var response=ajax.responseText;
        //             response=response.split('\\', 2);
        //             if(response[0]!='') {
        //                 il=response[0];
        //                 il=il.split(",,");
        //                 sn=1;
        //                 il.forEach(element => {
        //                     element=element.split("--");
        //                     document.getElementById("ingredients").innerHTML+=`<tr> <td>${sn++} <td>${element[0]}</td> <td>${element[1]}</td> </tr>`;
        //                 });
        //             }if(response[1]!='') {
        //                 sl=response[1];
        //                 sl=sl.split(",,");
        //                 sn=0;
        //                 sl.forEach(element=>{
        //                     document.getElementById('step').innerHTML+=`<li>${element}</li>`;
        //                 });
        //             }
        //         }else {
        //             alert("Error code: "+ajax.status);
        //             alert("Error Message: "+ajax.statusText);
        //         }
        //     }
        // }
        if(document.getElementById("paid").value>0) document.getElementById("cost").value="paid";
        if(document.getElementById("cost").value=="free") document.getElementById("paid").style.display="none";
        document.getElementById("addI").addEventListener("click", ()=>{
            console.log("Hello");
            var i=document.querySelector("input[name=ingredient]").value;
            var q=document.querySelector("input[name=quantity]").value;
            document.getElementById("iErr").innerHTML='';
            document.getElementById("qErr").innerHTML='';
            var qErr='', iErr='';
            ingredient= filter(i);
            q= filter(q);
            filteredQ="";
            filteredI="";
            vi=e=true;
            if(q!=""){
                var vq=validQty(q);
                console.log(vq.toString());
                if(!vq) filteredQ="";
                e=false;
            }
            if(ingredient) {
                vi=validIngredient(ingredient);
                console.log(vi.toString());
                if(filteredQ=='' && !e) qErr="Invalid Quantity";
                else if(filteredQ=='' && e) qErr="Quantity Required";
                else qErr='';
            }else {
                iErr="Ingredient Required";
                vi=true;
            }
            if(!vi) {
                iErr="Invalid Ingredient";
                if(!e && filteredQ=='') qErr="Invalid Quantity";
                else if(filteredQ=='') qErr="Quantity Required";
                else qErr='';
                console.log(filteredI);
            }else {
                iErr="";
                if(!e && filteredQ=='') qErr="Invalid Quantity";
                else if(filteredQ=='') qErr="Quantity Required";
                else qErr='';
            }
            if(il && iErr==''){
                if(il.indexOf(filteredI)>=0) {
                    console.log(il.indexOf(filteredI));
                    iErr="Ingredient already entered";
                }else iErr='';
                console.log("Hello");
            }
            if(!qErr && !iErr){
                il+=`${filteredI}&&${filteredQ},,`;
                document.getElementById("iL").innerHTML+=`<tr> <td>${sn++}</td> <td>${filteredI}</td> <td>${filteredQ}</td> <td><button onclick="deleteRow(this, ${sn-1})"><img src="delete.jpg" class='deleteIcon'"></button></td> </tr>`;
                document.querySelector('input[name=ingredient]').value="";
                document.querySelector('input[name=quantity]').value="";
                document.querySelector("input[name=ingredients]").value=il;
            }else {
                document.getElementById("iErr").innerText=iErr;
                document.getElementById("qErr").innerText=qErr;
            }
            console.log(filteredQ);
            console.log(filteredI+" "+filteredQ);
        });
        document.getElementById("addS").addEventListener("click", ()=>{
            var array=false;
            var steps, matches, unmatch, uml, i, sErr='';
            steps=document.querySelector("textarea[name=step]").value;
            steps=filter(steps);
            console.log(steps);
            if(!/[a-z]+/.test(steps)) {
                sErr="Enter a valid step";
                console.log("Hello");
            }else {
                document.getElementById("sErr").innerHTML='';
                if(/^[0-9]+(\.)+[^0-9]+/.test(steps)){
                    steps=steps.split(/[0-9]+(\.){1}(\D{1})?/);
                    steps=steps.filter(steps=>steps.match(/[a-z]{2,} [a-z]+[a-z_ \-\,]+/));
                    console.log(steps);
                    for (i=0; i<steps.length; i++){
                        steps[i]=filter(steps[i]);
                        matches=steps[i].match(/[a-z]{1}/);
                        if(/[^a-z]{1}/.test(steps[i])) {
                            unmatch=steps[i].match(/[^a-z]{1}/);
                            uml=unmatch.length;
                        }else uml=0;
                        if(!/\D?[a-z]{2,}( [a-z]{2,})+/.test(steps[i]) || matches.length-uml<6) sErr="Invalid step";
                        else sErr='';                     
                    }
                    array=true;
                }else if(/(\.){1}[a-z_ \-\,]{1}/.test(steps)){
                    steps=steps.split(/(\.){1}[a-z_ \-\,]{1}/);
                    console.log("Hello");
                    console.log(steps[0]);
                    steps=steps.filter(steps=>steps.match(/[a-z]{2,} [a-z]{2,}[a-z0-9_ \-\,\']+/));
                    console.log(steps);
                    for (i=0; i<steps.length; i++){
                        steps[i]=filter(steps[i]);
                        matches=steps[i].match(/[a-z]{1}/g);
                        if(/[^a-z]{1}/.test(steps[i])) {
                            unmatch=steps[i].match(/[^a-z]{1}/g);
                            uml=unmatch.length;
                        }else uml=0;
                        if(!/[a-z]{2,} [a-z_ \-\,\']{2,}/.test(steps[i]) || matches.length-uml<6) sErr="Invalid step";
                        else sErr='';                     
                    }
                    array=true;
                }else {
                    uml=0, ml=0;
                    if(/[\d\s_\-\,\']+/.test(steps)) {
                        uml=steps.match(/[\d\s_\-\,\']{1}/g);
                        console.log (uml.length);
                        uml=uml.length;
                    }
                    if(/[a-z]+/.test(steps)){
                        ml=steps.match(/[a-z]{1}/g);
                        ml=ml.length;
                    }
                    if(!/[a-z]{2,} [a-z \'\-\,]{2,}/.test(steps) || ml-uml<6) sErr="Invalid step format";
                    else sErr='';
                }
            }
            if(sErr!='') document.getElementById("sErr").innerHTML=`<br>${sErr}`;
            else {
                if(array) {
                    for(i=0; i<steps.length; i++) {
                        if(sl.indexOf(steps[i])>=0) sErr+=" Step"+i+" is already entered ";
                        else {
                            document.getElementById("step").innerHTML+=`<tr> <td>${ssn}</td> <td>${steps[i]}</td> <td><button onclick='deleteS(this, ${ssn++})'><img src='delete.jpg'></button></td> </tr>`;
                            sl+=steps[i]+',,';
                        }
                    }
                    document.querySelector("input[name=procedure]").value=sl;
                }else {
                    if(sl.indexOf(steps)>=0) sErr=`The step is already entered`;
                    else {
                        document.getElementById("step").innerHTML+=`<tr> <td>${ssn}</td> <td>${steps}</td> <td><button onclick='deleteS(this, ${ssn++})'><img src='delete.jpg'></button></td> </tr>`;
                        sl+=steps+',,';
                        document.querySelector("input[name=instructions]").value=sl;
                    }
                }
                document.querySelector('textarea[name=step]').value='';
            }
        });
        function deleteS(button, pos){
            var el=button.parentNode.parentNode;
            var table=el.parentNode;
            var index=0;
            for (var i=0; i<pos-1; i++){
                index=sl.indexOf(",,", index)+2;
            }
            sl=sl.replace(sl.substring(index, sl.indexOf(',,', index)+2), '');
            table.removeChild(el);
        }
        function change(){
            if(document.getElementById("cost").value=="free") document.getElementById("paid").style.display='none';
            else document.getElementById("paid").removeAttribute("style");
        }
        // document.getElementById("addF").addEventListener("click", (event)=>{
        //     var sil;
        //     if(document.getElementById('file').value=='') document.getElementById("fErr").value="Enter a file";
        //     else {
        //         document.getElementById("form0").innerHTML=(document.getElementById('form0').innerHTML).slice
        //     }
        //     if(files!='') {
        //         fL=files+"--";
        //         if(){
        //             document.getElementById('files').innerHTML+=`<img src='${files}'>`;
        //         }else if(/[(\.mp4)|(\.ogg)]{1}/.test(files)){
        //             document.getElementById("files").innerHTML+=`<video> <source src="${files}">Your browser doesnot support video tag.</video>`;
        //         }
        //         document.querySelector("input[type=file]").value='';
        //     }
        // });
        document.querySelector("button[type=submit]").addEventListener("click", (event)=>{
            var fname=document.querySelector("input[name=name]").value;
            nameErr='';
            fname=filter(fname);
            if(fname=='' || fname.trim()=='') nameErr="Food name required";
            else {
                fname=fname.trim();
                if(!/^[a-z]{2,}[a-z \'\,]*$/.test(fname.trim())) nameErr="Invalid Food name";
                else nameErr='';
            }
            if(nameErr!=''){
                document.getElementById("nameErr").innerHTML=`<br>${nameErr}`;
                event.preventDefault();
            }
            var ingredients=document.querySelector("input[name=ingredients]").value;
            console.log(ingredients);
            if(!ingredients) {
                document.getElementById("iErr").innerHTML=`<br>Ingredients Required`;
                event.preventDefault();
            }else{
                if(ingredients.split(',,').length<=2){
                    document.getElementById("iErr").innerHTML=`<br>At least 2 ingredients required`;
                    event.preventDefault();
                }
            }
            var instructions=document.querySelector("input[name=instructions]").value;
            if(instructions==''){
                document.getElementById("sErr").innerHTML=`<br>Procedure Required`;
                event.preventDefault();
            }else{
                if(instructions.split(",,").length<3 && instructions.length<30){
                    document.getElementById("sErr").innerHTML=`<br>Procedure is too short`;
                    event.preventDefault();
                }
            }
        });
        function deleteRow(button, c){
            var row=button.parentNode.parentNode;
            var index=0;
            sn=c;
            for(var i=1; i<sn; i++) {
                index=il.indexOf(',,', index);
                index+=2;
            }
            il=il.replace(il.substring(index, il.indexOf(',,', index)+2), '');
            row.parentNode.removeChild(row);
        }
        function filter(value){  
            if(!value) return value;          
            else{
                value=(value.toLowerCase()).trim();
                value=value.replace(/[^a-zA-Z0-9_\s\-\'\.\,]/, " ");
                value=value.trim();
            }
            while(!numChar.test(value) && value.length>=1) {
                value=value.substring(1);
                if(value=='') return value;
            }
            while(!numCharI.test(value) && value!="") {
                value=value.substring(0, value.length-1);
                // if(value[])
                if(value=="") return value;
            }
            value=value.replace(/[\t|\s{2,}]/, ' ');
            value=value.replace(/[\- ]{2,}/, '-');
            value=value.replace(/[\'\" ]{2,}/, "'");
            value=value.replace(/[\,]{2,}/, ',');
            return value;
        }
        function validQty(input){
            if(input=='') return false;
            var unit, qty, m=new Array();
            filteredQ=input;
            if(!filteredQ) return false; 
            else if(!validity.test(filteredQ)) return false;
            else{
                m[0]=filteredQ;
                if(filteredQ.indexOf(" to ")>0) m = filteredQ.split(" to ");
                else if(filteredQ.indexOf('-')>0) m=filteredQ.split("-");
                if(m.length==1){
                    if(numV.test(m[0]) && unitV.test(m[0])){
                        var num=m[0].match(numV);
                        unit=m[0].match(unitV);
                        filteredQ=num[0]+` ${unit[0]}`;
                    }else if(numV.test(m[0]) && !unitV.test(m[0])){
                        num=m[0].match(numV);
                        filteredQ=num[0]+" units";
                    }else return false;
                }else if(m.length==2){
                    var i=0;
                    if(!numV.test(m[0]) || !numV.test(m[1])) return false;
                    else if(m[0].match(numV).length>1 || m[1].match(numV).length>1) return false;
                    else qty=filteredQ.match(numV);
                    qty[0]=m[0].match(numV);
                    qty[1]=m[1].match(numV);
                    if(unitV.test(m[0])){
                        unit[i++]=m[0].match(unitV);
                    }
                    if(unitV.test(m[1])){
                        unit[i++]=m[1].match(unitV);
                    }
                    if(i==0) filteredQ=qty[0][0]+'-'+qty[1][0]+" units";
                    else if(i==1) filteredQ=qty[0][0]+'-'+qty[1][0]+" "+unit[0][0];
                    else filteredQ=qty[0][0]+unit[0][0]+"-"+qty[1][0]+unit[1][0];
                }else return false;
            }
            console.log(filteredQ);
            return true;
        }
        function validIngredient(input) {
            var nt;
            valueI=input;
            filteredI=input;
            var ic=/[a-z]{3}[a-z_ ]*(\'s [a-z]{3,})?/;
            var wc=/[a-z]+/;
            var nc=/[0-9]+/;
            var validI=/^[a-z0-9_ \-\']{5,}$/;
            if(nc.test(filteredI)) {
                var numMatch = filteredI.match(nc);
                nt=numMatch.length;
            }else {
                nt=0;
                nc=0;
            }
            if(filteredQ!=''){
                if(nt>1) return false;
            }
            if((filteredI.indexOf("'-")+filteredI.indexOf("_'")+filteredI.indexOf("-'")+filteredI.indexOf("_-"))>-4) return false;
            if(!wc.test(filteredI)) wc=0;
            if(wc!=0) {
                var words=filteredI.match(wc);
                wc=words.length;
            }      
            var candidates=filteredI.match(ic);
            if(!ic.test(filteredI)) ic=0;
            if(filteredQ==""){
                if(ic!=0){
                    if(nt==1){
                        if(wc>1){
                            if(filteredI.indexOf(numMatch[0])<filteredI.indexOf(words[words.length-1]) && filteredI.indexOf(numMatch[0])>filteredI.indexOf(candidates[0])){
                                filteredQ=`${numMatch[0]} ${words[words.length-1]}`;
                                filteredI=candidates[0];
                            }else if(filteredI.substring(filteredI.indexOf(" ", filteredI.indexOf(numMatch[0]))+1, filteredI.length-1).length>0 && filteredI.indexOf(numMatch[0])>=4){
                                filteredQ=`${numMatch[0]} ${filteredI.substring(filteredI.indexOf(" ", filteredI.indexOf(numMatch[0]))+1, filteredI.length-1)}`;
                                filteredI=candidates[0];
                            }else if(filteredI.indexOf(numMatch[0])==0 && filteredI.substring(filteredI.indexOf(words[0])+words[0].length, filteredI.length-1).match(/[a-z]{3,}[a-z_ ]*(\'s [a-z]{3})?/)) {
                                filteredQ=substring(0, filteredI.indexOf(words[0])+words[0].length);
                                var ing=filteredI.substring(filteredQ.length, filteredI.length-1).match(/[a-z]{3,}[a-z_ ]*(\'s [a-z]{3})?/);
                                filteredI=ing[0];
                            }else if(filteredI.indexOf(numMatch[0])+numMatch[0].length == filteredI.length) {
                                filteredQ=numMatch[0]+" units";
                                filteredI=candidates[0];
                            }
                        }else{
                            filteredQ=numMatch[0]+" units";
                            filteredI = candidates[0];
                        }
                    }else if(nt==2){
                        filteredQ=filteredI.substring(indexOf(numMatch[0]), filteredI.indexOf(numMatch[1])+numMatch[1].length-1);
                        if(filteredI.indexOf(candidates[0])<filteredI.indexOf(filteredQ)){
                            if(filteredI.indexOf(filteredQ)+filteredQ.length<filteredI.length){
                                filteredQ=filteredQ+" "+words[words.length-1];
                            }else{
                                var qty=substring(indexOf(numMatch[0]), indexOf(numMatch[1]).match(/[a-z]+/));
                                if(qty.length==1){
                                    filteredQ=`${numMatch[0]}-${numMatch[1]} ${qty[0]}`;
                                }else filteredQ=`${numMatch[0]}-${numMatch[1]} units`;
                            }
                            filteredI=candidates[0];
                        }else if(filteredI.indexOf(candidates[0])>filteredI.indexOf(filteredQ)+filteredQ.length-1) {
                            var ing=filteredI.substring(indexOf(' ', filteredI.indexOf(filteredQ)+filteredQ.length+1), filteredI.length-1).match(/[a-z]{3,}[a-z_ ]*((\'s)? [a-z]{3})?/);
                            if(ing.length>0){
                                filteredQ=filteredI.substring(indexOf(filteredQ), filteredI.indexOf(ing[0])-1);
                                filteredI=ing[0];
                            }else if(filteredI.indexOf(words[0])<filteredQ.length){
                                filteredQ=numMatch[0]+'-'+numMatch[1]+' '+words[0];
                                filteredI=candidates[0];
                            }else {
                                filteredI=candidates[0];
                                filteredQ=filteredQ+" units";
                            }
                        }else{
                            filteredQ=filteredI.substring(filteredI.indexOf(numMatch[1]), filteredI.length-1)
                            filteredI=filteredI.substring(0, filteredI.indexOf(numMatch[1])-1);
                        }
                    }else if(nt==0){
                        filteredI=candidates[0];
                    }else return false;
                }else {
                    if(nt==1 && wc==0){
                        filteredQ=numMatch[0]+" units";
                    }else if(nt==1 && wc==1) filteredQ=numMatch[0]+" "+words;
                    else if(nt==2 && wc==1) filteredQ=numMatch[0]+'-'+numMatch[1]+' '+words;
                    else if(nt==2 && wc==2){
                        if(filteredI.indexOf(numMatch[0])>filteredI.indexOf(words[0]) && filteredI.indexOf(numMatch[1])>filteredI.indexOf(words[0]) && filteredI.indexOf(numMatch[1])>filteredI.indexOf(words[1])){
                            if(words[0]!=words[1]) filteredQ=numMatch[0]+words[0]+'-'+numMatch[1].words[1];
                            else filteredQ=numMatch[0]+'-'+numMatch[1]+" "+words[0];
                        }
                    }
                    return false;
                }
            }else if(ic!=0) filteredI=candidates[0];
            else return false;
            return true;
        }
        function deleteF(e){
            var file=e.parentNode();
            n.getAttribute("src");


        }
    </script>
</body>
</html>