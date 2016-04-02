<?php foreach($modules as $module) : ?>
            <section>
                <h2><?= $module->moduleName ?></h2>
                <?php $numberOfPages = count($module->modulePage); ?>
                    <?php for ($i = 0; $i < $numberOfPages; $i++) : ?>
                         <a href="module.php?moduleID=<?= $module->moduleID ?>&amp;modulePage=<?= $module->modulePage[$i] ?>"><?= $module->modulePage[$i] ?></a>
                    <?php endfor ?>
            </section>
<?php endforeach ?>