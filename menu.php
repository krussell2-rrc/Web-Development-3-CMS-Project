<?php
/*******w******** 
    
    Name: Kareem Russell
    Date: November 10, 2024
    Description: PHP For Post Page of the Blog.

****************/
require('connect.php');

$menuItemQuery = "SELECT menuitems.menuitem_id, menuitems.item_name, menuitems.description FROM menuitems LIMIT 10";

$imageQuery = "SELECT images.image_path, images.menuitem_id
        FROM images
        LEFT JOIN menuitems
        ON images.menuitem_id = menuitems.menuitem_id
        WHERE images.image_path LIKE '%_medium%'
        LIMIT 10";

$menuItemStatement = $db->prepare($menuItemQuery);
$imageStatement = $db->prepare($imageQuery);
$menuItemStatement->execute();
$imageStatement->execute();
$menu_items = $menuItemStatement->fetchAll();
$images = $imageStatement->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Document</title>
</head>
<body>
<a href="">
    <img class="logo" src="defaultimages/cozycafe-logo.png" alt="The Cozy Café Logo">
</a>
<div class="header">
    <nav class="navigation">
        <ul>
            <a href="">HOME</a>
            <a href="">MENU</a>
            <a href="">NEWS</a>
            <a href="">ABOUT US</a>
        </ul>
    </nav>
</div>
<div class="donutbanner">
    <img class="donutbannerimg" src="defaultimages/menubanner2.png" alt="">
</div>
<div class="menuItemsContainer">
<?php
    foreach($images as $image){
        echo '<ul class="menuItemList">';
        echo '<li class="menuItemName">' . '<img src="' . $image['image_path'] . '"alt="">' . '</li>';
        foreach($menu_items as $menu_item){
            if($image['menuitem_id'] == $menu_item['menuitem_id']){
                echo '<li class="menuItemName">' . $menu_item['item_name'] . '</li>';
                echo '<li class="menuItemDescription">' . $menu_item['description'] . '</li>';
            }
        }
        echo '</ul>';
    }
?>
</div>
</body>
</html>