import "./App.css"
import axios from "axios";
import { useState, useEffect, useCallback } from "react";
function App() {
  const [viewRecipe, setViewRecipe]=useState(false);
  const [viewRecipeData, setViewRecipeData]=useState({});
  const [pp, setPP]=useState(".../server/");
  const [err, setErr]=useState("");
  const [validity, setValidity]=useState("");
  const [uName, setUName]=useState("Error");
  const [searched, setSearched]=useState("");
  const [recipes, setRecipes]=useState([{idMeal:"52785", strMealThumb:"https:\/\/www.themealdb.com\/images\/media\/meals\/wuxrtu1483564410.jpg", strMeal:"Dal fry", strCategory:"Vegetarian", strArea:"Indian"},{idMeal:"52861", strMealThumb:"https://www.themealdb.com/images/media/meals/qtuuys1511387068.jpg", strMeal:"Peanut Butter Cheesecake", strCategory:"Dessert", strArea:"American"},{idMeal:"53089", strMealThumb:"https://www.themealdb.com/images/media/meals/54xzk31760524666.jpg", strMeal:"Syrian Bread", strCategory:"Miscellaneous", strArea:"Syrian"},{idMeal:"52915", strMealThumb:"https://www.themealdb.com/images/media/meals/yvpuuy1511797244.jpg", strMeal:"French Omelette", strCategory:"Miscellaneous", strArea:"French"},{idMeal:"52865", strMealThumb:"https://www.themealdb.com/images/media/meals/xxpqsy1511452222.jpg", strMeal:"Matar Paneer", strCategory:"Vegetarian", strArea:"Indian"}]);
  var cNotFound=false;
  const checkNDispatch=async (recipe,index)=>{
    console.log(recipe, " index: "+index);
    var defaults=["52785", "52861", "53089", "52915", "52865"];
    if(defaults.includes(recipe)) {
      var r=await axios.get("https://www.themealdb.com/api/json/v1/1/lookup.php?i="+recipe).then((res)=>{
        return res.data.meals;
      }).catch((err)=>{
        console.log(err);
        return null;
      });
      if(r) {
        recipe=r[0];
        setViewRecipe(true);
        setViewRecipeData(recipe);
        getRecipes(recipe.strYoutube);
      }
    }else {
      setViewRecipe(true);
      setViewRecipeData(recipes[index]);
      getRecipes(recipes[index].strYoutube);
      console.log(recipes[index]);
    }
  };
  const getCookie=useCallback((cName)=>{
    const cookies=document.cookie;
    var ca=cookies.split("; ");
    var returnS="not found";
    for(var i=0; i<ca.length; i++){
      if(ca[i].indexOf(`${cName}=`==0)) {
        returnS=ca[i].substring(cName.length+1, ca[i].length);
        break;
      }
    }
    cNotFound=true;
    return returnS;
  },[]);
  const searchRecipe=useCallback(async (e)=>{
    e.preventDefault();
    if(searched=="") {
      setRecipes([{idMeal:"52785", strMealThumb:"https:\/\/www.themealdb.com\/images\/media\/meals\/wuxrtu1483564410.jpg", strMeal:"Dal fry", strCategory:"Vegetarian", strArea:"Indian"},{idMeal:"52861", strMealThumb:"https://www.themealdb.com/images/media/meals/qtuuys1511387068.jpg", strMeal:"Peanut Butter Cheesecake", strCategory:"Dessert", strArea:"American"},{idMeal:"53089", strMealThumb:"https://www.themealdb.com/images/media/meals/54xzk31760524666.jpg", strMeal:"Syrian Bread", strCategory:"Miscellaneous", strArea:"Syrian"},{idMeal:"52915", strMealThumb:"https://www.themealdb.com/images/media/meals/yvpuuy1511797244.jpg", strMeal:"French Omelette", strCategory:"Miscellaneous", strArea:"French"},{idMeal:"52865", strMealThumb:"https://www.themealdb.com/images/media/meals/xxpqsy1511452222.jpg", strMeal:"Matar Paneer", strCategory:"Vegetarian", strArea:"Indian"}]);
      return;
    }
    try {
      const res=await axios.get("https://www.themealdb.com/api/json/v1/1/search.php?s="+searched);
      if(res.data.meals=="no data found" || !res.data.meals) setRecipes([]);
      setRecipes(res.data.meals);
    } catch (error) {
      console.log(error);
    }
  });
  const getRecipes=useCallback((link)=>{ 
    try {
      var strYoutube=link;
      if(link) strYoutube=strYoutube.replace("watch?v=", "embed/");
      setViewRecipeData({...viewRecipeData, strYoutube:strYoutube});
      console.log(viewRecipeData.strYoutube);
    } catch (error) {
      console.log(error);
      setViewRecipeData({...viewRecipeData, strYoutube:"/error"});
    }
  },[]);
  var getUrl=location.href;
  var getRequest=getUrl.substring(getUrl.indexOf("?")+1, (getUrl.indexOf("#")>10)? getUrl.indexOf("#"):getUrl.length );
  var user=getRequest.substring(getRequest.indexOf("a=")+2, (getRequest.indexOf("&",getRequest.indexOf("a="))>0)?getRequest.indexOf("&", getRequest.indexOf("a=")):getRequest.length);
  var userC=getCookie('user');
  const find=useCallback(async (user)=>{
    try {
      const res=await axios.post("http://localhost:5000/find",{user:user});
      setPP(pp+res.data.pp);
      setValidity(res.data.validity);
      setUName(res.data.uName);
    } catch (err) {
      setErr(err+" occured");
    }
  },[]);
  if(user==userC) find(user);
  else console.log("User is "+user+"\n Cookie is "+userC);
  return (
    <>
      <span>{err? <br />:""}{err}{err? <br />:""}</span>
      <nav className="flex flex-row w-full h-15 bg-blue-900/50 text-blue-950 text-center flex items-center text-xl">
        <a href="#" className="w-15 h-12 border-red-900 rounded-xl text-xs"><img src={pp} alt="Profile"/><br />{uName}</a><br />
        <a href="#" onClick={(e)=>{e.preventDefault(); setViewRecipe(false); setSearched("")}}>Home</a>
        <input type="search" className="w-[40%] h-10 text-xl bg-white" value={searched} onChange={(e)=>{setSearched(e.target.value)}} />
        <input type="submit" className="bg-green-500 p-3 rounded-xl sm:ml-1 lg:ml-10" value="Search" onClick={searchRecipe} />
      </nav>
      <span>{validity? "Validity: "+validity:""}</span>
      {cNotFound? <div>{document.cookie}</div>:""}
      <div className={`grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-10 gap-y-10 items-center justify-center gap-5 mt-5 ${viewRecipe? "hidden":"block"}`}>
        {recipes? recipes.map((recipe,index)=>(          
          <div key={index} className="w-100 h-70 rounded-xl bg-yellow-500 min-w-[200px] max-w-[300px] min-h-[160px] max-h-[260px] flex items-center justify-center">
            <a href={`https://www.themealdb.com/api/json/v1/1/lookup.php?i=${recipe.idMeal}`} key={index} onClick={(e)=>{e.preventDefault(); checkNDispatch(recipe.idMeal, index)}} className="w-full h-full bg-green-500 flex flex-col items-center justify-center">
            <img src={(recipe.strMealThumb)? recipe.strMealThumb:""} className="w-90 h-50" alt="No image" />
            <span className="text-xl">{recipe.strMeal}</span>
            <span className="text-xs">{recipe.strCategory+", \n"+recipe.strArea+" food"}</span>
            </a>
          </div>  
        )): <div className="w-100 h-70 bg-yellow-500 flex items-center justify-center"><span className="text-xl">No recipes found</span></div>}
      </div>
      <div id="display" className={viewRecipe? "block":"hidden"}>
        <h1 className="text-center text-3xl m-10">{viewRecipeData.strMeal}</h1>
        <div className="flex flex-row items-center justify-center gap-5">
        <img src={viewRecipeData.strMealThumb} alt="No image" className="w-1/4 h-auto" />
        <a href={viewRecipeData.strYoutube} target="_new"><iframe src={viewRecipeData.strYoutube} title="YouTube video player" frameBorder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowFullScreen>Watch on youtube</iframe></a>
        </div>
        <div className={`flex flex-row items-start justify-start gap-5 mt-5`}>
          <div className="bg-gray-200 p-5 rounded-lg">
            <h2 className="text-xl">Ingredients</h2>
            <ol>{[...Array(20)].map((_,i)=>{
              var ingredient=viewRecipeData["strIngredient"+(i+1)];
              var measure=viewRecipeData["strMeasure"+(i+1)];
              if(ingredient && ingredient.trim()!="") return <li key={i}>{ingredient+" - "+measure}</li>
            })}</ol>
          </div>
          <div className="bg-blue-200 p-5 rounded-lg whitespace-pre-wrap">
            <h2 className="text-xl">Instructions</h2>
            <p>{viewRecipeData.strInstructions}</p>
          </div>

        </div>
      </div>
    </>
  );
}

export default App;
