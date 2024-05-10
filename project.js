document.getElementById("searchBtn").addEventListener("click", ()=>{
    var inp=encodeURI(document.getElementById("search").value);
    if(inp==""||inp==null||inp==undefined){
        document.getElementById("notFound").style.display="none";
        document.getElementById("guideForMeal").style.display="none";
        document.getElementById("error").style.display="block";
    }
    else{
        document.getElementById("error").style.display="none";
        getFoodRecipe(inp);
    }
})
document.querySelector("#display1").addEventListener("click", ()=>{
    var mealName=encodeURI(document.querySelector("#display1 .display p").innerHTML);
    console.log("meal is ", mealName);
    getFoodRecipe(mealName);
})
document.querySelector("#display2").addEventListener("click", ()=>{
    var mealName=encodeURI(document.querySelector("#display2 .display p").innerHTML);
    console.log("meal is ", mealName);
    getFoodRecipe(mealName);
})
document.querySelector("#display3").addEventListener("click", ()=>{
    var mealName=encodeURI(document.querySelector("#display3 .display p").innerHTML);
    console.log("meal is ", mealName);
    getFoodRecipe(mealName);
})
document.querySelector("#display4").addEventListener("click", ()=>{
    var mealName=encodeURI(document.querySelector("#display4 .display p").innerHTML);
    console.log("meal is ", mealName);
    getFoodRecipe(mealName);
})
document.querySelector("#display5").addEventListener("click", ()=>{
    var mealName=encodeURI(document.querySelector("#display5 .display p").innerHTML);
    console.log("meal is ", mealName);
    getFoodRecipe(mealName);
})
document.querySelector("#display6").addEventListener("click", ()=>{
    var mealName=encodeURI(document.querySelector("#display6 .display p").innerHTML);
    console.log("meal is ", mealName);
    getFoodRecipe(mealName);
})
async function getFoodRecipe(name){
    await fetch(`https://www.themealdb.com/api/json/v1/1/search.php?s=${name}`)
    .then(Response=>Response.json())
    .then(data=>{
        if(data.meals==null){
            document.getElementById("ingredients").style.display="none"
            document.getElementById("found").style.display="none"
            document.getElementById("guideForMeal").style.display="none";
            document.getElementById("notFound").style.display='block';
        }
        else{
            document.getElementById("guideForMeal").style.display="block";
            document.getElementById("notFound").style.display="none";
            document.getElementById("found").innerHTML=`Recipe for ${data.meals[0].strMeal}`;
            document.getElementById("found").style.display="block";
            document.getElementById("found").style.color="Green";
            document.getElementById("ingredients").style.display="block";
            document.getElementById("ingredients").innerHTML=
                `<tr>
                    <th>Ingredients</th>
                    <th>Amount</th>
                </tr>
                <tr>
                    <td>${data.meals[0].strIngredient1}</td>
                    <td>${data.meals[0].strMeasure1}</td>
                </tr>
                <tr>
                    <td>${data.meals[0].strIngredient2}</td>
                    <td>${data.meals[0].strMeasure2}</td>
                </tr>
                <tr>
                    <td>${data.meals[0].strIngredient3}</td>
                    <td>${data.meals[0].strMeasure3}</td>
                </tr>
                <tr>
                    <td>${data.meals[0].strIngredient4}</td>
                    <td>${data.meals[0].strMeasure4}</td>
                </tr>
                <tr>
                    <td>${data.meals[0].strIngredient5}</td>
                    <td>${data.meals[0].strMeasure5}</td>
                </tr>
                <tr>
                    <td>${data.meals[0].strIngredient6}</td>
                    <td>${data.meals[0].strMeasure6}</td>
                </tr>
                <tr>
                    <td>${data.meals[0].strIngredient7}</td>
                    <td>${data.meals[0].strMeasure7}</td>
                </tr>
                <tr>
                    <td>${data.meals[0].strIngredient8}</td>
                    <td>${data.meals[0].strMeasure8}</td>
                </tr>
                <tr>
                    <td>${data.meals[0].strIngredient9}</td>
                    <td>${data.meals[0].strMeasure9}</td>
                </tr>
                <tr>
                    <td>${data.meals[0].strIngredient10}</td>
                    <td>${data.meals[0].strMeasure10}</td>
                </tr>
                <tr>
                    <td>${data.meals[0].strIngredient11}</td>
                    <td>${data.meals[0].strMeasure11}</td>
                </tr>
                <tr>
                    <td>${data.meals[0].strIngredient12}</td>
                    <td>${data.meals[0].strMeasure12}</td>
                </tr>
                <tr>
                    <td>${data.meals[0].strIngredient13}</td>
                    <td>${data.meals[0].strMeasure13}</td>
                </tr>
                <tr>
                    <td>${data.meals[0].strIngredient14}</td>
                    <td>${data.meals[0].strMeasure14}</td>
                </tr>
                <tr>
                    <td>${data.meals[0].strIngredient15}</td>
                    <td>${data.meals[0].strMeasure15}</td>
                </tr>
                `
            
            document.getElementById("instruction").innerHTML=`Instructions<br>${data.meals[0].strInstructions}`;
        }
    })
}
