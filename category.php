<?php

/*******w******** 
    
    Name: Kareem Russell
    Date: November 10, 2024
    Description: PHP For Categories of the Blog.

****************/

require('connect.php');
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['category'])){
        $category = $_POST['category'];
            if($category == "menu"){
                
            }
    }
}
?>