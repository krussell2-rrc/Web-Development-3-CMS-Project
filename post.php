<?php

/*******w******** 
    
    Name: Kareem Russell
    Date: November 10, 2024
    Description: PHP For Post Page of the Blog.

****************/

require('authenticate.php');
require('connect.php');
require 'C:\xampp\htdocs\Assignments\WebDevFinalProject\php-image-resize-master\php-image-resize-master\lib\ImageResize.php';
require 'C:\xampp\htdocs\Assignments\WebDevFinalProject\php-image-resize-master\php-image-resize-master\lib\ImageResizeException.php';
use Gumlet\ImageResize;

// Adding posted content to the database
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $imagePath = file_upload_path($_FILES['file']['name']);

    if(strlen($title) >= 1 && strlen($content) >= 1){
        $pagesQuery = "INSERT INTO pages (title, content) values (:title, :content)";
        $imagesQuery = "INSERT INTO images (image_path) values (:imagePath)";
        $pagesStatement = $db->prepare($pagesQuery);
        $imagesStatement = $db->prepare($imagesQuery);
        $pagesStatement->bindValue(':title', $title);
        $pagesStatement->bindValue(':content', $content);
        $imagesStatement->bindValue(':imagePath', $imagePath);

        $pagesStatement->execute();
        $imagesStatement->execute();
    }
}

// Builds a path string that uses slashes appropriate for our OS.
function file_upload_path($original_filename, $upload_subfolder_name = 'uploads'){
    // Getting the name of the current folder
    $current_folder = dirname(__FILE__);

    // Build an array of paths segment names to be joins using OS specific slashes.
    $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];

    // The DIRECTORY_SEPARATOR constant is OS specific.
    return join(DIRECTORY_SEPARATOR, $path_segments);
}

// Checks to see if the file uploaded is an image/pdf then moves it to the uploads subfolder.
function file_is_an_image_or_pdf($temporary_path, $new_path){
    $allowed_file_mime_types = ['image/jpeg', 'image/png'];
    $allowed_file_extensions = ['jpg', 'jpeg', 'png'];

    // Getting the file extension and mime type of the file
    $actual_file_extension = pathinfo($new_path, PATHINFO_EXTENSION);
    $actual_file_mime_type = mime_content_type($temporary_path);

    $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
    $file_mime_type_is_valid = in_array($actual_file_mime_type, $allowed_file_mime_types);

    return $file_extension_is_valid && $file_mime_type_is_valid;
}

    $file_upload_detected = (isset($_FILES['file']) && $_FILES['file']['error'] === 0);
    $upload_error_detected = (isset($_FILES['file']) && $_FILES['file']['error'] > 0);
    $errorMessage = "";

    if($file_upload_detected){
        $filename = $_FILES['file']['name'];
        $temporary_file_path = $_FILES['file']['tmp_name'];
        
        $actual_image_extension = pathinfo($filename, PATHINFO_EXTENSION);
        $actual_image_mime_type = mime_content_type($_FILES['file']['tmp_name']);
        $allowed_image_mime_types = ['image/gif', 'image/jpeg', 'image/png'];

        // Moves the uploaded file to the uploads folder
        $new_file_path = file_upload_path($filename);
        if(file_is_an_image_or_pdf($temporary_file_path, $new_file_path)){
            move_uploaded_file($temporary_file_path, $new_file_path);
        }else{
            $errorMessage = "Only JPG, JPEG, PNG files are allowed.";
        }

        // Checks the uploaded image for "image-ness" 
        // If valid, it duplicates and resizes the image to 400px wide for the medium version
        // and 50px wide for the thumbnail version, then saves both to the uploads folder.
        if(in_array($actual_image_mime_type, $allowed_image_mime_types)){
            $image_name = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);

            $mediumImage = new ImageResize($new_file_path);
            $mediumImage->resizeToWidth(400);
            $mediumImage_filePath = 'uploads/' . $image_name . '_medium' . '.' . $actual_image_extension;
            $mediumImage->save($mediumImage_filePath);

            $thumbnailImage = new ImageResize($new_file_path);
            $thumbnailImage->resizeToWidth(50);
            $thumbnailImage_filePath = 'uploads/' . $image_name . '_thumbnail' . '.' . $actual_image_extension;
            $thumbnailImage->save($thumbnailImage_filePath);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>New Post</title>
</head>
<body>
<div id="postblogpost">
    <nav class="blognav">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="post.php">New Post</a></li>
        </ul>
    </nav>
</div>
    <h1>New Page Post</h1>
    <form method="POST" enctype="multipart/form-data">
    <div class="form-container">
    <label id="titlelabel">Title:</label>
        <input name="title" id="titletextbox" type="text" >
        <label id="contentlabel">Content:</label>
        <textarea name="content" id="contenttextarea"></textarea>
        <input type="radio" name="category" value="menu" id="menubutton">
        <label for="menubutton">New Menu Post</label>
        <input type="file" name="file" id="file">
        <input type="submit" id="submitbutton" value="Create Post">
    </div>
</form>
</body>
</html>