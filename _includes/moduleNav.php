<?php

$isFirst = true;
$nonUserModule = false;
//Not really necessary, but nice.
$userModuleIDs = [];
foreach($modules as $module) {
    array_push($userModuleIDs, $module->moduleID);
}

?>
<?php foreach($modules as $module) : ?>
            <section>
                <h2 <?php 
                    if (isset($currentModule) && in_array($currentModule->moduleID, $userModuleIDs)) {
                        if($currentModule->moduleID != $module->moduleID) {
                            ?> class="hide" <?php
                        }
                    } else if(isset($currentModule)) {
                        $nonUserModule = true;
                        if($currentModule->moduleID != $module->moduleID) {
                            ?> class="hide" <?php
                        }
                    } else if(!$isFirst) {
                        ?> class="hide" <?php
                    }
                    $isFirst = false;
                    
                    
                    ?> ><?= $module->moduleName ?></h2>
                <?php $numberOfPages = count($module->modulePage); ?>
                    <?php for ($i = 0; $i < $numberOfPages; $i++) : ?>
                        <a href="module.php?moduleID=<?= $module->moduleID ?>&amp;modulePage=<?= $module->modulePage[$i] ?>"><?= $module->modulePage[$i] ?></a>
                    <?php endfor ?>
                <?php
                
                $currentID = $module->moduleID;
    
                //Check whether to show lecturer page Module Files for adding and removing files.
                $sql = 'SELECT permission FROM userModule WHERE kNumber = :kNumber AND moduleID = :moduleID';
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':moduleID', $currentID);
                $stmt->bindParam(':kNumber', $_SESSION['username']);
                $stmt->execute();

                if($stmt->fetchColumn() == 1){
                    $showPage = true;
                } else {
                    $showPage = false;
                }
                ?>
                <?php if($showPage) : ?>
                    <a href="moduleFile.php?moduleID=<?= $module->moduleID ?>">Module Files</a>
                <?php endif ?>
            </section>
<?php endforeach ?>

<?php if($nonUserModule) : ?>
    <section>
        <h2><?= $currentModule->moduleName ?></h2>
    <?php $numberOfPages = count($currentModule->modulePage); ?>
    <?php for ($i = 0; $i < $numberOfPages; $i++) : ?>
        <a href="module.php?moduleID=<?= $currentModule->moduleID ?>&amp;modulePage=<?= $currentModule->modulePage[$i] ?>"><?= $currentModule->modulePage[$i] ?></a>
    <?php endfor ?>
    </section>
<?php endif ?>