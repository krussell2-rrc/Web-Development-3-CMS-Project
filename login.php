<?php
/*******w******** 
    
    Name: Kareem Russell
    Date: December 06, 2024
    Description: PHP For Login Page of the CMS.

****************/
require('connect.php');

session_start();
$message = "";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if(strlen($email) >= 1 && strlen($password) >= 1){
        $accountFetchQuery = 
        "SELECT password from users WHERE email = :email";

        $accountFetchStatement = $db->prepare($accountFetchQuery);
        $accountFetchStatement->bindParam(':email', $email, PDO::PARAM_STR);
        $accountFetchStatement->execute();
        $userPassword = $accountFetchStatement->fetchColumn();
        $user = $accountFetchStatement->fetch(PDO::FETCH_ASSOC);

        if($userPassword === null){
            $message =  "Incorrect email or password.";
        }
        
        if(password_verify($password, $userPassword)){
            $message = "You've successfully logged in!";
            $_SESSION['user_email'] = $email;
            header("Location: home.php");

        }else{
            $message =  "Incorrect email or password.";
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
                <label for="password" style="position: relative; top: 20px;">Password:</label>
                <input type="password" name="password" id="passwordInput" style="border-radius: 7px; position: relative; top: 20px;">
                <input type="submit" value="Login" style="border-radius: 7px; position: relative; top: 50px; left: 60px;">
                <p style="position: relative; top: 60px;">Not registered? <a href="register.php">Register</a></p>
                <?php 
                    if($message !== "")
                    {
                        echo '<p style="color: red; position: absolute; left: 675px; top: 475px; text-align: center;">' . $message . '</p>';
                    }
                ?>    
            </div>
        </form>
</body>
</html>