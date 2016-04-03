<?php

class Module {
    private $moduleID;
    private $moduleName;
    private $moduleDescription;
    private $modulePage;
    
    public function __get($name) {
        return $this->$name;
    }
    
//    public function setModulePage(&$modulePageArray) {
//        $modulePage = $modulePageArray;
//    }
    
    public function setModulePage($db){
            $stmt = $db->prepare('SELECT modulePage.pageName
            FROM modulePage
            WHERE modulePage.moduleID=:moduleID');
//            $modID = $module->moduleID;
            $stmt->bindParam(':moduleID', $this->moduleID);
            $stmt->execute();
            $this->modulePage = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
//            $this->modulePage = $stmt->fetch(PDO::FETCH_NUM);
    }
}