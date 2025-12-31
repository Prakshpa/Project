<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    include "Connection.php";
    $recipes="";
    $sql="SELECT `id`, `Food name`, `Help files`, Author FROM Recipes";
    $qry=mysqli_query($conn, $sql);
    if(!$qry) echo mysqli_error($conn);
    else{
        $sn=1;
        while($row=mysqli_fetch_assoc($qry)){
            $picture=explode(",,", $row['Help files']);
            $recipes.="<tr>
                <td>$sn</td>
                <td><a href='viewRecipe.php?recipe=".$row['id']."'>".$row['Food name']."<img src='$picture[0]'></a></td>
                <td>".$row["Author"].`</td>
                <td><button type="button" onclick='delete><img src="delete.jpg" class="delete"></button> </td>
                </tr>`;
                $sn++;
        }
    }
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav id="search">
        <select name="cat" id="">
            <option value="cat" disabled>Food Category</option>
            <input type="search" name="search" id="name">
            <button type="button">Search</button>
        </select>
    </nav>
    <table id="recipeView">
        <caption id="recipeCat">All Recipes</caption>
        <tr>
            <th id="sn">SN</th>
            <th id="rName">Recipe</th>
            <th id="creator">Creator</th>
            <th id="delete"></th>
        </tr>
        <?php echo $recipes;?>
    </table>
</body>
</html>