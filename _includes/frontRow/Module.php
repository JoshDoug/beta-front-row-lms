<?php

class Module {
    private $moduleID;
    private $moduleName;
    private $moduleDescription;
    private $modulePage;
    private $currentPage;
    
    public function __get($name) {
        return $this->$name;
    }
    
    public function setModulePage($db){
        $stmt = $db->prepare('SELECT modulePage.pageName
                            FROM modulePage
                            WHERE moduleID=:moduleID');
        $stmt->bindParam(':moduleID', $this->moduleID);
        $stmt->execute();
        $this->modulePage = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }
    
    public function setCurrentPage($db, $pageName){
        $stmt = $db->prepare('SELECT pageID, moduleID, pageName
                            FROM modulePage
                            WHERE moduleID=:moduleID AND pageName=:pageName');
        $stmt->bindParam(':moduleID', $this->moduleID);
        $stmt->bindParam(':pageName', $pageName);
        $stmt->execute();
        $this->currentPage = $stmt->fetchObject('ModulePage');
    }
}