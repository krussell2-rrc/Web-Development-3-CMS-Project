<!DOCTYPE html>
<?php

/*******w******** 
    
    Name: Kareem Russell
    Date: November 10, 2024
    Description: PHP For Post Page of the CMS.

****************/

require('authenticate.php');
require('connect.php');
session_start();

require 'C:\xampp\htdocs\Assignments\WebDevFinalProject\php-image-resize-master\php-image-resize-master\lib\ImageResize.php';
require 'C:\xampp\htdocs\Assignments\WebDevFinalProject\php-image-resize-master\php-image-resize-master\lib\ImageResizeException.php';
use Gumlet\ImageResize;
// Fetching categories from the database
$categoriesQuery = "SELECT category_id, category_name FROM categories";
$categoriesStatement = $db->prepare($categoriesQuery);
$categoriesStatement->execute();
$categories = $categoriesStatement->fetchAll();

// Adding posted content to the database
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $content = $_POST['content'];
    $cost = filter_input(INPUT_POST, 'menuItemCostInput', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $validated_cost = filter_var($cost, FILTER_VALIDATE_FLOAT);
    $category_id = filter_input(INPUT_POST, 'categoriesDropDown', FILTER_SANITIZE_NUMBER_INT);
    $validated_categoryId = filter_var($category_id, FILTER_VALIDATE_INT);
    $imagePath = file_upload_path($_FILES['file']['name']);
    $imageName = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);
    $imageExtension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    $new_file_path = file_upload_path($imagePath);
    $temporary_file_path = $_FILES['file']['tmp_name'];

    if(strlen($title) >= 1 && strlen($content) >= 1){
        if(isset($_POST['postType'])){
            $postType = $_POST['postType'];
            switch($postType){
                case "menu":
                if(isset($_FILES['file']) && $_FILES['file']['error'] === 0){
                    if(file_is_an_image($temporary_file_path, $new_file_path)){
                        $menuItemQuery = "INSERT INTO menuitems (item_name, description, cost, category_id) values (:item_name, :description, :cost, :category_id)";
                        $menuItemStatement = $db->prepare($menuItemQuery);
                        $menuItemStatement->bindValue(':item_name', $title);
                        $menuItemStatement->bindValue(':description', $content);
                        $menuItemStatement->bindValue(':cost', $validated_cost);
                        $menuItemStatement->bindValue(':category_id', $validated_categoryId);
                        $menuItemStatement->execute();
    
                        $menuItemID = $db->lastInsertId();
    
                        $newPageQuery = "INSERT INTO pages (menuItem_id, page_title) VALUES (:menuItem_id, :page_title)";
                        $newPageQueryStatement = $db->prepare($newPageQuery);
                        $newPageQueryStatement->bindValue(':menuItem_id', $menuItemID);
                        $newPageQueryStatement->bindValue(':page_title', $title);
                        $newPageQueryStatement->execute();
    
                        $resized_paths = [
                            ['image_path' => $imagePath],
                            ['image_path' => 'uploads/' . $imageName . '_medium' . '.' . $imageExtension],
                            ['image_path' => 'uploads/' . $imageName . '_thumbnail' . '.' . $imageExtension]
                        ];
    
                        $menuItemImageQuery = "INSERT INTO images (menuitem_id, image_path, image_name) values (:menuitem_id, :image_path, :image_name)";
                        $menuItemImageStatement = $db->prepare($menuItemImageQuery);
                        $menuItemImageStatement->bindValue(':menuitem_id', $menuItemID);
                        $menuItemImageStatement->bindValue(':image_name', $imageName);
    
                        foreach($resized_paths as $resized_path){
                            $menuItemImageStatement->bindValue(':image_path', $resized_path['image_path']);
                            $menuItemImageStatement->execute();
                        }
                        
                        header("Location: menu.php");
                    }

                }
            }
        }
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
function file_is_an_image($temporary_path, $new_path){
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
        if(file_is_an_image($temporary_file_path, $new_file_path)){
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
            $mediumImage->quality_png = 100;
            $mediumImage->crop(400, 300);
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
    <script src="https://cdn.tiny.cloud/1/820gg2umfmem63zoi1rxljkbeur1qnneg18i5m4u764kk8pi/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body>
<?php
if (isset($_SESSION['nonAdminUser'])) {
    header("Location: home.php");
} else {
    ?>
    <h1>New Page Post</h1>
    <form method="POST" enctype="multipart/form-data">
        <div class="postFormContainer">
            <label id="titlelabel" for="titletextbox">Title:</label>
            <input name="title" id="titletextbox" type="text" required>

            <label id="contentlabel" for="contenttextarea">Content:</label>
            <textarea name="content" id="contenttextarea"></textarea>

            <!-- TinyMCE Initialization -->
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    tinymce.init({
                        selector: '#contenttextarea',
                        plugins: [
                            'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 
                            'image', 'link', 'lists', 'media', 'searchreplace', 
                            'table', 'visualblocks', 'wordcount'
                        ],
                        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
                    });
                });
            </script>

            <input type="radio" name="postType" value="menu" id="menuPostType">
            <label for="menuPostType">New Menu Post</label>

            <label id="categoriesLabel" for="categoriesDropDown">Categories:</label>
            <select name="categoriesDropDown" id="categoriesDropDown">
                <?php foreach ($categories as $category) { ?>
                    <option value="<?php echo $category['category_id']; ?>">
                        <?php echo $category['category_name']; ?>
                    </option>
                <?php } ?>
            </select>

            <a id="categoryHREF" href="category.php">Edit Categories & Create New Categories</a>
            
            <label id="costLabel" for="menuItemCostInput" style="display:none;">Cost:</label>
            <input type="text" name="menuItemCostInput" id="menuItemCostInput" style="display:none;">

            <input type="file" name="file" id="file">
            <input type="submit" id="submitButton" value="Create Post">
        </div>
    </form>
    <script src="post.js"></script>
    <?php
}
?>
</body>
</html>





