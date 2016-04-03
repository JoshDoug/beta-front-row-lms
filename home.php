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

//Set up announcements, these are only used by the homepage
$announcementStmt = $db->prepare('SELECT * FROM announcement');
$announcementStmt->execute();
$announcements = $announcementStmt->fetchAll(PDO::FETCH_CLASS, 'announcement');

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>FR| Home</title>
        
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="Joshua Stringfellow, Jessica Wallace">
        <meta name="description" content="FrontRow Homepage.">
        
        <link href='http://fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
        <link rel="shortcut icon" href="/_img/favicon.ico" type="image/x-icon">
        
        <link rel="stylesheet" href="_css/layout.css">
    </head>
    <body>
        <header>
            <img src="_img/kulogo.png" alt="Kingston University">
            <h1>Front Row | Home</h1>
        <nav>
            <a href="home.php">Home</a>
            <a href="moduleCatalogue.php">Module Catalogue</a>
            <div class="dropdown">
                <a href="#" class="dropdown-toggle">User Information</a>
                <ul class="dropdown-content">
                    <li><a href="#"><?= $user->kNumber ?></a></li>
                    <li><a href="#"><?= $user->fName ?></a></li>
                    <li><a href="#"><?= $user->lName ?></a></li>
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
                <h2>General Announcements</h2>
                
                <?php foreach($announcements as $announcement) : ?>
                <section>
                    <h3><?= $announcement->title ?></h3>
                    <p><?= $announcement->content ?></p>
                    <p><?= $announcement->datePosted ?></p>
                </section>
                <?php endforeach ?>
            </article>
            <article>
                <h2>Module Announcements</h2>
                <section>
                    <h3>Programming</h3>
                    <p>Your marks have been emailed to you, please email me if you have any queries or disagree with the marks you were given!</p>
                </section>
                <section>
                    <h3>Networking</h3>
                    <p>Remember, your CA CW is due this friday.</p>
                </section>
                <section>
                    <h3>Databases</h3>
                    <p>You know Oracle DBs, now get ready to learn MySQL which has random differences, but does at least have Autoincrement (phew!).</p>
                </section>
            </article>
        </main>
    </body>
</html>