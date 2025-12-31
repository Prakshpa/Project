<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    include "Connection.php";
    $recipes=$result=$category=$sn="";
    if(isset($_GET['usn'])) {
        $sn=$_GET['usn'];
        $result=$conn->query("SELECT `id`, `searches` FROM users WHERE `id`=$sn");
        if(!$result) echo mysqli_error($conn);
        elseif(mysqli_num_rows($result)==0) echo "Unidentified user";
        $result=$result->fetch_assoc();
        setcookie("user", $sn, time()+1000);
        setcookie("A", $result['searches'], time()+1000);
    }else echo "Login for better experience.";
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="content">
        <div id="activities">
            <a class="option" href="addRecipe.php?usn=<?php echo $sn;?>"><img src="" alt="+"><br>Add Recipe</a>
        </div>
        <section id="browse">
            <span id="flex">
                <input type="text" placeholder="Type category"><br>
                <select name="categories" id="foodCat">
                    <option value="" disabled>Food category</option>
                    <option value="Recommend">Recommend</option>
                    <option value="Trending">Trending</option>
                    <option value="Free">Cheapest</option>
                    <option value="Latest" selected>Latest</option>
                    <option value="Oldest">Oldest</option>
                </select>
            </span>
            <span id="search">
                <input type="search" name="food" id="food">
                <input type="button" name="" id="searchRecipe" value="Search">
            </span>
        </section><br>
        <h3 id="category">Popular dishes</h3>
        <div id="recipes">
            <a href="" class="dish">empty</a>
            <a href="" class="dish">empty</a>
            <a href="" class="dish">empty</a>
            <a href="" class="dish">empty</a>
            <a href="" class="dish">empty</a>
        </div> 
    </div>
    <script type="text/javascript">
        const A=getcookie("A"), user=getcookie("user");
        console.log(user);
        var temp=new Array(), temp1,  temp2, temp3=new Array(), foods, temp4;
        var cat="", food="", fn=0, mn=0;
        var ajax=new XMLHttpRequest();
        var foodList="";
        var recipe=new Array(), picture=new Array(), pick="";
        function getcookie(name){
            name=name+"=";
            var ca=document.cookie.split("; ");
            for(var i=0; i<ca.length; i++){
                let c=ca[i];
                if(c.indexOf(name)==0) {
                    console.log(c);
                    return decodeURIComponent(c.substring(name.length, 200));
                }
            }
        }
        function setcookie(name, value, time){
            name=name+"=";
            var ca=document.cookie.split(";");
            for(var i=0; i<ca.length; i++){
                let c=ca[i].trim();
                if(c.indexOf(name)>=0){
                    continue;
                }
                
            }
        }
        ajax.onreadystatechange=()=>{
            if(ajax.readyState==4){
                if(ajax.status==200){
                    foodList=ajax.responseText;
                    if(foodList.length<=10) return;
                    temp1=foodList.split("$$");
                    fn=temp1.length;
                    temp2=foodList.split("$$");
                    temp4=foodList.split("$$");
                    // debugger;
                    for(var i=0; i<fn; i++){
                        temp[i]=temp1[fn-1-i];
                        for(var j=fn-1; j>i; j--){
                            var foods2=temp2[j].split("--").concat(temp2[j-1].split("--"));
                            if(foods2[5]>foods2[11]){ 
                                var tempFood=temp2[j-1];
                                temp2[j-1]=temp2[j];
                                temp2[j]=tempFood;
                            }
                            foods2=temp4[j].split("--").concat(temp4[j-1].split("--"));
                            if(foods2[4]<foods2[10]){
                                var tempFood=temp4[j];
                                temp4[j]=temp4[j-1];
                                temp4[j-1]=tempFood;
                            }
                        }
                    }
                    foods=temp1;
                    searchRecipes(true);
                    document.getElementById("category").innerHTML=document.getElementById("foodCat").value;
                    var n=10;
                    for(var i=0; i<n && i<fn; i++){
                        recipe=foods[i].split("--");
                        picture=recipe[2].split(",,");
                        pick="";
                        for(var j=0; j<picture.length; j++){
                            if(/.jpg$/i.test(picture[j]) || /.jpeg$/i.test(picture[j]) || /.png$/i.test(picture[j]) || /.psd$/i.test(picture[j])){
                                pick=picture[j];
                                break;
                            }
                        }
                        if(i==0) document.getElementById("recipes").innerHTML=`<a href="viewRecipe.php?recipe=${recipe[0]}&&user=${user}"><img src="${pick}"><br>${recipe[1]}<br>price:${recipe[4]}</a>`;
                        else document.getElementById("recipes").innerHTML+=`<a href="viewRecipe.php?recipe=${recipe[0]}&user=${user}"><img src="${pick}"><br>${recipe[1]}<br>price:${recipe[4]}</a>`;
                    }
                }
            }
        }
        ajax.open("GET", "RequestHandle.php?view=1", true);
        ajax.send();
        var element=document.getElementById("foodCat")
        element.addEventListener("change", ()=>{
            if(element.value=="Latest"){
                foods=temp1;
            }else if(element.value=="Oldest"){
                foods=temp;
            }else if(element.value=="Trending"){
                foods=temp2;
            }else if(element.value=="Recommend"){
                console.log(temp3);
                foods=temp3;
            }else if(element.value=="Free"){
                foods=temp4;
            }
            console.log(foods);
            searchRecipes();
        });
        document.getElementById("searchRecipe").addEventListener("click", function(e){
            e.preventDefault();
            searchRecipes();
        });
        function searchRecipes(a=false){
            var searches=A;
            var allMatch=",";
            if(searches=="") searches="Default";
            if(searches.indexOf("<foodName>")<0){
                searches=searches+"<foodName>";
            }
            searches=searches.split("<foodName>",2);
            if(a){
                    food=(searches[1])? searches[1].trim().toLowerCase():"0";
                    cat=(searches[0])? searches[0].trim().toLowerCase():"0";
            }
            else{
                cat=document.querySelector("input[placeholder]").value;
                cat=(cat!="")? cat.trim().toLowerCase():"";
                console.log(cat);
                food=document.getElementById("food").value;
                food=(food)? food.trim().toLowerCase():"";
                console.log(food); 
            }
            if(cat!="" || food!=""){
                var matches=new Array(fn);
                matches.fill(0,0,fn);
                var matched=new Array(fn);
                matched.fill(0,0,fn);
                var matching=new Array(fn);
                matching.fill(-1,0,fn);
                var fmatches=new Array(fn);
                fmatches.fill(0,0,fn);
                var fmatched=new Array(fn);
                fmatched.fill(-1,0,fn);
                var i=0, k=0;
                if(cat!=""){
                    var catt=new Array();
                    if(cat.length>5){
                        catt=cat.split(/[ \,]+/);
                    }else catt[0]=cat;
                    cat=catt;
                    document.getElementById("category").innerHTML=document.getElementById("foodCat").value+" of the category";
                    if(fn>0){
                        foods.forEach(food => {
                            food=food.split("--");
                            var foodCat=food[3].toLowerCase();
                            for(var j=0; j<cat.length; j++){
                                if(foodCat.indexOf(cat[j])>=0){
                                    matches[i]++;
                                    console.log("Hello"+matches[i]);
                                }
                                if(matches[i]>0 && j==cat.length-1) {
                                    matched[i]=food[0];
                                    matching[i++]=k;
                                }
                                if(k==0){
                                    if(searches[0].indexOf(cat[j])>=0) searches[0].replace(cat[j], ""); 
                                    searches[0]+=" "+cat[j];
                                }
                            }
                            k++;
                        });
                        mn=i;
                        for(var j=0; j<i-1, matches[j]>0; j++){
                            if(matches[j]<matches[j+1]){
                                var temp=matches[j];
                                var temp1=matched[j];
                                var temp2=matching[j];
                                matches[j]=matches[j+1];
                                matches[j+1]=temp;
                                matched[j]=matched[j+1];
                                matched[j+1]=temp1;
                                matching[j]=matching[j+1];
                                matching[j+1]=temp2;
                            }
                            if(j==i-2){
                                i--;
                                j=-1;
                            }
                        }
                        if(a) for(i=0; i<mn; i++){
                            temp3[i]=foods[matching[i]];
                            allMatch+=`${matching[i]},`;
                        }
                    }
                }
                if(food!=''){
                    var j=0;
                    var fmatching=new Array();
                    fmatching.fill(0);
                    k=0;
                    foods.forEach(single=>{
                        single=single.split("--", 4);
                        console.log(single[1]);
                        if(single[1].toLowerCase().indexOf(food)>=0){
                            fmatched[k]=j;
                            fmatches[k++]=single[0];
                            var singles=searches[1].split(",");
                            var i;
                            for(i=0; i<singles.length; i++){
                                if(singles[i].indexOf(food)>=0){
                                    var temp=singles[i];
                                    for(var j=i; j<singles.length-1; j++) singles[j]=singles[j+1];
                                    singles[singles.length-1]=temp;
                                    searches[1]=singles.join(",");
                                    break;
                                }
                            }
                            if(i==singles.length){
                                // var foodb=single[1].substring(0,single[1].indexOf(food));
                                var foodt=single[1].substring(single[1].lastIndexOf(",",single[1].indexOf(food)), single[1].indexOf(",",single[1].indexOf(food)+food.length));
                                single[1].replace(foodt, "");
                                single[1]+=","+foodt;
                            }
                        }else if(!a){
                            var temp=food.split(/[ ,]+/);
                            var add="",a=0;
                            temp.forEach(word=>{
                                console.log(word);
                                if(searches[1].indexOf(word)>=0){
                                    searches[1].replace(word, "");
                                }
                            });
                            searches[1]+=","+food;
                        }else{
                            var food_div=single[1].split(/[ ,]+/);
                            for(var i=0; i<food_div.length; i++){
                                if(food.indexOf(food_div[i])>=0){
                                    fmatching[k]++;
                                }
                                if(i==food_div.length-1 && fmatching[k]>0){
                                    fmatches[k]=single[0];
                                    fmatched[k++]=j;
                                }
                            }
                        }
                        j++;
                    });
                    if(a){
                        for(var i=0; i<k; i++){
                            for(var j=k-1; j>i; j--){
                                if(fmatching[j]>fmatching[j-1]){
                                    var tempes=fmatches[j];
                                    var temped=fmatched[j];
                                    var temping=fmatching[j-1];
                                    fmatching[j-1]=fmatching[j];
                                    fmatching[j]=temping;
                                    fmatches[j]=fmatches[j-1];
                                    fmatches[j-1]=tempes;
                                    fmatched[j]=fmatched[j-1];
                                    fmatched[j-1]=temped;
                                }
                            }
                        }
                        var l=0, m=mn;
                        var mmatched=new Array(k);
                        mmatched.fill(-1, 0, k);
                        for(var i=0; i<k || i==0; i++){
                            if(k==0) continue;
                            for(var j=0; j<mn; j++){
                                if(matching[j]==fmatched[i]){
                                    var temp=temp3[l];
                                    temp3[l++]=temp3[fmatched[i]];
                                    for(var n=l; n<=j; n++){
                                        var temp1=temp3[n];
                                        temp3[n]=temp;
                                        temp=temp1;
                                    }
                                    continue;
                                }
                                if(j==mn-1){
                                    temp3[m++]=foods[fmatched[i]];
                                    allMatch+=`${fmatched[i]},`;
                                }
                            }
                        }
                        for(var i=0; i<fn && m<fn; i++){
                            if(allMatch.indexOf(`,${i},`)<0) temp3[m++]=foods[i];
                        }
                        return;
                    }
                }
                if(food==''){
                    var max=i;
                    console.log("No food name"+i);
                    if(matches[0]>0) {
                        console.log(matching[0]);
                        recipe=foods[matching[0]].split("--");
                        picture=recipe[2].length>=7? recipe[2].split(",,"):"";
                        pick="Capture.JPG";
                        for(var c=0; c<picture.length, recipe[2].length>=7; c++){
                            if(/.jpg$/i.test(picture[c]) || /.png$/i.test(picture[c]) || /.jpeg$/.test(picture[c]) || /.psd$/i.test(picture[c])){
                                pick=picture[c];
                                break;
                            }
                        }
                        document.getElementById("recipes").innerHTML=`<a href="viewRecipe.php?user=${user}&recipe=${matched[0]}"><img src="${pick}"><br>${recipe[1]}<br>price:${recipe[4]}</a>`;
                    }
                    console.log("Max "+max);
                    for(var i=1; i<max; i++){
                        if(matches[i]>0){
                            console.log("More"+matching[i]);
                            recipe=foods[matching[i]].split("--");
                            pick="Capture.jpg";
                            if(recipe[2].length>=7){
                                picture=recipe[2].split(",,");
                                for(var c=0; c<picture.length, recipe[2].length>=7; c++){
                                    if(/.jpg$/i.test(picture[c]) || /.jpeg$/i.test(picture[c]) || /.png$/i.test(picture[c]) || /.psd$/.test(picture[c])){
                                        pick=picture[c];
                                        break;
                                    }
                                }
                            }
                            document.getElementById("recipes").innerHTML+=`<a href="viewRecipe.php?user=${user}&recipe=${matched[i]}><img src=${pick}><br>${recipe[i]}<br>price:${recipe[4]}</a>`;
                        }else break;
                    }
                }else if(cat=="" || cat.length==0){
                    if(fmatches[0]>0){
                        console.log("Clear");
                        console.log(fmatched[0]+" matched");
                        recipe=foods[fmatched[0]].split('--');
                        picture=recipe[2].length>=5? recipe[2].split(",,"):"";
                        pick="Capture.JPG";
                        for(var c=0; c<picture.length && recipe[2].length>=7; c++){
                            if(/.jpg$/i.test(picture[c]) || /.jpeg$/.test(picture[c]) || /.png$/i.test(picture[c]) || /.psd$/i.test(picture[c])){
                                pick=picture[c];
                                break;
                            }
                        }
                        document.getElementById("recipes").innerHTML=`<a href="viewRecipe.php?user=${user}&recipe=${fmatches[0]}"><img src="${pick}"><br>${recipe[1]}<br>price:${recipe[4]}</a>`;
                    }
                    for(var i=1; i<fmatches.length && fmatched[i]!=-1; i++){
                        console.log(i);
                        recipe=foods[fmatched[i]].split("--");
                        picture=recipe[2].length>=5? recipe[2].split(",,"):"";
                        pick="Capture.jpg";
                        for(var j=0; j<picture.length, recipe[2].length>=7; j++){
                            if(/.jpg$/i.test(picture[j]) || /.jpeg$/i.test(picture[j]) || /.png$/i.test(picture[j]) || /.psd$/i.test(picture[j])){
                                pick=picture[j];
                                break;
                            }
                        }
                        document.getElementById("recipes").innerHTML+=`<a href="viewRecipe.php?user=${user}&recipe=${fmatches[i]}"><img src="${pick}"><br>${recipe[1]}<br>price:${recipe[4]}</a>`;
                    }
                }else{
                    var mmatched=new Array(fn);
                    var mmatching=new Array(fn);
                    var j=0;
                    for(var i=0; i<matches.length && matches[i]>0; i++){
                        mmatched[j]=fmatches.find((value, i, matched)=>{return value==matched[i]},);
                        if(mmatched[j]>=0) {
                            console.log(mmatched[i], matching[i]);
                            mmatching[j]=matching[i];
                            j++;
                            console.log(fmatched);
                        }
                    }
                    if(mmatched[0]>0){
                        recipe=foods[mmatching[0]].split("--");
                        picture=recipe[2].length>=5? recipe[2].split(",,"):"";
                        pick="";
                        for(var i=0; i<picture.length && recipe[2].length>=7; i++){
                            if(/.jpg$/i.test(picture[i]) || /.jpeg$/i.test(picture[i]) || /.png$/i.test(picture[i]) || /.psd$/i.test(picture[i])){
                                pick=picture[i];
                                break;
                            }
                        }
                        document.getElementById("recipes").innerHTML=`<a href="viewRecipe.php?user=${user}&recipe=${mmatched[0]}"><img src="${pick}"><br>${recipe[1]}<br>price:${recipe[4]}</a>`;
                    }
                    for(var i=1; i<mmatched.length && mmatched[i]>0; i++){
                        if(mmatching[i]>0){
                            recipe=foods[mmatching[i]].split("--");
                            picture=recipe[2].length>=5? recipe[2].split(",,"):"";
                            pick='';
                            for(var j=0; j<picture.length && recipe[2].length>=7; j++){
                                if(/.jpg$/i.test(picture[j]) || /.jpeg$/i.test(picture[j]) || /.png$/i.test(picture[j]) || /.psd$/i.test(picture[j])){
                                    pick=picture[j];
                                    break;
                                }
                            }
                            document.getElementById("recipes").innerHTML+=`<a href="viewRecipe.php?user=${user}&recipe=${recipe}"><img src="${pick}"><br>${recipe[1]}<br>price:${recipe[4]}</a>`;
                        }else break;
                    }
                }
                searches[0]=filter(searches[0]);
                searches[1]=filter(searches[1]);
                // setcookie("searches", searches.join("<foodName>"),time()+1000000);
                document.cookie="searches="+searches.join("<foodName>");
                console.log(document.cookie);
            }else{
                showRecipes();
            }
        }
        function filter(value){  
            // var validity=/^[a-zA-Z0-9_ \-\'\.\,]+$/;
            var numChar=/^[a-zA-Z0-9]/;
            var numCharI=/[a-zA-Z0-9]$/;
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
            value=value.replace(/[\' ]{2,}/, "'");
            value=value.replace(/[\,]{2,}/, ',');
            return value;
        }
        function showRecipes(){
            document.getElementById("category").innerHTML=document.getElementById("foodCat").value;
            var n=10;
            for(var i=0; i<n && i<fn; i++){
                recipe=foods[i].split("--");
                picture=recipe[2].split(",,");
                pick="";
                for(var j=0; j<picture.length; j++){
                    if(/.jpg$/i.test(picture[j]) || /.jpeg$/i.test(picture[j]) || /.png$/i.test(picture[j]) || /.psd$/i.test(picture[j])){
                        pick=picture[j];
                        break;
                    }
                }
                if(i==0) document.getElementById("recipes").innerHTML=`<a href="viewRecipe.php?recipe=${recipe[0]}&&user=${user}"><img src="${pick}"><br>${recipe[1]}<br>price:${recipe[4]}</a>`;
                else document.getElementById("recipes").innerHTML+=`<a href="viewRecipe.php?recipe=${recipe[0]}&user=${user}"><img src="${pick}"><br>${recipe[1]}<br>price:${recipe[4]}</a>`;
            }
        }
        // class NewArray extends Array{
        //     arr=new Array();
            
        //     NewArray(arr=new Array()){
        //         this.arr=arr;
        //     }

        //     find(a){
        //         for(var i=0; i<arr.length; i++){
        //             if(arr[i]==a) return true;
        //         }
        //         return false;
        //     }
        // }
    </script>    
</body>
</html>