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
        $currentPageID = $currentPage->pageID;
            
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
        
        
        //Handle Posts - potentially shift into a require/include
        $moduleID = $_GET['moduleID'] . '/';
        $destination = __DIR__ . '/_uploads/' . $moduleID;

        if(!is_dir($destination)) {
            mkdir($destination, 0755);
        }
        
        if(isset($_POST['makePost']) && isset($_POST['postTitle'])) {
            $sql = 'INSERT INTO post (title, pageID, content, commentsAllowed, dateTimePosted)
                    VALUES (:title, :pageID, :content, :commentsAllowed, now())';
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':pageID', $currentPageID);
            $stmt->bindParam(':title', $_POST['postTitle']);
            $stmt->bindParam(':content', $_POST['postContent']);
            if(isset($_POST['commentsAllowed'])) {
                $commentsAllowed = 1;
                $stmt->bindParam(':commentsAllowed', $commentsAllowed);
            } else {
                $commentsAllowed = 0;
                $stmt->bindParam(':commentsAllowed', $commentsAllowed);
            }
            $stmt->execute();

            //Can probably remove this var
            $sql = 'SELECT LAST_INSERT_ID();';
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $lastID = $stmt->fetchColumn();

            print_r($lastID);
            
            if(isset($_POST['fileChoice'])) {

                $postFiles = $_POST['fileChoice'];

                foreach($postFiles as $file) {
                $sql = 'INSERT INTO postFile (postID, fileName)
                        VALUES (:postID, :fileName)';
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':postID', $lastID);
                $stmt->bindParam(':fileName', $file);
                $stmt->execute();
                }
                print_r($_POST['fileChoice']);
            }

            if(isset($_POST['linkName']) && isset($_POST['linkHref'])){
                $linkNames = $_POST['linkName'];
                $linkHrefs = $_POST['linkHref'];

                $linkNumber = count($linkNames);

                for($i = 0; $i < $linkNumber; $i++) {
                    $sql = 'INSERT INTO postLink (postID, linkName, linkHref)
                            VALUES (:postID, :linkName, :linkHref)';
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':postID', $lastID);
                    $stmt->bindParam(':linkName', $linkNames[$i]);
                    $stmt->bindParam(':linkHref', $linkHrefs[$i]);
                    $stmt->execute();
                }
            }
        }
        
        if(isset($_POST['postComment'])) {
            $sql = 'INSERT INTO postComment (postID, kNumber, commentText, dateTimeCommented)
                    VALUES (:postID, :kNumber, :commentText, now())';
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':postID', $_POST['postID']);
            $stmt->bindParam(':kNumber', $_SESSION['username']);
            $stmt->bindParam(':commentText', $_POST['commentText']);
            $stmt->execute();
        }
        
        //Select posts
        $sql = 'SELECT * FROM Post WHERE pageID = :pageID';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':pageID', $currentPageID);
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_CLASS, 'Post');
        
        
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
            <h2>Module: <?= $currentModule->moduleID ?> - <?= $currentModule->moduleName ?></h2>
            
            <?php if($priv) : ?>
            <article>
                <h2>Test Multiple Insert referencing initial insert ID.</h2>
                <section>
                    <!--     POST CREATION FORM               -->
                    <form method="post" action="">
                        <p>Enter Post Title:</p>
                        <input type="text" name="postTitle">
                        <p>Enter Post Content:</p>
                        <input type="text" name="postContent">
                        <p>Comments allowed:</p>
                        <input type="checkbox" name="commentsAllowed">
                        <?php $directoryContents = scandir($destination);
                        $files = array_diff($directoryContents, array('.', '..')); ?>
                        <p>Add files to post or remove files:</p>
                        <button id="addFileChoice">Add File</button>
                        <button id="removeFileChoice">Remove File</button>
                        <div id="file-choice-section">
                            <select class="fileChoice" name="fileChoice[]">
                                <?php foreach($files as $file) : ?>
                                <?php if($file != "." || $file != "..") : ?>
                                    <option value="<?= $file ?>"><?= $file ?></option>
                                <?php endif ?>

                                <?php if($file == "."){
                                        echo 'It equals .';
                                    }
                                ?>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <p>Add links to post or remove links:</p>
                        <button id="addLinkChoice">Add Link</button>
                        <button id="removeLinkChoice">Remove Link</button>
                        <div id="link-choice"></div>

                        <input type="submit" name="makePost">
                    </form>
                </section>
            </article>
            <?php endif ?>
            
            <?php foreach($posts as $post) : ?>
            <article>
                <h2><?= $post->title ?></h2>
                <section>
                <p><?= $post->content ?></p>
                <?php
                    $postID = $post->postID;
                    $sql = 'SELECT fileName FROM postFile WHERE postID = :postID';
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':postID', $postID);
                    $stmt->execute();
                    $postFileArr = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

                ?>
                <p>Files:</p>
                <?php
                    foreach($postFileArr as $linkedFile){
                        echo '<p><a target="_blank" href="/_uploads/' . $moduleID . $linkedFile . '">' . $linkedFile . '</a></p>';
                    }

                ?>
                <p>Links:</p>
                <?php
                    $sql = 'SELECT * FROM postLink WHERE postID = :postID';
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':postID', $postID);
                    $stmt->execute();
                    $links = $stmt->fetchAll(PDO::FETCH_CLASS, 'Link');

                    foreach($links as $link){
                        echo '<p><a target="_blank" href="' . $link->linkHref . '">' . $link->linkName . '</a></p>';
                    }
                ?>
                <?php
                    if($post->commentsAllowed){
                        echo '<p>Comments allowed!</p>';
                        $sql = 'SELECT * FROM postComment WHERE postID = :postID';
                        $stmt = $db->prepare($sql);
                        $stmt->bindParam(':postID', $postID);
                        $stmt->execute();
                        $comments = $stmt->fetchAll(PDO::FETCH_CLASS, 'Comment');
                        foreach($comments as $comment) {
                            echo '<h3>' . $comment->kNumber . '</h3>';
                            echo '<p>' . $comment->commentText . '</p>';
                        }
                ?>
                <form method="post" action="">
                    <input type="hidden" name="postID" value="<?= $post->postID ?>">
                    <input type="text" name="commentText">
                    <input type="submit" name="postComment">
                </form>
                <?php
                    } elseif(!$post->commentsAllowed) {
                        echo '<p>No comments allowed!</p>';
                    } else {
                        echo '<p>Something went wrong.</p>';
                    }
                ?>
                </section>
            </article>
            <?php endforeach ?>
        </main>
    <script src="_js/postOptions.js"></script>
    </body>
</html>