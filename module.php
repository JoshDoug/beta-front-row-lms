<?php

use frontRow\Comment;
use frontRow\Link;
use frontRow\Module;
use frontRow\ModulePage;
use frontRow\Post;
use frontRow\UploadFile;
use frontRow\User;

require_once '_includes/pdoConnect.php';
require_once '_includes/authenticate.php';
require_once '_includes/frontRow/Comment.php';
require_once '_includes/frontRow/Link.php';
require_once '_includes/frontRow/Module.php';
require_once '_includes/frontRow/ModulePage.php';
require_once '_includes/frontRow/Post.php';
require_once '_includes/frontRow/UploadFile.php';
require_once '_includes/frontRow/User.php';

if(isset($_GET['moduleID'])) {
    //Check that the module exists, avoids any randoom user additions to the _GET
    $sql = 'SELECT COUNT(*) FROM module WHERE moduleID = :moduleID';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':moduleID', $_GET['moduleID']);
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        header('Location: home.php');
    } else {
        
        //Set up user
        $stmt = $db->prepare('SELECT *
        FROM user
        WHERE user.kNumber=:kNumber');
        $stmt->bindParam(':kNumber', $_SESSION['username']);
        $stmt->execute();

        $user = $stmt->fetchObject('User');
        
        //Get Modules
        $user->setModules($db);
        $modules = $user->modules;
        
        //Create var for current module
        $currentModule;
        
        foreach($modules as $module) {
            $module->setModulePage($db);
            if ($_GET['moduleID'] == $module->moduleID) {
                $currentModule = $module;
            }
        }
        
        if(isset($_GET['modulePage'])) {
            if(in_array($_GET['modulePage'], $currentModule->modulePage)) {
                $currentModule->setCurrentPage($db, $_GET['modulePage']);
            } else {
                $currentModule->setCurrentPage($db, $currentModule->modulePage[0]);
            }
        } else {
            $currentModule->setCurrentPage($db, $currentModule->modulePage[0]);
        }
        
        $currentPage = $currentModule->currentPage;
        
        $currentPage->getPosts($db);    
            
        //Get user privs for current module
        $sql = 'SELECT permission FROM userModule WHERE kNumber = :kNumber AND moduleID = :moduleID';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':moduleID', $_GET['moduleID']);
        $stmt->bindParam(':kNumber', $_SESSION['username']);
        $stmt->execute();
        
        if($stmt->fetchColumn() == 1){
            $priv = true;
        } else {
            $priv = false;
        }
    }
} else {
    header('Location: home.php');
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>FR | Module Page</title>
        
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="Joshua Stringfellow, Jessica Wallace">
        <meta name="description" content="FrontRow Homepage.">
        
        <link href='http://fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
        <link rel="shortcut icon" href="_img/favicon.ico" type="image/x-icon">
        
        <link rel="stylesheet" href="_css/layout.css">
    </head>
    <body>
        <header>
            <img src="_img/kulogo.png" alt="Kingston University">
            <h1><?= $currentPage->pageName ?></h1>
            <nav>
                <a href="home.php">Home</a>
                <a href="moduleCatalogue.php">Module Catalogue</a>
                <div class="dropdown">
                    <a href="#" class="dropdown-toggle">User Information</a>
                    <ul class="dropdown-content">
                        <li><a href="#">KNumber</a></li>
                        <li><a href="#">Email</a></li>
                        <li><a href="#">Name</a></li>
                    </ul>
                </div>
                <a id="logout" href="logout.php">Logout</a>
            </nav>
        </header>
        <nav>
<?php include_once '_includes/moduleNav.php'; ?>
        </nav>
        <main>
            <h2>Module: <?= $currentModule->moduleID ?> - <?= $currentModule->moduleName ?></h2>
            <article>
                <p>Gon put some forms here</p>
            </article>
            <article class="module-post">
                <h2>Lecture 3 Slides</h2>
                <section>
                    <p>Here are the slides from the lecture, as well as some useful resources to help with the coursework.</p>
                    <h3>Documents &amp; Resources:</h3>
                    <a href="#">SomeSlides.jpg</a>
                    <a href="#">SomeCode.zip</a>
                    <h3>Other Resources:</h3>
                    <a href="#">CodeCademy</a>
                    <a href="#">OraclesJavaSE9Plans</a>
                </section>
            </article>
            <article class="module-post">
                <h2>Coursework Info</h2>
                <section>
                    <p>Here are the slides from the lecture, as well as some useful resources to help with the coursework.</p>
                    <h3>Documents &amp; Resources:</h3>
                    <a href="#">SomeSlides.jpg</a>
                    <a href="#">SomeCode.zip</a>
                    <h3>Other Resources:</h3>
                    <a href="#">CodeCademy</a>
                    <a href="#">OraclesJavaSE9Plans</a>
                </section>
            </article>
        </main>
    </body>
</html>