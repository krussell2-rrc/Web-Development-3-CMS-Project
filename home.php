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
}else{
    $welcomeMessage = "Welcome To The Cozy Cafe!";
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
        <?php if (!isset($_SESSION['nonAdminUser'])) { ?>
            <p style="color: white; font-size: 15px; text-align: center;">
                <?php echo $welcomeMessage; ?>
            </p>
            <form action="login.php" method="GET">
                <input id="logInButton" type="submit" name="logInButton" value="Login" style="border-radius: 7px; position: relative; top: 52px; left: 530px;">
            </form>
        <?php } ?>
        <?php if (isset($_SESSION['nonAdminUser'])) { ?>
        <p style="color: white; font-size: 15px; text-align: center;">
            <?php echo $welcomeMessage; ?>
        </p>
            <form method="POST">
                <input id="logOutButton" type="submit" name="logOutButton" value="Logout" style="border-radius: 7px; position: relative; top: 52px; left: 500px;">
            </form>
        <?php } ?>
    </div>
    <div class="homeImage">
    </div>
    <div class="homeNavDiv">
        <nav class="homeNav">
            <ul>
                <a href="home.php">HOME</a>
                <a href="menu.php">MENU</a>
                <a href="">NEWS</a>
                <a href="">ABOUT US</a>
            </ul>
        </nav>
    </div>
</body>
</html>