<?php

class Announcement {
    private $announcementID;
    private $title;
    private $content;
    private $datePosted;
    
    public function __get($name) {
        return $this->$name;
    }
}