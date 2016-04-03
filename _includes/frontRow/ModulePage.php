<?php

class ModulePage {
    private $pageID;
    private $moduleID;
    private $pageName;
    private $posts;
    
    public function __get($name) {
        return $this->$name;
    }
    
    public function getPosts($db){
        $stmt = $db->prepare('SELECT postID, title, content, commentsAllowed, dateTimePosted
                            FROM post
                            WHERE pageID=:pageID');
        $stmt->bindParam(':pageID', $this->pageID);
        $stmt->execute();
        $this->currentPage = $stmt->fetchObject('Post');
    }
}