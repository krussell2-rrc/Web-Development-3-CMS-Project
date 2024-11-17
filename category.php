<?php

/*******w******** 
    
    Name: Kareem Russell
    Date: November 10, 2024
    Description: PHP For Displaying Categories of the CMS.

****************/

require('connect.php');
require('authenticate.php');

$query = "SELECT category_id, category_name, description FROM categories";
$statement = $db->prepare($query);
$statement->execute();
$categories = $statement->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <a href=""></a>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories</title>
</head>
<body>
    <h1>Categories:</h1>
    <?php
        foreach($categories as $category){
            echo '<ul>';
            echo '<li id="category' . $category['category_id'] . '">' . $category['category_name'] . ': ' . $category['description'] . ' - <a class="editCategoryButton" href="editCategory.php?categoryId=' . $category['category_id'] . '">Edit Category</a></li>';
            echo '</ul>';
        }
    echo '<a class="createCategoryButton" href="createCategory.php">Create New Category</a>';
    ?>
</body>
</html>