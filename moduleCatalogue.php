<?php

use frontRow\Announcement;
use frontRow\Link;
use frontRow\Module;
use frontRow\ModulePage;
use frontRow\Post;
use frontRow\UploadFile;
use frontRow\User;

require_once '_includes/pdoConnect.php';
require_once '_includes/authenticate.php';
include_once '_includes/frontRow/Announcement.php';
require_once '_includes/frontRow/Module.php';
require_once '_includes/frontRow/User.php';

//Set up user
$stmt = $db->prepare('SELECT *
FROM user
WHERE user.kNumber=:kNumber');
$stmt->bindParam(':kNumber', $_SESSION['username']);
$stmt->execute();

$user = $stmt->fetchObject('User');

//Set Modules up, for module navigation, moduleSetup is slightly more complex in module.php as there is also a current module
$user->setModules($db);
$modules = $user->modules;

foreach($modules as $module) {
    
    $module->setModulePage($db);    
//    $numberOfPages = count($module->modulePage);
}

//Set up module catalogue

$stmt = $db->prepare('SELECT * FROM module');
$stmt->execute();
$allModules = $stmt->fetchAll(PDO::FETCH_CLASS, 'Module');

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Module Catalogue</title>
        
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="Joshua Stringfellow, Jessica Wallace">
        <meta name="description" content="FrontRow Homepage.">
        
        <link href='http://fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
        <link rel="shortcut icon" href="/_img/favicon.ico" type="image/x-icon">
        
        <link rel="stylesheet" href="_css/layout.css">
        
        <script>
//            var showLectureUtils = $_SESSION['showUtils'] or something like that
        </script>
        
    </head>
    <body>
        <header>
            <img src="_img/kulogo.png" alt="Kingston University">
            <h1>Module Catalogue</h1>
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
            <article>
                <h2>List of Modules</h2>
                <?php foreach($allModules as $module) : ?>
                <section>
                    <h3><a href="module.php?moduleID=<?= $module->moduleID ?>"><?= $module->moduleID ?> : <?= $module->moduleName ?></a></h3>
                    <p><?= $module->moduleDescription ?></p>
                </section>
                <?php endforeach; ?>
            </article>
        </main>
        <script src="_js/navToggle.js"></script>
    </body>
</html>