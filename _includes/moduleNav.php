<?php $isFirst = true; ?>
<?php foreach($modules as $module) : ?>
            <section>
                <h2 <?php 
                    
                    if (isset($currentModule)) {
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