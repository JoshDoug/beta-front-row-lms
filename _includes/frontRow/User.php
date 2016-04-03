<?php

class User {
    private $kNumber;
    private $pwd = '';
    private $fName;
    private $lName;
    private $kMail;
    private $modules;
    
    public function __get($name) {
        return $this->$name;
    }
    
    public function setModules($db){
        $stmt = $db->prepare('SELECT module.moduleID, module.moduleName
            FROM module, userModule
            WHERE module.moduleID=userModule.moduleID AND userModule.kNumber=:kNumber');
        $stmt->bindParam(':kNumber', $_SESSION['username']);
        $stmt->execute();
        $this->modules = $stmt->fetchAll(PDO::FETCH_CLASS, 'Module');
    }
}