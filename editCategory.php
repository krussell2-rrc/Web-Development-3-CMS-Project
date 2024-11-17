<?php

/*******w******** 
    
    Name: Kareem Russell
    Date: November 10, 2024
    Description: PHP For Creating and Updating Categories of the CMS.

****************/

require('connect.php');
require('authenticate.php');

$categoryId = filter_input(INPUT_GET, 'categoryId', FILTER_SANITIZE_NUMBER_INT);

$query = "SELECT category_id, category_name, description FROM categories";
$statement = $db->prepare($query);
$statement->execute();
$categories = $statement->fetchAll();

if(isset($_POST['updateCategory'])){
    $category_name = filter_input(INPUT_POST, 'category_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $category_description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $categoryId = filter_input(INPUT_POST, 'categoryId', FILTER_SANITIZE_NUMBER_INT);

    if(strlen($category_name) >= 1 && strlen($category_description) >= 1){
        $query = "UPDATE categories 
                  SET category_name = :category_name, description = :description 
                  WHERE category_id = :categoryId";
        $statement = $db->prepare($query);
        $statement->bindValue(':category_name', $category_name);
        $statement->bindValue(':description', $category_description);
        $statement->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);

        $statement->execute();       

        header("Location: category.php");    
    }
}elseif(isset($_POST['deleteCategory'])){
    $query = "DELETE FROM categories WHERE category_id = :categoryId";
    $statement = $db->prepare($query);
    $statement->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
    $statement->execute();

    header("Location: category.php");    

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
</head>
<body>
    <h1>Edit Category</h1>
    <form method="post">
    <?php
        foreach($categories as $category){
            if($category['category_id'] == $categoryId){
                echo '<input type="hidden" name="categoryId" value="' . $category['category_id'] . '">';
                echo '<label id="categoryLabel">Category:</label>';
                echo '<input name="category_name" id="editCategoryName" type="text" value="'. $category['category_name'] . '">';
                echo '<label id="descriptionLabel">Description:</label>';
                echo '<textarea name="description" id="editDescriptionName">' . $category['description'] . '</textarea>';
                echo '<input type="submit" name="updateCategory" id="updateCategoryButton" value="Update">';
                echo '<input type="submit" name="deleteCategory" id="deleteCategoryButton" value="Delete">';
            };
        };
    ?>
    </form>
</body>
</html>
