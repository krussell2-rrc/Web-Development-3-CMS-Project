<?php
/*******w******** 
    
    Name: Kareem Russell
    Date: November 10, 2024
    Description: PHP For Menu Page of the CMS.

****************/
require('connect.php');
session_start();

$menuItemId = filter_input(INPUT_GET, 'menuItemId', FILTER_SANITIZE_NUMBER_INT);
$validated_menuItemId = filter_var($menuItemId, FILTER_VALIDATE_INT);

// Full menu post handling
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

// Comment/review handling
if (isset($_POST['submitReview'])) {
    if ($_POST["captcha_code"] == $_SESSION["captcha_code"]) {
        if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
            $guest_username = filter_input(INPUT_POST, 'guestNameInput', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $guest_review = filter_input(INPUT_POST, 'commentsInput', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if (strlen($guest_username) >= 1 && strlen($guest_review) >= 1) {
                $guestCommentQuery = "INSERT INTO reviews (guest_username, review, menuItem_id) VALUES (:guest_username, :review, :menuItem_id)";
                $guestCommentStatement = $db->prepare($guestCommentQuery);
                $guestCommentStatement->bindValue(':menuItem_id', $validated_menuItemId, PDO::PARAM_INT);
                $guestCommentStatement->bindValue(':guest_username', $guest_username);
                $guestCommentStatement->bindValue(':review', $guest_review);
                $guestCommentStatement->execute();
            }
        } elseif((isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) && (!isset($_SESSION['nonAdminUser']))){
            $admin_username = $_SERVER['PHP_AUTH_USER'];
            $user_review = filter_input(INPUT_POST, 'commentsInput', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if (strlen($admin_username) >= 1 && strlen($user_review) >= 1) {
                $userCommentQuery = "INSERT INTO reviews (admin_username, review, menuItem_id) VALUES (:admin_username, :review, :menuItem_id)";
                $userCommentQuery = $db->prepare($userCommentQuery);
                $userCommentQuery->bindValue(':menuItem_id', $validated_menuItemId, PDO::PARAM_INT);
                $userCommentQuery->bindValue(':admin_username', $admin_username);
                $userCommentQuery->bindValue(':review', $user_review);
                $userCommentQuery->execute();
            }
        }  elseif (isset($_SESSION['nonAdminUser'])) {
            $user_username = $_SESSION['nonAdminUser'];
            $user_review = filter_input(INPUT_POST, 'commentsInput', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if (strlen($user_username) >= 1 && strlen($user_review) >= 1) {
                $userCommentQuery = "INSERT INTO reviews (user_username, review, menuItem_id) VALUES (:user_username, :review, :menuItem_id)";
                $userCommentQuery = $db->prepare($userCommentQuery);
                $userCommentQuery->bindValue(':menuItem_id', $validated_menuItemId, PDO::PARAM_INT);
                $userCommentQuery->bindValue(':user_username', $user_username);
                $userCommentQuery->bindValue(':review', $user_review);
                $userCommentQuery->execute();
            }
        }
    }else{
        $comment = $_POST["commentsInput"];
    }
}

if(isset($_POST['deleteComment'])){
    $review_id = filter_input(INPUT_POST, 'review_id', FILTER_SANITIZE_NUMBER_INT);
    $validated_reviewId = filter_var($review_id, FILTER_VALIDATE_INT);

    $deleteCommentQuery = "DELETE FROM reviews
                        WHERE review_id = :review_id";
    $deleteCommentStatement = $db->prepare($deleteCommentQuery);
    $deleteCommentStatement->bindValue(':review_id', $validated_reviewId, PDO::PARAM_INT);
    $deleteCommentStatement->execute();
}

$commentsQuery = "SELECT admin_username, guest_username, user_username, review, menuItem_id, created_at, review_id
                FROM reviews
                WHERE menuItem_id = :menuitem_id
                ORDER BY created_at DESC";
$commentsStatement = $db->prepare($commentsQuery);
$commentsStatement->bindValue(':menuitem_id', $validated_menuItemId, PDO::PARAM_INT);
$commentsStatement->execute();
$comments = $commentsStatement->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
<?php
    echo '<a style="background-color:red; color:white; text-decoration :none; position: relative; top:140px; left:340px;" href="post.php">Edit Post</a>';
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
    echo '<form id="commentsForm" method="POST">';
    if (!isset($_SERVER['PHP_AUTH_USER']) && !isset($_SERVER['PHP_AUTH_PW']) && (!isset($_SESSION['nonAdminUser']))){
        echo '<label id="guestNameLabel" for="guestNameInput">Enter your name:</label>';
        echo '<input type="text" name="guestNameInput" id="guestName">';
    }
    echo '<label id="commentsLabel" for="commentsInput">Share Your Thoughts:</label>';
    echo '<textarea name="commentsInput" id="commentsInput" placeholder="Post a review">' . (isset($comment) ? $comment : '') . '</textarea>';

    echo '<label for="captcha">Enter the CAPTCHA:</label>';
    echo '<img src="tweeks_captcha\captcha\captcha_gen.php" alt="CAPTCHA Image">';
    echo '<input type="text" name="captcha_code" id="captcha">';

    echo '<input name="submitReview" type="submit" value="Submit Review">';
    echo '</form>';
    echo '</div>';
?>
<?php
echo '<div id=commentsContainer>';
foreach ($comments as $comment) {
    if ($comment['menuItem_id'] == $validated_menuItemId) {
        if ($comment['admin_username']) {
            echo '<h1 class="admin_username">' . $comment['admin_username'] . '</h1>';
            echo '<p class="reviewPostTime">' . $comment['created_at'] . '</p>';
            echo '<p class="user_review">' . $comment['review'] . '</p>';
            if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && (!isset($_SESSION['nonAdminUser']))) {
                echo '<form method="POST">';
                echo '<input type="hidden" name="review_id" value="' . $comment['review_id'] . '">';
                echo '<input type="submit" name="deleteComment" id="deleteCommentButton" value="Delete">';
                echo '</form>';
            }
        } else {
            if ($comment['guest_username']) {
                echo '<h1 class="guest_username">' . $comment['guest_username'] . '</h1>';
                echo '<p class="reviewPostTime">' . $comment['created_at'] . '</p>';
                echo '<p class="user_review">' . $comment['review'] . '</p>';
                if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && (!isset($_SESSION['nonAdminUser']))) {
                    echo '<form method="POST">';
                    echo '<input type="hidden" name="review_id" value="' . $comment['review_id'] . '">';
                    echo '<input type="submit" name="deleteComment" id="deleteCommentButton" value="Delete">';
                    echo '</form>';
                }
            } else {
                if ($comment['user_username']) {
                    echo '<h1 class="user_username">' . $comment['user_username'] . '</h1>';
                    echo '<p class="reviewPostTime">' . $comment['created_at'] . '</p>';
                    echo '<p class="user_review">' . $comment['review'] . '</p>';
                    if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && (!isset($_SESSION['nonAdminUser']))) {
                        echo '<form method="POST">';
                        echo '<input type="hidden" name="review_id" value="' . $comment['review_id'] . '">';
                        echo '<input type="submit" name="deleteComment" id="deleteCommentButton" value="Delete">';
                        echo '</form>';
                    }
                }
            }
        }
    }
}
echo '</div>';
?>
</body>
</html>