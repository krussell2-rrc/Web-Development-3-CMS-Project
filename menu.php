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

// Getting default menu items without any sorting from the database
$menuItemQuery = "SELECT menuitem_id, item_name, description, cost FROM menuitems LIMIT 15";

$imageQuery = "SELECT images.image_path, images.menuitem_id
        FROM images
        LEFT JOIN menuitems
        ON images.menuitem_id = menuitems.menuitem_id
        WHERE images.image_path LIKE '%_medium%'
        LIMIT 15";
    
$menuItemStatement = $db->prepare($menuItemQuery);
$imageStatement = $db->prepare($imageQuery);
$menuItemStatement->execute();
$imageStatement->execute();
$menu_items = $menuItemStatement->fetchAll();
$images = $imageStatement->fetchAll();

// Handling both category selection and search forms.
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Filtering menu items based on the category from POST
    if(isset($_POST['menuCategoriesDropDown'])){
        $category_id = filter_input(INPUT_POST, 'menuCategoriesDropDown', FILTER_SANITIZE_NUMBER_INT);
        $validated_categoryId = filter_var($category_id, FILTER_VALIDATE_INT);

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

        $menuItemStatement->bindParam(':category_id', $validated_categoryId, PDO::PARAM_INT);
        $imageStatement->bindParam(':category_id', $validated_categoryId, PDO::PARAM_INT);
        $categoryStatement->bindParam(':category_id', $validated_categoryId, PDO::PARAM_INT);

        $menuItemStatement->execute();
        $imageStatement->execute();
        $categoryStatement->execute();

        $menu_items = $menuItemStatement->fetchAll();
        $images = $imageStatement->fetchAll();
        $categoryName = $categoryStatement->fetchColumn();
    }elseif(isset($_POST['menuSearch'])){
        $searchInput = filter_input(INPUT_POST, 'menuSearch', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $category_id = filter_input(INPUT_POST, 'menuSearchCategories', FILTER_SANITIZE_NUMBER_INT);
        $validated_categoryId = filter_var($category_id, FILTER_VALIDATE_INT);
        $searchTerm = "%" . $searchInput . "%";

        if($validated_categoryId == 100){
            $menuItemQuery = 
            "SELECT menuitem_id, item_name, description, cost, category_id
            FROM menuitems
            WHERE item_name LIKE :item_name
            LIMIT 10";
    
            $imageQuery = 
            "SELECT images.image_path, images.menuitem_id
            FROM images
            LEFT JOIN menuitems ON images.menuitem_id = menuitems.menuitem_id
            WHERE images.image_path LIKE '%_medium%' AND menuitems.item_name LIKE :item_name
            LIMIT 10";

            $menuItemStatement = $db->prepare($menuItemQuery);
            $imageStatement = $db->prepare($imageQuery);
            
            $menuItemStatement->bindParam(':item_name', $searchTerm, PDO::PARAM_STR);
            $imageStatement->bindParam(':item_name', $searchTerm, PDO::PARAM_STR);

            $menuItemStatement->execute();
            $imageStatement->execute();
    
            $menu_items = $menuItemStatement->fetchAll();
            $images = $imageStatement->fetchAll();
        }elseif($validated_categoryId != 100){
            $menuItemQuery = 
            "SELECT menuitem_id, item_name, description, cost, category_id
            FROM menuitems
            WHERE item_name LIKE :item_name AND category_id = :category_id
            LIMIT 10";
    
            $imageQuery = 
            "SELECT images.image_path, images.menuitem_id
            FROM images
            LEFT JOIN menuitems ON images.menuitem_id = menuitems.menuitem_id
            WHERE images.image_path LIKE '%_medium%' AND menuitems.item_name LIKE :item_name AND menuitems.category_id = :category_id
            LIMIT 10";

            $menuItemStatement = $db->prepare($menuItemQuery);
            $imageStatement = $db->prepare($imageQuery);

            $menuItemStatement->bindParam(':item_name', $searchTerm, PDO::PARAM_STR);
            $imageStatement->bindParam(':item_name', $searchTerm, PDO::PARAM_STR);
            $menuItemStatement->bindParam(':category_id', $validated_categoryId, PDO::PARAM_INT);
            $imageStatement->bindParam(':category_id', $validated_categoryId, PDO::PARAM_INT);

            $menuItemStatement->execute();
            $imageStatement->execute();
    
            $menu_items = $menuItemStatement->fetchAll();
            $images = $imageStatement->fetchAll();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="main.css">
    <title>Cozy Cafe Menu</title>
</head>
<body>
<div class="header">
<img class="logo" src="defaultimages/cozycafe-logo.png" alt="The Cozy Café Logo">    <nav class="navigation">
    <ul>
        <a href="">HOME</a>
        <a href="">MENU</a>
        <a href="">NEWS</a>
        <a href="">ABOUT US</a>
        </ul>
    </nav>
    <form id="searchForm" action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
        <input type="text" name="menuSearch" id="menuSearch" placeholder="Search">
        <select id="menuSearchCategories" name="menuSearchCategories">
        <option value="100">All</option>
            <?php
                foreach ($categories as $category)
                {
                    echo '<option value="' . $category['category_id'] . '">' . $category['category_name'] . '</option>';
                }
            ?>
        </select>
        <button id="searchButton" type="submit">
            <i style="font-size:15px" class="fa">&#xf002;</i>
        </button>
    </form>
</div>
<div class="theCozyCafeMenuBanner">
    <p class ="theCozyCafeMenu">THE COZY CAFÉ MENU</p>
</div>
<div class="thisMonthsMenuBanner">
    <p class ="thisMonthsMenu">THIS MONTH'S MENU:</p>
</div>
<?php
    echo '<form id="categoryForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
    echo '<select name="menuCategoriesDropDown" id="menuCategoriesDropDown">';
    echo '<option value="" disabled selected>Sort By</option>';
    foreach ($categories as $category){
        echo '<option value="' . $category['category_id'] . '">' . $category['category_name'] . '</option>';
    }
    echo '</select>';
    echo '<input type="submit" id="applyButton" value="Apply">';
    echo '</form>';
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['menuCategoriesDropDown']))
    {
        echo '<p class="sortedBy">' . strtoupper($categoryName) . '</p>';
    }
?>
<div class="menuItemsPageContainer">
<?php
    echo '<div class="menuItemImageContainer">';
    foreach($images as $image){
        echo '<a class="menuPostHREF" href="fullMenuPost.php?menuItemId=' . $image['menuitem_id'] . '">';
        echo '<div class="menuItem">';
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