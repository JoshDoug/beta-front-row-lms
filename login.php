<?php

require_once '_includes/pdoConnect.php';
require_once '_includes/loginUser.php';

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Front Row | LMS</title>
        
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="Joshua Stringfellow, Jessica Wallace">
        <meta name="description" content="This is a prototype learning management system.">
        
        <link href='http://fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
        <link rel="shortcut icon" href="/_img/favicon.ico" type="image/x-icon">
        
        <link rel="stylesheet" href="_css/login.css">
    </head>
    <body>
        <header>
            <h1>Front Row LMS</h1>
        </header>
        <main>
        <img src="_img/kulogo.png" alt="Kingston University">
            <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post">
                    <?php
                        if (isset($error)) {
                            echo "<p style=\"color: white\">$error</p>";
                        }
                    ?>
                <input type="text" name="kNumber" placeholder="K Number">
                <input type="password" name="pwd" placeholder="Password">
                <input type="submit" name="login" value="Log In">
            </form>
        </main>
    </body>
</html>