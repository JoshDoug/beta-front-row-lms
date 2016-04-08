<?php

use frontRow\Module;
use frontRow\ModulePage;
use frontRow\UploadFile;
use frontRow\User;

require_once '_includes/pdoConnect.php';
require_once '_includes/authenticate.php';
require_once '_includes/frontRow/Module.php';
require_once '_includes/frontRow/ModulePage.php';
require_once '_includes/frontRow/UploadFile.php';
require_once '_includes/frontRow/User.php';

$sql = 'SELECT permission FROM userModule WHERE kNumber = :kNumber AND moduleID = :moduleID';
$stmt = $db->prepare($sql);
$stmt->bindParam(':moduleID', $_GET['moduleID']);
$stmt->bindParam(':kNumber', $_SESSION['username']);
$stmt->execute();

if($stmt->fetchColumn() == 1){
    $priv = true;
} else {
    $priv = false;
    header('Location: home.php');
}

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

foreach($modules as $module) {
    
    $module->setModulePage($db);    
}

if(!isset($_SESSION['maxfiles'])){
    $_SESSION['maxfiles'] = ini_get('max_file_uploads');
    $_SESSION['postmax'] = UploadFile::convertToBytes(ini_get('post_max_size'));
    $_SESSION['displaymax'] = UploadFile::convertFromBytes($_SESSION['postmax']);
}

$max = 32000000;
$result = [];

$moduleID = $_GET['moduleID'];
$destination = __DIR__ . '/_uploads/' . $moduleID . '/';

if(!is_dir($destination)) {
    mkdir($destination, 0755);
}

if(isset($_POST['upload'])) {
    
    try {
        $upload = new UploadFile($destination);
//        $upload->setMaxSize($max);
        $upload->allowAllTypes();
        $upload->upload();
        $result = $upload->getMessages();
    } catch (Exception $e) {
        $result[] = $e->getMessage();
    }
}

$error = error_get_last();


//DELTE FILE AND LINKS TO FILE
if(isset($_POST['deleteFile'])) {
    //Need to delete all linked files associated with posts first, to avoid void links
    //Should probably warn user, but is also self explanatory, add warning if time allows.
    //Delete postFile rows associated with file
    $sql = 'DELETE FROM postFile
            WHERE fileName = :fileName';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':fileName', $_POST['fileName']);
    $stmt->execute();
    //After postFile rows associated with file have been removed, delete file.
    
    $fileAndPath = $destination . $_POST['fileName'];
    if(file_exists($fileAndPath)) {
        unlink($fileAndPath);
    }
}

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
            <?php if($result || $error) : ?>
            <section>
                <h2>File Upload Info:</h2>
                <ul class="result">
                    <?php 

                        if($error) {
                            echo "<li>{error['message']}</li>";
                        }

                    if($result) {
                    foreach($result as $message) : ?>
                        <li><?= $message ?></li>
                    <?php endforeach ?>
                    <?php } ?>
                </ul>
            </section>
            <?php endif ?>
            
            <section>
                <h2>Upload Files:</h2>
                <form action="moduleFile.php?moduleID=<?= $_GET['moduleID'] ?>" method="post" enctype="multipart/form-data">
                    <p>
                        <input type="hidden" name="MAX_FILE_SIZE" value="<?= $max ?>">
                        <label for="filename">Select File</label>
                        <input type="file" name="filename[]" id="filename" multiple
                               data-maxfiles="<?= $_SESSION['maxfiles'] ?>"
                               data-postmax="<?= $_SESSION['postmax'] ?>"
                               data-displaymax="<?= $_SESSION['displaxmax'] ?>">
                    </p>
                    <p>
                        <input type="submit" name="upload" value="Upload File">
                    </p>
                    <ul>
                        <li>Up to <?php echo $_SESSION['maxfiles'];?> files can be uploaded simultaneously.</li>
                        <li>Each file should be no more than <?php echo UploadFile::convertFromBytes($max);?>.</li>
                        <li>Combined total should not exceed <?php echo $_SESSION ['displaymax'];?>.</li>
                    </ul>
                </form>
            </section>
            <?php 
                $directoryContents = scandir($destination);
                $files = array_diff($directoryContents, array('.', '..'));    
            ?>
            <article>
                <h2>Files</h2>
                <?php foreach($files as $file) : ?>
                <section class="fileList">
                    <p><a href="<?php echo '_uploads/' . $moduleID . '/' .  $file ?>" target="_blank"><?= $file ?></a></p>
                    <form method="post" action="">
                        <input type="hidden" name="fileName" value="<?= $file ?>">
                        <input type="submit" name="deleteFile" value="Delete File">
                    </form>
                </section>
                <?php endforeach ?>
            </article>
        </main>
        <script src="_js/checkMultiple.js"></script>
        <script src="_js/navToggle.js"></script>
    </body>
</html>