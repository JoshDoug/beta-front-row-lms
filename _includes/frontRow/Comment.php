<?php

class Comment {
    private $commentID;
    private $postID;
    private $kNumber;
    private $commentText;
    private $dateTimeCommented;
    
    public function __get($name) {
        return $this->$name;
    }
}