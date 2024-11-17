<?php

/*******w******** 
    
    Name: Kareem Russell
    Date: November 10, 2024
    Description: PHP For Creating Categories for the CMS.

****************/

require('connect.php');
require('authenticate.php');

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $category_name = filter_input(INPUT_POST, 'categoryName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $category_description = filter_input(INPUT_POST, 'categoryDescription', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if(strlen($category_name) >= 1 && strlen($category_description) >= 1){
        $query = "INSERT INTO categories(category_name, description) VALUES (:category_name, :description)";
        $statement = $db->prepare($query);
        $statement->bindValue(':category_name', $category_name);
        $statement->bindValue(':description', $category_description);

        $statement->execute();
        header("Location: category.php");    
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="post">
        <h1>New Category</h1>
        <label for="categoryName">Category Name:</label>
        <input name="categoryName" id="createCategoryName" type="text">
        <label for="categoryDescription">Description:</label>
        <input name="categoryDescription" id="createCategoryDescription" type="text">
        <input type="submit" name="createCategory" id="createCategoryButton" value="Create">
    </form>
</body>
</html>