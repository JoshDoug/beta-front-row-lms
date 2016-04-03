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
require_once '_includes/frontRow/Module.php';

if(isset($_GET['moduleID'])) {
    //Check that the module exists, avoids any randoom user additions to the _GET
    $sql = 'SELECT COUNT(*) FROM module WHERE moduleID = :moduleID';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':moduleID', $_GET['moduleID']);
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        header('Location: home.php');
    } else {
        
        $currentModule;
        
        $stmt = $db->prepare('SELECT module.moduleID, module.moduleName
        FROM module, userModule
        WHERE module.moduleID=userModule.moduleID AND userModule.kNumber=:kNumber');
        $stmt->bindParam(':kNumber', $_SESSION['username']);
        $stmt->execute();
        
        $modules = $stmt->fetchAll(PDO::FETCH_CLASS, 'module');
        
        foreach($modules as $module) {
            $module->setModulePage($db);
            if ($_GET['moduleID'] == $module->moduleID) {
                $currentModule = $module;
            }
        }
        
        $currentPage;
        
        //USE CURRENT MODULE - DUH
        if (isset($_GET['modulePage'])) {
            $numberOfPages = count($currentModule->modulePage);
            for ($i = 0; $i < $numberOfPages; $i++) {
                if ($_GET['modulePage'] == $currentModule->modulePage[$i]) {
                    $currentPage = $currentModule->modulePage[$i];
                }
            }
            if (!isset($currentPage)) {
                $currentPage = $currentModule->modulePage[0];
            }
        } else {
            echo 'It ain\'t set!';
            $currentPage = $currentModule->modulePage[0];
        }
        
        //Get user privs
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
        //echo $stmt->fetchColumn();

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
            <h1><?= $currentPage ?></h1>
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