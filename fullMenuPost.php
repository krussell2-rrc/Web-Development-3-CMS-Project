<?php
/*******w******** 
    
    Name: Kareem Russell
    Date: November 10, 2024
    Description: PHP For Menu Page of the CMS.

****************/
require('connect.php');

$menuItemId = filter_input(INPUT_GET, 'menuItemId', FILTER_SANITIZE_NUMBER_INT);
$validated_menuItemId = filter_var($menuItemId, FILTER_VALIDATE_INT);

if(isset($_POST['submitReview'])){
    if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])){
        $guest_username = filter_input(INPUT_POST, 'guestNameInput', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $guest_review = filter_input(INPUT_POST, 'commentsInput', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if(strlen($guest_username) >= 1 && strlen($guest_review) >= 1){
            $guestCommentQuery = "INSERT INTO reviews (guest_username, review, menuItem_id) VALUES (:guest_username, :review, :menuItem_id)";
            $guestCommentStatement = $db->prepare($guestCommentQuery);
            $guestCommentStatement->bindValue(':menuItem_id', $validated_menuItemId, PDO::PARAM_INT);
            $guestCommentStatement->bindValue(':guest_username', $guest_username);
            $guestCommentStatement->bindValue(':review', $guest_review);
            $guestCommentStatement->execute();
        }
    }else{
        $user_username = $_SERVER['PHP_AUTH_USER'];
        $user_review = filter_input(INPUT_POST, 'commentsInput', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if(strlen($user_username) >= 1 && strlen($user_review) >=1){
            $userCommentQuery = "INSERT INTO reviews (user_username, review, menuItem_id) VALUES (:user_username, :review, :menuItem_id)";
            $userCommentQuery = $db->prepare($userCommentQuery);
            $userCommentQuery->bindValue(':menuItem_id', $validated_menuItemId, PDO::PARAM_INT);
            $userCommentQuery->bindValue(':user_username', $user_username);
            $userCommentQuery->bindValue(':review', $user_review );
            $userCommentQuery->execute();
        }
    }
}

if(isset($_POST['deleteComment'])){
    $review_id = filter_input(INPUT_POST, 'review_id', FILTER_SANITIZE_NUMBER_INT);

    $deleteCommentQuery = "DELETE FROM reviews
                        WHERE review_id = :review_id";
    $deleteCommentStatement = $db->prepare($deleteCommentQuery);
    $deleteCommentStatement->bindValue(':review_id', $review_id, PDO::PARAM_INT);
    $deleteCommentStatement->execute();
}

$commentsQuery = "SELECT user_username, guest_username, review, menuItem_id, created_at, review_id
                FROM reviews
                WHERE menuItem_id = :menuitem_id
                ORDER BY created_at DESC";
$commentsStatement = $db->prepare($commentsQuery);
$commentsStatement->bindValue(':menuitem_id', $validated_menuItemId, PDO::PARAM_INT);
$commentsStatement->execute();
$comments = $commentsStatement->fetchAll();

$query = "SELECT menuitem_id, item_name, description, cost 
            FROM menuitems
            WHERE menuitem_id = :menuitem_id";

$statement = $db->prepare($query);
$statement->bindValue(':menuitem_id', $validated_menuItemId, PDO::PARAM_INT);
$statement->execute();
$menuPosts = $statement->fetchAll();

$imageQuery = "SELECT images.image_path, images.menuitem_id
        FROM images
        LEFT JOIN menuitems
        ON images.menuitem_id = :menuitem_id
        WHERE images.image_path LIKE '%_medium%'
        LIMIT 1";

$imageStatement = $db->prepare($imageQuery);
$imageStatement->bindValue(':menuitem_id', $validated_menuItemId, PDO::PARAM_INT);
$imageStatement->execute();
$images = $imageStatement->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
<?php
    echo '<div>';
    foreach($menuPosts as $menuPost){
        echo '<h1>' . $menuPost['item_name'] . '</h1>';
        echo '<p>' . $menuPost['description'] . '</p>';
        echo '<p>Cost: $' .$menuPost['cost'] . '</p>';
    }

    foreach($images as $image){
        if($image['menuitem_id'] == $validated_menuItemId){
            echo '<img class="menuItemImage" src="' . $image['image_path'] . '" alt="Image">';
        }
    }
    echo '</div>';
?>
<?php
    echo '<div id="commentsFormContainer">';
    echo '<form id="commentsForm" method="post">';
    if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])){
        echo '<label id="guestNameLabel" for="guestNameInput">Enter your name:</label>';
        echo '<input type="text" name="guestNameInput" id="guestName">';
    }
    echo '<label id="commentsLabel" for="commentsInput">Share Your Thoughts:</label>';
    echo '<textarea name="commentsInput" id="commentsInput" placeholder="Post a review"></textarea>';
    echo '<input name="submitReview" type="submit" value="Submit Review">';
    echo '</form>';
    echo '</div>';
?>
<?php
    echo '<div id=commentsContainer>';
        foreach($comments as $comment){
            if($comment['menuItem_id'] == $validated_menuItemId){
                if($comment['user_username']){
                    echo '<h1 class="user_username">' . $comment['user_username'] . '</h1>';
                    echo '<p class="reviewPostTime">' . $comment['created_at'] . '</p>';
                    echo '<p class="user_review">' . $comment['review'] . '</p>';
                    if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){
                        echo '<form method="POST">';
                        echo '<input type="hidden" name="review_id" value="' . $comment['review_id'] . '">';
                        echo '<input type="submit" name="deleteComment" id="deleteCommentButton" value="Delete">';
                        echo '</form>';
                    }
                }
                if(!$comment['user_username']){
                    echo '<h1 class="guest_username">' . $comment['guest_username'] . '</h1>';
                    echo '<p class="reviewPostTime">' . $comment['created_at'] . '</p>';
                    echo '<p class="user_review">' . $comment['review'] . '</p>';
                    if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){
                            echo '<form method="POST">';
                            echo '<input type="hidden" name="review_id" value="' . $comment['review_id'] . '">';
                            echo '<input type="submit" name="deleteComment" id="deleteCommentButton" value="Delete">';
                            echo '</form>';
                        }
                }
            }
        }
    echo '</div>';
?>
</body>
</html>