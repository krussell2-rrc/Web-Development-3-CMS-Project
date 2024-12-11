<?php
/*******w******** 
    
    Name: Kareem Russell
    Date: December 06, 2024
    Description: PHP For Register Page of the CMS.

****************/
require('connect.php');

$errorMessage = "";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $reenteredPassword = filter_input(INPUT_POST, 'reenteredPassword', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if(strlen($email) >= 1 && strlen($password) >= 1 && strlen($reenteredPassword) >= 1){
        if($password == $reenteredPassword){
            $userPassword = password_hash($password, PASSWORD_DEFAULT);

            $newUserQuery = "INSERT INTO users (email, password) values (:email, :password)";
            $newUserStatement = $db->prepare($newUserQuery);
            $newUserStatement->bindValue(':email', $email);
            $newUserStatement->bindValue(':password', $userPassword);
            $newUserStatement->execute();

            header("Location: login.php");
        }else{
            $errorMessage = "The re-entered password does not match.<br>Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account</title>
    <link rel="stylesheet" href="main.css">
</head>
<body class="registerAndLoginBody">
        <form method="POST" id="registerAndLoginForm">
            <div class="registerAndLoginContainer">
                <label for="email" style="border-radius: 7px; position: relative; top: -10px;">Email:</label>
                <input type="text" name="email" id="emailInput" style="border-radius: 7px; position: relative; top: -10px;">
                <label for="password" style="position: relative; top: 10px;">Password:</label>
                <input type="password" name="password" id="passwordInput" style="border-radius: 7px; position: relative; top: 10px;">
                <label for="reenteredPassword" style="position: relative; top: 20px;">Re-enter your password:</label>
                <input type="password" name="reenteredPassword" id="passwordInput" style="border-radius: 7px; position: relative; top: 20px;">
                <input type="submit" value="Register" style="border-radius: 7px; position: relative; top: 40px; left: 50px;">
                <?php 
                    if($errorMessage !== "")
                    {
                        echo '<p style="color: red position: absolute; left: 640px; top:465px; text-align: center;">' . $errorMessage . '</p>';
                    }
                ?>
            </div>
        </form>
</body>
</html>