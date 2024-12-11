<?php
/*******w******** 
    
    Name: Kareem Russell
    Date: December 1, 2024
    Description: Homepage of the CMS.

****************/
require('connect.php');
session_start();

$welcomeMessage = "";

if (isset($_SESSION['nonAdminUser'])) {
    $welcomeMessage = "You've successfully signed in, " . $_SESSION['nonAdminUser'] . "!";

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logOutButton'])) {
        $_SESSION = array();
        session_destroy();

        header("Location: login.php");
        exit;
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
    <title>Home</title> 
</head>
<body class="homeBody">
    <div class="homeHeader">
        <p style="color: white; font-size: 15px;">
            <?php echo $welcomeMessage; ?>
        </p>
        <form action="login.php" method="GET">
            <button id="accountButton" style="font-size:24px;"><i class="fa fa-user"></i></button>
        </form>
        <?php if (isset($_SESSION['user_email'])) { ?>
        <form method="POST">
            <input id="logOutButton" type="submit" name="logOutButton" value="Log out" style="border-radius: 7px; position: relative; top: 52px; left: 515px;">
        </form>
        <?php } ?>
    </div>
    <div class="homeImage">
    </div>
</body>
</html>