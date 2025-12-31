<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php
    include ("Connection.php");
    $sql="SELECT `id`, `Full Name`, login, role FROM `Users` ORDER BY role DESC, `Full Name` ASC, `id` DESC";
    $query=mysqli_query($conn, $sql);
    ?>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav id="search">
        <select name="cat" id="">
            <input type="search" name="searchM" id="name">
            <button type="button">Search Member</button>
        </select>
    </nav>
    <table id="members">
        <caption>All Members</caption>
        <tr>
            <th>S.N.</th>
            <th>Name</th>
            <th>Login</th>
            <th>Position</th>
            <th>Delete</th>
        </tr>
        <?php
        if($query){
            
            while($row=mysqli_fetch_assoc($query)){
                $a=$row['id'];
                $position=$row['role'];
                echo '<tr>
                <td>'.$row['id'].'</td>
                <td>'.$row['Full Name'].'</td>
                <td>'.$row['login']."</td>
                <td>$position</td>
                <td><button type=".'"button" name="delete" value="'.$a.'"><img id="deleteIcon" src="delete.jpg" alt="delete"></button>
                </td></tr>';
            }
        }
        ?>
    </table>
</body>
</html>