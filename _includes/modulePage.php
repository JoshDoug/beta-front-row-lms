<?php

class ModulePage {
    private $pageID;
    private $moduleID;
    private $pageName;
    
    public function __get($name) {
        return $this->$name;
    }
}