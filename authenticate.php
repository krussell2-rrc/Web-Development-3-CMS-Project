 <?php

/*******w******** 
    
    Name: Kareem Russell
    Date: November 10, 2024
    Description: PHP For authenticating user's to be able to access the post and edit page of the blog. 

****************/

  define('ADMIN_LOGIN','wally');

  define('ADMIN_PASSWORD','mypass');

  if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])

      || ($_SERVER['PHP_AUTH_USER'] != ADMIN_LOGIN)

      || ($_SERVER['PHP_AUTH_PW'] != ADMIN_PASSWORD)) {

    header('HTTP/1.1 401 Unauthorized');

    header('WWW-Authenticate: Basic realm="Our Blog"');

    exit("Access Denied: Username and password required.");

  }
?>