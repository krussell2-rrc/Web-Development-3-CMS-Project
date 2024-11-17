<?php
/*******w******** 
    
    Name: Kareem Russell
    Date: November 10, 2024
    Description: PHP For Menu Page of the CMS.

****************/
require('connect.php');

// Fetching categories from the database
$categoriesQuery = "SELECT category_id, category_name FROM categories";
$categoriesStatement = $db->prepare($categoriesQuery);
$categoriesStatement->execute();
$categories = $categoriesStatement->fetchAll();

// Getting default menu items without sorting from database
$menuItemQuery = "SELECT menuitem_id, item_name, description, cost FROM menuitems LIMIT 10";

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

// Filtering menu items based on the category from POST
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if (isset($_POST['menuCategoriesDropDown'])) {
        $category_id = filter_input(INPUT_POST, 'menuCategoriesDropDown', FILTER_SANITIZE_NUMBER_INT);

        // Fetching the name of the selected category from the dropdown list.
        $categoryQuery = 
        "SELECT category_name, category_id
        FROM categories
        WHERE category_id = :category_id";

        // Fetching the menu items that correspond with the selected category from the dropdown list
        $menuItemQuery = 
        "SELECT menuitem_id, item_name, description, cost, category_id 
        FROM menuitems
        WHERE category_id = :category_id
        LIMIT 10";

        $imageQuery = 
        "SELECT images.image_path, images.menuitem_id
        FROM images
        LEFT JOIN menuitems ON images.menuitem_id = menuitems.menuitem_id
        WHERE images.image_path LIKE '%_medium%' AND menuitems.category_id = :category_id
        LIMIT 10";

        $menuItemStatement = $db->prepare($menuItemQuery);
        $imageStatement = $db->prepare($imageQuery);
        $categoryStatement = $db->prepare($categoryQuery);
        $imageStatement->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $menuItemStatement->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $categoryStatement->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $menuItemStatement->execute();
        $imageStatement->execute();
        $categoryStatement->execute();
        $menu_items = $menuItemStatement->fetchAll();
        $images = $imageStatement->fetchAll();
        $categoryName = $categoryStatement->fetchColumn();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Cozy Cafe Menu</title>
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
<div class="theCozyCafeMenuBanner">
    <p class ="theCozyCafeMenu">THE COZY CAFÉ MENU</p>
</div>
<div class="thisMonthsMenuBanner">
    <p class ="thisMonthsMenu">THIS MONTH'S MENU:</p>
</div>
<?php
    echo '<form id="categoryForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
    echo '<label id="menuCategoriesLabel" for="menuCategoriesDropDown">Categories:</label>';
    echo '<select name="menuCategoriesDropDown" id="menuCategoriesDropDown">';

    foreach ($categories as $category) {
        echo '<option value="' . $category['category_id'] . '">' . $category['category_name'] . '</option>';
        }
        echo '</select>';
    echo '<input type="submit" id="applyButton" value="Apply">';
    echo '</form>';

    if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        echo '<p>Sorted By:' . $categoryName . '</p>';
    }
?>
</div>
<div class="menuItemsPageContainer">
<?php
    echo '<div class="menuItemImageContainer">';
    foreach($images as $image){
        echo '<div class="menuItem">';
        echo '<a class="menuPostHREF" href="fullMenuPost.php?menuItemId=' . $image['menuitem_id'] . '">';
        echo '<img class="menuItemImage" src="' . $image['image_path'] . '" alt="Image">';
        foreach($menu_items as $menu_item){
            if($image['menuitem_id'] == $menu_item['menuitem_id']){
                echo '<div class="menuItemText">';
                echo '<p class="menuItemCost">' . '$' . $menu_item['cost'] . '</p>';
                echo '<p class="menuItemName">' . $menu_item['item_name'] . '</p>';
                echo '<p class="menuItemDescription">' . $menu_item['description'] . '</p>';
                echo '</div>';
            }
        }
        echo '</div>';
    }
    echo '</div>';
?>
</div>
</body>
</html>