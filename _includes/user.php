<?php

class User {
    private $kNumber;
    private $pwd = '';
    private $fName;
    private $lName;
    private $kMail;
    
//    public setUser($db) {
//        
//    }
    
    public function __get($name) {
        return $this->$name;
    }
}