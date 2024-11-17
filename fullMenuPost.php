<?php
/*******w******** 
    
    Name: Kareem Russell
    Date: November 10, 2024
    Description: PHP For Menu Page of the CMS.

****************/
require('connect.php');

$menuItemId = filter_input(INPUT_GET, 'menuItemId', FILTER_SANITIZE_NUMBER_INT);

$query = "SELECT menuitem_id, item_name, description, cost 
            FROM menuitems
            WHERE menuitem_id = :menuitem_id";

$statement = $db->prepare($query);
$statement->bindValue(':menuitem_id', $menuItemId, PDO::PARAM_INT);
$statement->execute();
$menuPosts = $statement->fetchAll();

$imageQuery = "SELECT images.image_path, images.menuitem_id
        FROM images
        LEFT JOIN menuitems
        ON images.menuitem_id = :menuitem_id
        WHERE images.image_path LIKE '%_medium%'
        LIMIT 1";

$imageStatement = $db->prepare($imageQuery);
$imageStatement->bindValue(':menuitem_id', $menuItemId, PDO::PARAM_INT);
$imageStatement->execute();
$images = $imageStatement->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php
    foreach($menuPosts as $menuPost){
        echo '<h1>' . $menuPost['item_name'] . '</h1>';
        echo '<p>' . $menuPost['description'] . '</p>';
        echo '<p>Cost: $' .$menuPost['cost'] . '</p>';
    }

    foreach($images as $image){
        if($image['menuitem_id'] == $menuItemId){
            echo '<img class="menuItemImage" src="' . $image['image_path'] . '" alt="Image">';
        }
    }
?>
</body>
</html>